# E2E Testing

End-to-end tests use Playwright against a local WordPress Docker environment.

## Prerequisites

- Docker & Docker Compose
- Node.js (18+)
- Playwright browsers: `npx playwright install`

## Setup

1. Install dependencies:

```bash
npm install
```

2. Start Docker containers:

```bash
docker compose up -d
```

This starts:
- **MySQL 8.0** on internal network
- **WordPress** on `http://localhost:8080` with the plugin mounted at `wp-content/plugins/forms-for-campaign-monitor`

3. The Playwright global setup (`tests/e2e/global-setup.ts`) automatically handles:
   - Waiting for WordPress to be ready
   - Installing WordPress via WP-CLI (core install, plugin activation)
   - Creating a subscriber test user (`testsubscriber` / `testsubscriber`)
   - Saving auth states for admin and subscriber to `tests/e2e/.auth/`

## Running Tests

```bash
# Run all e2e tests (includes setup)
npm run test:e2e

# Run with UI mode
npm run test:e2e:ui

# Run only setup (useful for debugging)
npm run test:e2e:setup
```

## Test Accounts

| User           | Password       | Role          |
|----------------|----------------|---------------|
| admin          | admin          | Administrator |
| testsubscriber | testsubscriber | Subscriber    |

## Test Structure

- `global-setup.ts` — Docker check, WP install, auth state creation
- `authorization.spec.ts` — Verifies capability checks and nonce/CSRF protection

## Stopping

```bash
docker compose down        # Stop containers, keep data
docker compose down -v     # Stop containers and delete volumes
```
