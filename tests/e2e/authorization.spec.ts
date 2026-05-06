import { test, expect, request as playwrightRequest } from '@playwright/test';

const WP_URL = 'http://localhost:8080';

test.describe('Authorization - Admin (has manage_options)', () => {
  // Uses admin auth state from config default

  test('admin can access plugin settings page', async ({ page }) => {
    await page.goto('/wp-admin/admin.php?page=campaign-monitor-for-wordpress');
    // Should not get a 403 or "Unauthorized"
    const content = await page.content();
    expect(content).not.toContain('Unauthorized');
    expect(page.url()).toContain('campaign_monitor');
  });

  test('admin can reach ajax_handler endpoint', async ({ page, request }) => {
    // Get a valid nonce from the admin page
    await page.goto('/wp-admin/admin.php?page=campaign-monitor-for-wordpress');
    const nonce = await page.evaluate(() => (window as any).ajax_request?.nonce);
    expect(nonce).toBeTruthy();

    const response = await request.post(`${WP_URL}/wp-admin/admin-ajax.php`, {
      form: {
        action: 'handle_ajax_cm_forms',
        nonce: nonce,
        type: 'getLists',
        clientId: 'test',
      },
    });
    // Admin should not get 403
    expect(response.status()).not.toBe(403);
  });

  test('admin can reach handleRequest endpoint', async ({ request }) => {
    const response = await request.post(`${WP_URL}/wp-admin/admin-post.php`, {
      form: {
        action: 'handle_cm_form_request',
        'data[type]': 'save_settings',
        'data[app_nonce]': 'fake',
      },
    });
    // Admin should not get 403 - may get other errors but not auth blocked
    const body = await response.text();
    expect(body).not.toContain('Unauthorized');
  });

  test('admin ajax_handler rejects request without valid nonce (CSRF protection)', async ({ request }) => {
    const response = await request.post(`${WP_URL}/wp-admin/admin-ajax.php`, {
      form: {
        action: 'handle_ajax_cm_forms',
        nonce: 'invalid_nonce',
        type: 'getLists',
        clientId: 'test',
      },
    });
    // Should be rejected due to invalid nonce
    expect(response.status()).toBe(403);
  });
});

test.describe('Authorization - Subscriber (no manage_options)', () => {
  test.use({ storageState: './tests/e2e/.auth/subscriber.json' });

  test('subscriber is blocked from ajax_handler', async ({ request }) => {
    const response = await request.post(`${WP_URL}/wp-admin/admin-ajax.php`, {
      form: {
        action: 'handle_ajax_cm_forms',
        type: 'getLists',
        clientId: 'test',
      },
    });
    expect(response.status()).toBe(403);
    const body = await response.text();
    expect(body).toContain('Unauthorized');
  });

  test('subscriber is blocked from handleRequest', async ({ request }) => {
    const response = await request.post(`${WP_URL}/wp-admin/admin-post.php`, {
      form: {
        action: 'handle_cm_form_request',
        'data[type]': 'account_disconnect',
        'data[app_nonce]': 'fake',
      },
    });
    expect(response.status()).toBe(403);
    const body = await response.text();
    expect(body).toContain('Unauthorized');
  });

  test('subscriber is blocked from save_settings', async ({ request }) => {
    const response = await request.post(`${WP_URL}/wp-admin/admin-post.php`, {
      form: {
        action: 'handle_cm_form_request',
        'data[type]': 'save_settings',
        'data[app_nonce]': 'fake',
        recaptcha_key: 'attacker_key',
      },
    });
    expect(response.status()).toBe(403);
    const body = await response.text();
    expect(body).toContain('Unauthorized');
  });

  test('subscriber is blocked from save_ab_test', async ({ request }) => {
    const response = await request.post(`${WP_URL}/wp-admin/admin-post.php`, {
      form: {
        action: 'handle_cm_form_request',
        'data[type]': 'save_ab_test',
        'data[app_nonce]': 'fake',
        test_title: 'HackedTest',
      },
    });
    expect(response.status()).toBe(403);
    const body = await response.text();
    expect(body).toContain('Unauthorized');
  });

  test('subscriber cannot access plugin admin page', async ({ page }) => {
    const response = await page.goto('/wp-admin/admin.php?page=campaign-monitor-for-wordpress');
    // WordPress menu uses 'administrator' capability, so subscriber can't access
    const content = await page.content();
    // Should either redirect or show insufficient permissions
    expect(
      page.url().includes('wp-login.php') ||
      content.includes('You do not have sufficient permissions') ||
      content.includes('Sorry, you are not allowed')
    ).toBeTruthy();
  });
});

test.describe('Authorization - Unauthenticated', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('unauthenticated user is blocked from ajax_handler', async ({ request }) => {
    const response = await request.post(`${WP_URL}/wp-admin/admin-ajax.php`, {
      form: {
        action: 'handle_ajax_cm_forms',
        type: 'getLists',
        clientId: 'test',
      },
    });
    // WordPress itself blocks unauthenticated wp_ajax_ requests
    const body = await response.text();
    expect(body).toBe('0');
  });

  test('unauthenticated user is blocked from admin-post', async ({ request }) => {
    const response = await request.post(`${WP_URL}/wp-admin/admin-post.php`, {
      form: {
        action: 'handle_cm_form_request',
        'data[type]': 'save_settings',
        'data[app_nonce]': 'fake',
      },
      maxRedirects: 0,
    });
    // WordPress blocks unauthenticated users (302 redirect to login or 400)
    expect([302, 400]).toContain(response.status());
  });
});
