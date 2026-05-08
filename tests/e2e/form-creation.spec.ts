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
      console.log('Page content:', await page.content());
      throw new Error(`Redirected away from form builder to: ${page.url()}`);
    }

    // Fill form name
    await page.locator('#formName').waitFor({ state: 'visible', timeout: 60000 });
    await page.fill('#formName', 'e2e-test-form');

    // Select first available client (triggers list dropdown to appear)
    const clientDropdown = page.locator('#campaignMonitorClientId');
    if (await clientDropdown.isVisible()) {
      const clientOptions = await clientDropdown.locator('option').all();
      for (const option of clientOptions) {
        const value = await option.getAttribute('value');
        if (value && value !== '') {
          await clientDropdown.selectOption(value);
          break;
        }
      }
    }

    // Select first available list from dropdown (first non-empty option)
    const listDropdown = page.locator('#campaignMonitorListId');
    await listDropdown.waitFor({ state: 'visible', timeout: 60000 });
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
      console.log('Page content:', await page.content());
      throw new Error(`Redirected away from form builder to: ${page.url()}`);
    }

    await page.locator('#formName').waitFor({ state: 'visible', timeout: 60000 });
    await page.fill('#formName', 'e2e-test-subscribe-form');

    // Select first available client (triggers list dropdown to appear)
    const clientDropdown2 = page.locator('#campaignMonitorClientId');
    if (await clientDropdown2.isVisible()) {
      const clientOptions2 = await clientDropdown2.locator('option').all();
      for (const option of clientOptions2) {
        const value = await option.getAttribute('value');
        if (value && value !== '') {
          await clientDropdown2.selectOption(value);
          break;
        }
      }
    }

    const listDropdown = page.locator('#campaignMonitorListId');
    await listDropdown.waitFor();
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
