import { test as setup, expect } from '@playwright/test';
import { execSync } from 'child_process';
import { mkdirSync } from 'fs';

const WP_URL = 'http://localhost:8080';
const ADMIN_USER = 'admin';
const ADMIN_PASS = 'admin';
const SUBSCRIBER_USER = 'testsubscriber';
const SUBSCRIBER_PASS = 'testsubscriber';

// CM OAuth credentials — loaded from .env file or environment variables
const CM_CLIENT_ID = process.env.CM_CLIENT_ID || '';
const CM_CLIENT_SECRET = process.env.CM_CLIENT_SECRET || '';
const CM_EMAIL = process.env.CM_EMAIL || '';
const CM_PASSWORD = process.env.CM_PASSWORD || '';

async function waitForWordPress(baseURL: string, maxRetries = 30) {
  for (let i = 0; i < maxRetries; i++) {
    try {
      const res = await fetch(baseURL);
      if (res.ok || res.status === 302) return;
    } catch {}
    await new Promise(r => setTimeout(r, 2000));
  }
  throw new Error('WordPress did not become available');
}

setup('start docker and configure WordPress', async ({ request }) => {
  setup.setTimeout(180_000);
  // 1. Ensure Docker containers are running
  try {
    execSync('docker compose ps --status running --format json | grep -q wordpress', {
      cwd: process.cwd(),
      stdio: 'pipe',
    });
    console.log('Docker containers already running');
  } catch {
    console.log('Starting Docker containers...');
    execSync('docker compose up -d', { cwd: process.cwd(), stdio: 'inherit' });
  }

  // 2. Wait for WordPress to be ready
  console.log('Waiting for WordPress...');
  await waitForWordPress(WP_URL);

  // 3. Install WordPress via WP-CLI in the container
  console.log('Installing WordPress...');
  const dockerExec = (cmd: string) =>
    execSync(`docker compose exec -T wordpress bash -c "${cmd}"`, {
      cwd: process.cwd(), stdio: 'inherit', timeout: 120_000,
    });

  // Install system deps and WP-CLI
  dockerExec(
    `apt-get update -qq && apt-get install -y -qq less > /dev/null 2>&1; ` +
    `test -f /usr/local/bin/wp || (curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp)`
  );

  // Install WP core (idempotent)
  dockerExec(
    `wp core is-installed --allow-root 2>/dev/null || ` +
    `wp core install --url='${WP_URL}' --title='Test Site' --admin_user='${ADMIN_USER}' --admin_password='${ADMIN_PASS}' --admin_email='admin@example.com' --skip-email --allow-root`
  );

  // Activate plugin (idempotent)
  dockerExec(`wp plugin activate forms-for-campaign-monitor --allow-root 2>/dev/null || true`);

  // Dismiss the plugin update/welcome screen (prevents redirect to update page)
  dockerExec(`wp option update forms_for_campaign_monitor_plugin_update 1 --allow-root`);

  // Create subscriber user (idempotent)
  dockerExec(
    `wp user get ${SUBSCRIBER_USER} --allow-root 2>/dev/null || ` +
    `wp user create ${SUBSCRIBER_USER} subscriber@example.com --role=subscriber --user_pass=${SUBSCRIBER_PASS} --allow-root`
  );

  // 4. Save admin auth state
  console.log('Saving admin auth state...');
  mkdirSync('./tests/e2e/.auth', { recursive: true });
  await request.get(`${WP_URL}/wp-login.php`);
  const loginResponse = await request.post(`${WP_URL}/wp-login.php`, {
    form: {
      log: ADMIN_USER,
      pwd: ADMIN_PASS,
      'wp-submit': 'Log In',
      redirect_to: `${WP_URL}/wp-admin/`,
      testcookie: '1',
    },
  });
  expect(loginResponse.ok() || loginResponse.status() === 302).toBeTruthy();

  await request.storageState({ path: './tests/e2e/.auth/admin.json' });

  // 5. Save subscriber auth state
  console.log('Saving subscriber auth state...');
  const subRequest = await (await import('@playwright/test')).request.newContext();
  await subRequest.get(`${WP_URL}/wp-login.php`);
  const subLogin = await subRequest.post(`${WP_URL}/wp-login.php`, {
    form: {
      log: SUBSCRIBER_USER,
      pwd: SUBSCRIBER_PASS,
      'wp-submit': 'Log In',
      redirect_to: `${WP_URL}/wp-admin/`,
      testcookie: '1',
    },
  });
  expect(subLogin.ok() || subLogin.status() === 302).toBeTruthy();
  await subRequest.storageState({ path: './tests/e2e/.auth/subscriber.json' });
  await subRequest.dispose();

  // 6. Connect Campaign Monitor if not already connected
  console.log('Checking Campaign Monitor connection...');
  const wpCli = (cmd: string) =>
    execSync(`docker compose exec -T wordpress wp ${cmd} --allow-root`, {
      cwd: process.cwd(),
      encoding: 'utf-8',
    }).trim();

  const isConnected = (() => {
    try {
      const val = wpCli('option get forms_for_campaign_monitor_connected');
      return val === '1';
    } catch {
      return false;
    }
  })();

  if (!isConnected) {
    if (!CM_CLIENT_ID || !CM_CLIENT_SECRET || !CM_EMAIL || !CM_PASSWORD) {
      console.log('⚠ Skipping CM connection: CM_CLIENT_ID, CM_CLIENT_SECRET, CM_EMAIL, and CM_PASSWORD must be set');
      console.log('Setup complete! (without CM connection)');
      return;
    }
    console.log('Connecting to Campaign Monitor via OAuth...');
    const { chromium } = await import('@playwright/test');
    const browser = await chromium.launch();
    const context = await browser.newContext({
      storageState: './tests/e2e/.auth/admin.json',
    });
    const page = await context.newPage();

    // Navigate to settings page, fill credentials if needed, and submit
    await page.goto(`${WP_URL}/wp-admin/admin.php?page=campaign_monitor_settings_page`);
    const existingClientId = await page.locator('#client_id').inputValue();
    const existingClientSecret = await page.locator('#client_secret').inputValue();
    if (!existingClientId) await page.fill('#client_id', CM_CLIENT_ID);
    if (!existingClientSecret) await page.fill('#client_secret', CM_CLIENT_SECRET);
    await page.click('#btnSaveSettings');

    // Wait for redirect to CM OAuth login page
    await page.waitForURL(/api\.createsend\.com\/oauth/, { timeout: 30000 });
    console.log('On CM OAuth login page');

    // Fill CM credentials and login
    await page.getByLabel('Email').fill(CM_EMAIL);
    await page.getByLabel('Password').fill(CM_PASSWORD);
    await page.getByRole('button', { name: 'Continue' }).click();

    // Wait for consent page
    const allowBtn = page.getByRole('button', { name: 'Allow access' });
    await allowBtn.waitFor({ state: 'visible', timeout: 30000 });
    console.log('On CM consent page');

    // Capture the auth code from the redirect response
    // (headless Chrome blocks HTTPS→HTTP redirect from CM to localhost)
    let authCode = '';
    page.on('response', (response) => {
      const location = response.headers()['location'] || '';
      if (location.includes('localhost:8080') && location.includes('code=')) {
        try {
          const url = new URL(location);
          authCode = url.searchParams.get('code') || '';
        } catch {}
      }
    });

    await allowBtn.click();
    await page.waitForTimeout(10000);

    await page.unrouteAll({ behavior: 'ignoreErrors' }).catch(() => {});
    await context.close();
    await browser.close();

    if (!authCode) {
      throw new Error('Failed to capture OAuth auth code from CM redirect');
    }
    console.log('Captured auth code, exchanging for tokens...');

    // Exchange the code for tokens inside the WordPress container
    const exchangeResult = wpCli(`eval '
      \$settings = get_option("forms_for_campaign_monitor_campaign_monitor_forms_account_settings");
      \$clientId = \$settings["client_id"];
      \$clientSecret = \$settings["client_secret"];
      \$code = "${authCode}";
      \$redirectUri = "${WP_URL}/wp-admin/admin.php?page=campaign-monitor-for-wordpress&connected=true";
      \$params = array(
        "grant_type" => "authorization_code",
        "client_id" => \$clientId,
        "client_secret" => \$clientSecret,
        "code" => \$code,
        "redirect_uri" => \$redirectUri
      );
      \$response = wp_remote_post("https://api.createsend.com/oauth/token", array(
        "body" => http_build_query(\$params),
        "timeout" => 30,
        "headers" => array("Content-Type" => "application/x-www-form-urlencoded")
      ));
      if (is_wp_error(\$response)) {
        echo "ERROR:" . \$response->get_error_message();
      } else {
        \$body = wp_remote_retrieve_body(\$response);
        \$creds = json_decode(\$body);
        if (isset(\$creds->access_token)) {
          \$settings["access_token"] = \$creds->access_token;
          \$settings["refresh_token"] = \$creds->refresh_token;
          \$settings["expiry"] = time() + \$creds->expires_in;
          update_option("forms_for_campaign_monitor_campaign_monitor_forms_account_settings", \$settings);
          update_option("forms_for_campaign_monitor_connected", 1);
          echo "OK";
        } else {
          echo "ERROR:" . \$body;
        }
      }
    '`);
    if (!exchangeResult.startsWith('OK')) {
      throw new Error(`Token exchange failed: ${exchangeResult}`);
    }
    console.log('Campaign Monitor connected successfully!');
  } else {
    console.log('Campaign Monitor already connected.');
  }

  // Fetch CM clients directly via WP-CLI (more reliable than browser visit in CI)
  console.log('Fetching Campaign Monitor clients via WP-CLI...');
  const fetchClientsResult = wpCli(`eval '
    \$settings = get_option("forms_for_campaign_monitor_campaign_monitor_forms_account_settings");
    if (empty(\$settings) || empty(\$settings["access_token"])) {
      echo "ERROR:no_token";
      return;
    }
    \$accessToken = \$settings["access_token"];

    // Call CM API directly
    \$response = wp_remote_get("https://api.createsend.com/api/v3.3/clients.json", array(
      "timeout" => 30,
      "headers" => array(
        "Authorization" => "Bearer " . \$accessToken,
      ),
    ));

    if (is_wp_error(\$response)) {
      echo "ERROR:wp_error:" . \$response->get_error_message();
      return;
    }

    \$code = wp_remote_retrieve_response_code(\$response);
    \$body = wp_remote_retrieve_body(\$response);

    if (\$code === 401) {
      // Token might be expired, try refreshing
      echo "EXPIRED:" . \$body;
      return;
    }

    if (\$code !== 200) {
      echo "ERROR:http_" . \$code . ":" . \$body;
      return;
    }

    \$clients = json_decode(\$body);
    if (empty(\$clients)) {
      echo "ERROR:empty_clients:" . \$body;
      return;
    }

    // Store clients in the settings (same as generateConnectPage does)
    \$settings["campaign_monitor_clients"] = \$clients;
    update_option("forms_for_campaign_monitor_campaign_monitor_forms_account_settings", \$settings);

    // If only one client, set it as default
    if (count(\$clients) === 1 && !empty(\$clients[0]->ClientID)) {
      \$settings["default_client"] = \$clients[0]->ClientID;
      update_option("forms_for_campaign_monitor_campaign_monitor_forms_account_settings", \$settings);
    }

    echo "OK:" . count(\$clients) . " clients";
  '`);
  console.log('Fetch clients result:', fetchClientsResult);

  if (fetchClientsResult.startsWith('EXPIRED')) {
    // Try token refresh
    console.log('Token expired, attempting refresh...');
    const refreshResult = wpCli(`eval '
      \$settings = get_option("forms_for_campaign_monitor_campaign_monitor_forms_account_settings");
      \$params = array(
        "grant_type" => "refresh_token",
        "refresh_token" => \$settings["refresh_token"],
      );
      \$response = wp_remote_post("https://api.createsend.com/oauth/token", array(
        "body" => http_build_query(\$params),
        "timeout" => 30,
        "headers" => array("Content-Type" => "application/x-www-form-urlencoded"),
      ));
      if (is_wp_error(\$response)) {
        echo "ERROR:" . \$response->get_error_message();
        return;
      }
      \$body = wp_remote_retrieve_body(\$response);
      \$creds = json_decode(\$body);
      if (isset(\$creds->access_token)) {
        \$settings["access_token"] = \$creds->access_token;
        \$settings["refresh_token"] = \$creds->refresh_token;
        \$settings["expiry"] = time() + \$creds->expires_in;
        update_option("forms_for_campaign_monitor_campaign_monitor_forms_account_settings", \$settings);
        echo "OK";
      } else {
        echo "ERROR:" . \$body;
      }
    '`);
    console.log('Token refresh result:', refreshResult);
    if (!refreshResult.startsWith('OK')) {
      throw new Error(`Token refresh failed: ${refreshResult}`);
    }

    // Retry fetching clients with new token
    const retryResult = wpCli(`eval '
      \$settings = get_option("forms_for_campaign_monitor_campaign_monitor_forms_account_settings");
      \$response = wp_remote_get("https://api.createsend.com/api/v3.3/clients.json", array(
        "timeout" => 30,
        "headers" => array("Authorization" => "Bearer " . \$settings["access_token"]),
      ));
      if (is_wp_error(\$response)) { echo "ERROR:" . \$response->get_error_message(); return; }
      \$body = wp_remote_retrieve_body(\$response);
      \$clients = json_decode(\$body);
      if (empty(\$clients)) { echo "ERROR:empty:" . \$body; return; }
      \$settings["campaign_monitor_clients"] = \$clients;
      if (count(\$clients) === 1 && !empty(\$clients[0]->ClientID)) {
        \$settings["default_client"] = \$clients[0]->ClientID;
      }
      update_option("forms_for_campaign_monitor_campaign_monitor_forms_account_settings", \$settings);
      echo "OK:" . count(\$clients) . " clients";
    '`);
    console.log('Retry fetch clients result:', retryResult);
    if (!retryResult.startsWith('OK')) {
      throw new Error(`Failed to fetch clients after token refresh: ${retryResult}`);
    }
  } else if (!fetchClientsResult.startsWith('OK')) {
    throw new Error(`Failed to fetch CM clients: ${fetchClientsResult}`);
  }

  console.log('Setup complete!');
});
