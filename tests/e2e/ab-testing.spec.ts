import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

const WP_URL = 'http://localhost:8080';

test.describe('A/B Testing', () => {
  let testPageId: string;
  let formIdA: string;
  let formIdB: string;
  let abTestId: string;

  const wpCli = (cmd: string) =>
    execSync(
      `docker compose exec -T wordpress wp ${cmd} --allow-root`,
      { cwd: process.cwd(), encoding: 'utf-8' }
    ).trim();

  // Clean up stale e2e forms/AB tests from previous failed runs
  test.beforeEach(() => {
    wpCli(`eval '
      $forms = get_option("forms_for_campaign_monitor_forms");
      if ($forms) {
        foreach ($forms as $id => $form) {
          if (strpos($form->getName(), "e2e-ab-form") !== false) { unset($forms[$id]); }
        }
        update_option("forms_for_campaign_monitor_forms", $forms);
      }
      $tests = get_option("forms_for_campaign_monitor_ab_tests");
      if ($tests) {
        foreach ($tests as $id => $test) {
          if (strpos($test->getName(), "e2e-ab-test") !== false) { unset($tests[$id]); }
        }
        update_option("forms_for_campaign_monitor_ab_tests", $tests);
      }
    '`);
  });

  /**
   * Helper: create a Bar form (no page assignment) with CAPTCHA disabled.
   * Returns the formId.
   */
  async function createForm(page: import('@playwright/test').Page, name: string, header: string, type: string = 'bar'): Promise<string> {
    await page.goto('/wp-admin/admin.php?page=campaign_monitor_create_builder');

    await page.locator('#formName').waitFor({ state: 'visible', timeout: 60000 });
    await page.fill('#formName', name);
    await page.fill('#formHeader', header);

    // Select first available list (may take time for CM API to respond)
    const listDropdown = page.locator('#campaignMonitorListId');
    await listDropdown.waitFor({ state: 'visible', timeout: 60000 });
    const options = await listDropdown.locator('option').all();
    for (const option of options) {
      const value = await option.getAttribute('value');
      if (value && value !== '') {
        await listDropdown.selectOption(value);
        break;
      }
    }

    await page.selectOption('#formType', type);

    // Don't assign to any page — the A/B test will handle page assignment
    // Leave page dropdown at default (no page selected)

    await page.check('#isActiveEnabled');

    // Disable CAPTCHA
    await page.evaluate(() => {
      const el = document.getElementById('hasCaptchaOff') as HTMLInputElement;
      if (el) { el.checked = true; }
    });

    await page.click('#submitFormFormButton');
    await page.waitForURL(/page=campaign/, { timeout: 15000 });

    // Try to get form ID from redirect URL
    const currentUrl = page.url();
    const formIdMatch = currentUrl.match(/formId=([^&]+)/);
    if (formIdMatch) {
      return formIdMatch[1];
    }

    // Otherwise get it from the forms list
    await page.goto('/wp-admin/admin.php?page=campaign-monitor-for-wordpress');
    const trashLink = await page.locator('tr', { hasText: name }).first().locator('a.submitdelete').getAttribute('href');
    const match = trashLink?.match(/formId=([^&]+)/);
    return match ? match[1] : '';
  }

  test.afterEach(async ({ page }) => {
    // Delete A/B test
    if (abTestId) {
      await page.goto(`/wp-admin/admin.php?page=campaign_monitor_ab_testing_editing&testId=${abTestId}&action=delete`);
      await page.waitForTimeout(1000);
    }
    // Delete forms
    for (const fid of [formIdA, formIdB]) {
      if (fid) {
        await page.goto(`/wp-admin/admin.php?page=campaign_monitor_create_builder&formId=${fid}&action=delete`);
        await page.waitForTimeout(500);
      }
    }
    // Delete test page
    if (testPageId) {
      try { wpCli(`post delete ${testPageId} --force`); } catch { /* already deleted */ }
    }
  });

  test('create A/B test, verify impressions, then delete and verify form gone', async ({ page }) => {
    test.setTimeout(120000);
    // -------------------------------------------------------
    // 1. Create two forms (no page assignment)
    // -------------------------------------------------------
    formIdA = await createForm(page, 'e2e-ab-form-A', 'Form A Header', 'lightbox');
    expect(formIdA).toBeTruthy();

    formIdB = await createForm(page, 'e2e-ab-form-B', 'Form B Header', 'bar');
    expect(formIdB).toBeTruthy();

    // -------------------------------------------------------
    // 2. Create a test page
    // -------------------------------------------------------
    testPageId = wpCli('post create --post_type=page --post_title="e2e-ab-test-page" --post_status=publish --porcelain');
    expect(Number(testPageId)).toBeGreaterThan(0);

    // -------------------------------------------------------
    // 3. Create A/B test assigning both forms to the page
    // -------------------------------------------------------
    await page.goto('/wp-admin/admin.php?page=campaign_monitor_ab_testing_editing');

    await page.fill('#testTitle', 'e2e-ab-test');

    // Select form A as primary
    await page.selectOption('select[name="form_primary"]', { label: 'e2e-ab-form-A' });
    // Select form B as secondary
    await page.selectOption('select[name="form_secondary"]', { label: 'e2e-ab-form-B' });
    // Enable on test page
    await page.selectOption('select[name="enable_on"]', testPageId);

    await page.click('#btnSaveSettings');

    // Should redirect to A/B testing list with success notice
    await page.waitForURL(/page=campaign_monitor_ab_testing/, { timeout: 15000 });
    await expect(page.locator('a.row-title', { hasText: 'e2e-ab-test' }).first()).toBeVisible();

    // Grab A/B test ID from the trash link
    const abTrashLink = await page.locator('tr', { hasText: 'e2e-ab-test' }).first().locator('a.submitdelete').getAttribute('href');
    const abMatch = abTrashLink?.match(/testId=([^&]+)/);
    expect(abMatch).toBeTruthy();
    abTestId = abMatch![1];

    // -------------------------------------------------------
    // 4. Visit the page twice — first visit shows one form,
    //    second visit shows the other (cookie blocks re-showing)
    // -------------------------------------------------------
    const impressions: Record<string, number> = {};
    impressions[formIdA] = 0;
    impressions[formIdB] = 0;

    // Clear any stale cookie
    await page.context().clearCookies({ name: 'campaignMonitorViewedIds' });

    // First visit — one form will be randomly selected
    await page.goto(`/?page_id=${testPageId}`);
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(2000);

    let formUuid = await page.locator('#cmApp_signupForm').getAttribute('data-uuid');
    expect(formUuid).toBeTruthy();
    if (formUuid === formIdA) impressions[formIdA]++;
    else if (formUuid === formIdB) impressions[formIdB]++;

    // Second visit — same browser context, cookie blocks the first form,
    // so the other form must be shown
    await page.goto(`/?page_id=${testPageId}`);
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(2000);

    formUuid = await page.locator('#cmApp_signupForm').getAttribute('data-uuid');
    expect(formUuid).toBeTruthy();
    if (formUuid === formIdA) impressions[formIdA]++;
    else if (formUuid === formIdB) impressions[formIdB]++;

    // Both forms should have been shown exactly once
    expect(impressions[formIdA]).toBe(1);
    expect(impressions[formIdB]).toBe(1);

    const totalImpressions = 2;

    // -------------------------------------------------------
    // 5. Verify A/B test admin page shows correct stats
    // -------------------------------------------------------
    await page.goto('/wp-admin/admin.php?page=campaign_monitor_ab_testing');
    await page.waitForLoadState('domcontentloaded');

    const abRow = page.locator('tr', { hasText: 'e2e-ab-test' }).first();
    await expect(abRow).toBeVisible();

    // The row columns: Title | Status | Page | Form A | Impressions | Submissions | Rate | Form B | Impressions | Submissions | Rate
    const cells = abRow.locator('td');

    // Form A name
    const formACell = cells.nth(3);
    await expect(formACell).toContainText('e2e-ab-form-A');

    // Form A impressions
    const formAImpressions = cells.nth(4);
    const aImpText = await formAImpressions.textContent();
    expect(Number(aImpText?.trim())).toBe(impressions[formIdA]);

    // Form B name
    const formBCell = cells.nth(7);
    await expect(formBCell).toContainText('e2e-ab-form-B');

    // Form B impressions
    const formBImpressions = cells.nth(8);
    const bImpText = await formBImpressions.textContent();
    expect(Number(bImpText?.trim())).toBe(impressions[formIdB]);

    // -------------------------------------------------------
    // 6. Delete the A/B test via the UI
    // -------------------------------------------------------
    const deleteHref = await abRow.locator('a.submitdelete').getAttribute('href');
    expect(deleteHref).toBeTruthy();
    await page.goto(deleteHref!);

    // Delete handler outputs a JS redirect, wait for it to settle
    await page.waitForLoadState('networkidle', { timeout: 15000 });

    // Navigate explicitly to A/B testing list
    await page.goto('/wp-admin/admin.php?page=campaign_monitor_ab_testing');

    // Verify the test is gone from the list
    await expect(page.locator('a.row-title', { hasText: 'e2e-ab-test' })).not.toBeVisible();

    // Mark as already deleted so afterEach doesn't try again
    abTestId = '';

    // -------------------------------------------------------
    // 7. Verify the page no longer shows any form
    // -------------------------------------------------------
    await page.context().clearCookies({ name: 'campaignMonitorViewedIds' });

    await page.goto(`/?page_id=${testPageId}`);
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(2000);

    // No subscribe form should be rendered
    const formElement = page.locator('#cmApp_signupForm');
    await expect(formElement).toHaveCount(0);
  });
});
