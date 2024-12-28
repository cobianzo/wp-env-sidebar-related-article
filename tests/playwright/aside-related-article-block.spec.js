const { test, expect } = require('@wordpress/e2e-test-utils-playwright');
const { createRelatedArticleWithTerms, openCategoriesPanelIfClosed } = require('./utils');
import path from 'path';

test.describe('Aside Related Article Block - E2E Tests', () => {
	test.beforeEach(async ({ page, requestUtils }) => {
		console.log('>>>>>>> START TEST - beforeEach');
		// http://localhost:8890
		// login
		await page.goto('/wp-login.php');
		await page.fill('input[name="log"]', 'admin');
		await page.fill('input[name="pwd"]', 'password');
		await page.click('input[type="submit"]');
		// first test
		await expect(page).toHaveTitle(/Dashboard/);

		// activate plugin if not activated yet:
		await page.goto('/wp-admin/plugins.php');
		const activateSidebarRelated = page.locator('#activate-aside-related-article-block');
		if (await activateSidebarRelated.isVisible()) {
			console.log('>>>>>>> Plugin Activation (it was deactivated)');
			await activateSidebarRelated.click();
		}

		// reset dummy data and ensure that we have dummy data created
		// (at least the placeholder 300x150 image uploaded programmatically):
		await page.goto('/wp-admin/options-general.php?page=create-dummy-data&reset=1');
		await expect(page.locator('img[data-check="demo-attachment"]')).toBeVisible();

		console.log('>>>>>>> Before ALL finished. Start Single test');
	});

	test.afterEach('Regenerating after test', async ({ page }) => {
		console.log('This test is finsihed: Regenerating the posts');
		await page.goto('/wp-admin/options-general.php?page=create-dummy-data&reset=1');
	});
	test.afterAll('Teardown', async () => {
		console.log('We have Done - with tests');
	});

	// ------------------------
	// ------------------------
	// ------------------------ THE TEST
	// ------------------------ @TODO: it takes 30 secs, its too long. Optimize it.

	test('Add same category to two existing posts and insert block in the second', async ({ admin, editor, page }) => {
		const CAT_NAME = 'Politics'; // the category must exist, I created with class-create-dummy-data.php
		console.log('>>>>>>> Start Single test for cat ', CAT_NAME);

		// 1) Filter list of posts by cat Politics
		await page.getByRole('link', { name: 'Posts', exact: true }).click();
		await page.getByRole('link', { name: 'Politics', exact: true }).first().click();
		const postsListedPoliticsURL = await page.url();

		// 2) Open first post
		const firstPost = await page.locator('a.row-title').first();
		await firstPost.click();

		await page.waitForTimeout(3000);
		const welcomeImage = await page.locator(`div[aria-label="Welcome to the block editor"]`);
		if (await welcomeImage.isVisible()) {
			console.log('>>>>>>> Welcome wizard is visible');
			await page.getByLabel('Close', { exact: true }).click();
		} else {
			console.log('>>>>>>> no Welcome guide');
		}

		await editor.canvas.locator('[aria-label="Add title"]').fill('Barack Obama writes a new book');
		// await openCategoriesPanelIfClosed({ page });
		await page.getByRole('button', { name: 'Save', exact: true }).click();

		// 2) Open second post
		await page.goto(postsListedPoliticsURL);
		const secondPost = await page.locator('a.row-title').nth(1);
		await secondPost.click();

		// insert the block, check and save
		const paragraphInEditor = editor.canvas.locator('p').nth(0);
		await expect(paragraphInEditor).toBeVisible();
		await paragraphInEditor.click({ position: { x: 0, y: 0 } });
		await page.getByLabel('Toggle block inserter').click();
		await page.getByPlaceholder('Search').click();
		await page.getByPlaceholder('Search').fill('aside');
		await page.getByRole('option', { name: ' Aside Related Article' }).click();
		await page.getByLabel('Close block inserter');
		// all previous 5 lines can be replaced with this, but it appends it instead of putting it where i want
		// await editor.insertBlock({ name: 'coco/aside-related-article' }, 1);

		// edit attributes block
		await page.getByRole('tab', { name: 'Block', exact: true }).click();
		await page.getByLabel('Category', { exact: true }).check();
		await page.waitForTimeout(2000);
		const catsDropdownTextContent = await page
			.locator('.components-panel__body.is-opened #inspector-select-control-0')
			.textContent();
		console.log('Text Content:', catsDropdownTextContent);
		await expect(catsDropdownTextContent).toContain(CAT_NAME);
		await page.getByLabel('Select a Category or Tag').selectOption({ label: 'Politics' });
		// await page.getByLabel('Select a Category or Tag').selectOption({ index: 1 });
		await page.getByRole('button', { name: 'Save', exact: true }).click();
		await expect(editor.canvas.locator('[aria-label="Block: Aside Related Article"]')).toBeVisible();

		// Check the frontend
		const viewPostElement = await page.getByRole('link', { name: 'View Post', exact: true }).first();
		await expect(viewPostElement).toBeVisible();
		const page2Promise = page.waitForEvent('popup');
		await viewPostElement.click();
		const page2 = await page2Promise;
		await expect(page2.getByRole('link', { name: 'Related Article Politics ⦿' })).toBeVisible();

		// Compare the snapshot. Instead of using toMatchAriaSnapshot we check the textContent reasonabily.
		const relatedArticleText = await page2
			.locator('.wp-block-coco-aside-related-article.is-frontend')
			.textContent();

		const snapshotPath = 'tests/playwright/screenshots';
		console.log('>>>> taken snapshots editor and frontend at: ', snapshotPath);
		await page.screenshot({ path: `${snapshotPath}/editor.png`, fullPage: true });
		await page2.screenshot({ path: `${snapshotPath}/frontend.png`, fullPage: true });

		// Final assertion: the related article must be category Politics
		const sanitizedText = relatedArticleText.replace(/\t/g, ' ').replace(/\n/g, ' ');
		console.log('>>>> SNAPSHOT as text: ', sanitizedText);
		await expect(relatedArticleText).toContain(CAT_NAME);
	});
});
