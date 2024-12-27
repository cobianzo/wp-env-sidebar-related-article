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

		// 1) Open first post
		await page.getByRole('link', { name: 'Posts', exact: true }).click();
		const firstPost = await page.locator('a.row-title').first();
		await firstPost.click();
		// avoid showing the Welcome guide
		await editor.setPreferences('core/edit-post', {
			welcomeGuide: false,
			fullscreenMode: false,
		});
		const welcomeCloseButton = page.getByLabel('Close', { exact: true });
		if (await welcomeCloseButton.isVisible()) {
			await welcomeCloseButton.click();
		}
		await editor.canvas.locator('[aria-label="Add title"]').fill('Barack Obama writes a new book');
		// Open panel categories if not open
		await openCategoriesPanelIfClosed({ page });
		// assign cat Politics ans save
		await page.getByLabel(CAT_NAME).check();
		await page.getByRole('button', { name: 'Save', exact: true }).click();

		// 2) Open second post
		await page.getByRole('link', { name: 'All Posts' }).click();
		const secondPost = await page.locator('a.row-title').nth(1);
		await secondPost.click();

		// assing cat Politics too
		await page.getByLabel(CAT_NAME).check();

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
		const catsDropdownTextContent = await page
			.locator('.components-panel__body.is-opened #inspector-select-control-0')
			.textContent();
		console.log('Text Content:', catsDropdownTextContent);
		await expect(catsDropdownTextContent).toContain(CAT_NAME);
		await page.getByLabel('Select a Category or Tag').selectOption('2');
		// await page.getByLabel('Select a Category or Tag').selectOption({ index: 1 });
		await page.getByRole('button', { name: 'Save', exact: true }).click();
		await expect(editor.canvas.locator('[aria-label="Block: Aside Related Article"]')).toBeVisible();

		// Check the frontend
		await expect(page.getByLabel('View Post')).toBeVisible();
		const page2Promise = page.waitForEvent('popup');
		await page.getByLabel('View Post').click();
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
