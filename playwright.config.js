import { defineConfig, devices } from '@playwright/test';

/**
 * Read environment variables from file.
 * https://github.com/motdotla/dotenv
 */
// import dotenv from 'dotenv';
// import path from 'path';
// dotenv.config({ path: path.resolve(__dirname, '.env') });

/**
 * See https://playwright.dev/docs/test-configuration.
 */
export default defineConfig({
	testDir: './tests/playwright',
	fullyParallel: true,
	/* Fail the build on CI if you accidentally left test.only in the source code. */
	forbidOnly: !!process.env.CI,
	/* Retry on CI only */
	retries: process.env.CI ? 2 : 0,
	/* Opt out of parallel tests on CI. */
	workers: process.env.CI ? 1 : undefined,
	/* Reporter to use. See https://playwright.dev/docs/test-reporters */
	reporter: 'html',
	/* Shared settings for all the projects below. See https://playwright.dev/docs/api/class-testoptions. */
	use: {
		/* Base URL to use in actions like `await page.goto('/')`. */
		baseURL: process.env.BASE_URL || 'http://localhost:8891',
		browserName: process.env.BROWSER || 'chromium', // Usa la variable de entorno BROWSER o 'chromium'
		headless: process.env.HEADLESS !== 'false', // Usa la variable HEADLESS (true por defecto)
		/* Collect trace when retrying the failed test. See https://playwright.dev/docs/trace-viewer */
		trace: 'on-first-retry',
		viewport: { width: 1280, height: 800 },
		timeout: 60000,
		// screenshot: 'only-on-failure',
		ignoreHTTPSErrors: true,
	},
	webServer: {
		command: 'wp-env start',
		port: 8891,
		reuseExistingServer: true,
		timeout: 120000,
	},

	/* Configure projects for major browsers */
	projects: [
		{
			name: 'chromium',
			use: { ...devices['Desktop Chrome'] },
		},
	],
	/* Run your local dev server before starting the tests */
	// webServer: {
	//   command: 'npm run up',
	//   url: 'http://localhost:8889',
	//   reuseExistingServer: !process.env.CI,
	// },
});
