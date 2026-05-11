import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

const WP_URL = 'http://localhost:8080';

test.describe('Form Creation and Rendering', () => {
  let testPageId: string;
  let testFormId: string;

  const wpCli = (cmd: string) =>
    execSync(
      `docker compose exec -T wordpress wp ${cmd} --allow-root`,
      { cwd: process.cwd(), encoding: 'utf-8' }
    ).trim();

  test.afterEach(async ({ page }) => {
    // Delete the form via the plugin's delete URL
    if (testFormId) {
      await page.goto(`/wp-admin/admin.php?page=campaign_monitor_create_builder&formId=${testFormId}&action=delete`);
      await page.waitForTimeout(1000);
    }

    // Delete the test page via WP-CLI
    if (testPageId) {
      try { wpCli(`post delete ${testPageId} --force`); } catch { /* already deleted */ }
    }
  });

  test('create a page, create a form on it, and verify frontend rendering', async ({ page }) => {
    // 1. Create a new page via WP-CLI
    testPageId = wpCli('post create --post_type=page --post_title="e2e-test-form-page" --post_name="e2e-test-form-page" --post_status=publish --porcelain');
    expect(Number(testPageId)).toBeGreaterThan(0);

    // 2. Create a new form assigned to this page
    await page.goto('/wp-admin/admin.php?page=campaign_monitor_create_builder');
    await page.waitForLoadState('networkidle');

    // Debug: log where we actually landed
    console.log('Form builder URL:', page.url());
    if (!page.url().includes('campaign_monitor_create_builder')) {
      console.log('Redirected to:', page.url());
      throw new Error(`Redirected away from form builder to: ${page.url()}`);
    }

    // Fill form name
    await page.locator('#formName').waitFor({ state: 'visible', timeout: 60000 });
    await page.fill('#formName', 'e2e-test-form');

    // Monitor all admin-ajax.php responses for debugging
    page.on('response', async (resp) => {
      if (resp.url().includes('admin-ajax.php')) {
        const postData = resp.request().postData() || '';
        try { console.log(`AJAX response: status=${resp.status()}, postData=${postData.substring(0, 100)}`); } catch {}
      }
    });

    // Select first available client (triggers list dropdown to appear)
    const clientDropdown = page.locator('#campaignMonitorClientId');
    console.log('Client dropdown visible:', await clientDropdown.isVisible());
    if (await clientDropdown.isVisible()) {
      const preOptions = await clientDropdown.locator('option').allTextContents();
      console.log('Client dropdown options before wait:', JSON.stringify(preOptions));

      console.log('Waiting for non-empty client option...');
      await clientDropdown.locator('option:not([value=""])').first().waitFor({ state: 'attached', timeout: 60000 });
      console.log('Client options ready');
      const clientOptions = await clientDropdown.locator('option').all();
      for (const option of clientOptions) {
        const value = await option.getAttribute('value');
        if (value && value !== '') {
          console.log('Selecting client:', value);
          await clientDropdown.selectOption(value);
          // Trigger jQuery change event to ensure delegated handler fires populateListDropdown()
          console.log('Dispatching jQuery change event...');
          await page.evaluate(() => {
            const $v = (window as any).jQuery || (window as any).$campaignMonitor;
            if ($v) { $v('#campaignMonitorClientId').trigger('change'); }
          });
          break;
        }
      }
    } else {
      console.log('Client dropdown NOT visible!');
    }

    // Wait for list dropdown to be populated by AJAX
    const listDropdown = page.locator('#campaignMonitorListId');
    console.log('Waiting for list dropdown options...');
    try {
      await listDropdown.locator('option:not([value=""])').first().waitFor({ state: 'attached', timeout: 15000 });
    } catch {
      // First attempt failed — retry by calling populateListDropdown() directly
      console.log('First attempt failed, retrying with direct populateListDropdown() call...');
      await page.evaluate(() => { (window as any).populateListDropdown(); });
      try {
        await listDropdown.locator('option:not([value=""])').first().waitFor({ state: 'attached', timeout: 15000 });
      } catch {
        const listOptions = await listDropdown.locator('option').allTextContents();
        console.log('List dropdown options at timeout:', JSON.stringify(listOptions));
        const listHtml = await listDropdown.evaluate(el => el.outerHTML);
        console.log('List dropdown HTML:', listHtml);
        throw new Error('List dropdown never populated with options after selecting client');
      }
    }
    const listOptions = await listDropdown.locator('option').allTextContents();
    console.log('List dropdown populated:', JSON.stringify(listOptions));
    const options = await listDropdown.locator('option').all();
    let selectedList = false;
    for (const option of options) {
      const value = await option.getAttribute('value');
      if (value && value !== '') {
        await listDropdown.selectOption(value);
        selectedList = true;
        break;
      }
    }
    expect(selectedList).toBe(true);

    // Set form type to "Bar" (auto-injects without shortcode)
    await page.selectOption('#formType', 'bar');

    // Select our test page
    await page.selectOption('#formPageOn_1', testPageId);

    // Ensure form is enabled
    await page.check('#isActiveEnabled');

    // Submit the form
    await page.click('#submitFormFormButton');

    // Verify redirect — saved form redirects to the builder with formId or to the list
    await page.waitForURL(/page=campaign/, { timeout: 15000 });

    // Grab the form ID from the URL or from the forms list
    const currentUrl = page.url();
    const formIdMatch = currentUrl.match(/formId=([^&]+)/);
    if (formIdMatch) {
      testFormId = formIdMatch[1];
    }

    // Confirm the form exists in the list
    await page.goto('/wp-admin/admin.php?page=campaign-monitor-for-wordpress');
    const formsList = await page.content();
    expect(formsList).toContain('e2e-test-form');

    // If we didn't get formId from redirect, get it from the trash link
    if (!testFormId) {
      const trashLink = await page.locator('tr', { hasText: 'e2e-test-form' }).first().locator('a.submitdelete').getAttribute('href');
      const match = trashLink?.match(/formId=([^&]+)/);
      if (match) testFormId = match[1];
    }

    // 3. Verify the form renders on the frontend
    await page.goto(`/?page_id=${testPageId}`);
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(2000);

    // The form should have a SUBSCRIBE button and email input
    const subscribeBtn = page.locator('button:has-text("SUBSCRIBE"), input[value="SUBSCRIBE"]');
    await expect(subscribeBtn.first()).toBeVisible({ timeout: 10000 });
  });

  test('subscribe via a form and verify success message', async ({ page }) => {
    // 1. Create a new page via WP-CLI
    testPageId = wpCli('post create --post_type=page --post_title="e2e-test-subscribe-page" --post_name="e2e-test-subscribe-page" --post_status=publish --porcelain');
    expect(Number(testPageId)).toBeGreaterThan(0);

    // 2. Create a new Bar form assigned to this page
    await page.goto('/wp-admin/admin.php?page=campaign_monitor_create_builder');
    await page.waitForLoadState('networkidle');

    console.log('Form builder URL:', page.url());
    if (!page.url().includes('campaign_monitor_create_builder')) {
      console.log('Redirected to:', page.url());
      throw new Error(`Redirected away from form builder to: ${page.url()}`);
    }

    await page.locator('#formName').waitFor({ state: 'visible', timeout: 60000 });
    await page.fill('#formName', 'e2e-test-subscribe-form');

    // Monitor all admin-ajax.php responses for debugging
    page.on('response', async (resp) => {
      if (resp.url().includes('admin-ajax.php')) {
        const postData = resp.request().postData() || '';
        try { console.log(`AJAX response (test 2): status=${resp.status()}, postData=${postData.substring(0, 100)}`); } catch {}
      }
    });

    // Select first available client (triggers list dropdown to appear)
    const clientDropdown2 = page.locator('#campaignMonitorClientId');
    console.log('Client dropdown2 visible:', await clientDropdown2.isVisible());
    if (await clientDropdown2.isVisible()) {
      const preOptions2 = await clientDropdown2.locator('option').allTextContents();
      console.log('Client dropdown2 options before wait:', JSON.stringify(preOptions2));

      console.log('Waiting for non-empty client option (test 2)...');
      await clientDropdown2.locator('option:not([value=""])').first().waitFor({ state: 'attached', timeout: 60000 });
      console.log('Client options ready (test 2)');
      const clientOptions2 = await clientDropdown2.locator('option').all();
      for (const option of clientOptions2) {
        const value = await option.getAttribute('value');
        if (value && value !== '') {
          console.log('Selecting client (test 2):', value);
          await clientDropdown2.selectOption(value);
          console.log('Dispatching jQuery change event (test 2)...');
          await page.evaluate(() => {
            const $v = (window as any).jQuery || (window as any).$campaignMonitor;
            if ($v) { $v('#campaignMonitorClientId').trigger('change'); }
          });
          break;
        }
      }
    } else {
      console.log('Client dropdown2 NOT visible!');
    }

    // Wait for list dropdown to be populated by AJAX
    const listDropdown = page.locator('#campaignMonitorListId');
    console.log('Waiting for list dropdown options (test 2)...');
    try {
      await listDropdown.locator('option:not([value=""])').first().waitFor({ state: 'attached', timeout: 15000 });
    } catch {
      console.log('First attempt failed (test 2), retrying with direct populateListDropdown() call...');
      await page.evaluate(() => { (window as any).populateListDropdown(); });
      try {
        await listDropdown.locator('option:not([value=""])').first().waitFor({ state: 'attached', timeout: 15000 });
      } catch {
        const listOptions2 = await listDropdown.locator('option').allTextContents();
        console.log('List dropdown options at timeout (test 2):', JSON.stringify(listOptions2));
        throw new Error('List dropdown never populated with options after selecting client (test 2)');
      }
    }
    const listOpts = await listDropdown.locator('option').allTextContents();
    console.log('List dropdown populated (test 2):', JSON.stringify(listOpts));
    const options = await listDropdown.locator('option').all();
    let selectedList = false;
    for (const option of options) {
      const value = await option.getAttribute('value');
      if (value && value !== '') {
        await listDropdown.selectOption(value);
        selectedList = true;
        break;
      }
    }
    expect(selectedList).toBe(true);

    await page.selectOption('#formType', 'bar');
    await page.selectOption('#formPageOn_1', testPageId);
    await page.check('#isActiveEnabled');
    // Disable CAPTCHA so the subscribe test can submit without it
    await page.evaluate(() => {
      const el = document.getElementById('hasCaptchaOff') as HTMLInputElement;
      if (el) { el.checked = true; }
    });
    await page.click('#submitFormFormButton');
    await page.waitForURL(/page=campaign/, { timeout: 15000 });

    // Grab form ID
    const currentUrl = page.url();
    const formIdMatch = currentUrl.match(/formId=([^&]+)/);
    if (formIdMatch) {
      testFormId = formIdMatch[1];
    }
    if (!testFormId) {
      await page.goto('/wp-admin/admin.php?page=campaign-monitor-for-wordpress');
      const trashLink = await page.locator('tr', { hasText: 'e2e-test-subscribe-form' }).first().locator('a.submitdelete').getAttribute('href');
      const match = trashLink?.match(/formId=([^&]+)/);
      if (match) testFormId = match[1];
    }

    // 3. Navigate to the frontend page
    await page.goto(`/?page_id=${testPageId}`);
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(2000);

    // 4. Fill in email and submit
    const emailInput = page.locator('#cmApp_signupEmail');
    await emailInput.waitFor({ state: 'visible', timeout: 10000 });

    const testEmail = `e2e-test-${Date.now()}@example.com`;
    await emailInput.fill(testEmail);

    const submitBtn = page.locator('.cmApp_formSubmitButton');
    await submitBtn.click();

    // 5. Verify the success response
    const thankYouCheck = page.locator('#cmApp_thankYouCheck');
    await expect(thankYouCheck).toBeVisible({ timeout: 15000 });

    const successMsg = page.locator('.cmApp_processingMsg');
    await expect(successMsg).toBeVisible({ timeout: 5000 });
  });
});
