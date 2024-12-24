const { expect } = require('@wordpress/e2e-test-utils-playwright');
const dummyText = require('./assets/dummyText.json');

// not in use but it works. creates post and assign cats and tags
export const createRelatedArticleWithTerms = async function (
	utils,
	options = { title: 'New Article to refer', categories: [], tags: [], assert: true }
) {
	const { page, admin, editor } = utils;

	// Create a new post
	await admin.createNewPost({
		title: options.title,
		content: dummyText.content,
		showWelcomeGuide: false,
		fullscreenMode: false,
	});

	await page.getByRole('tab', { name: 'Post' }).click();

	// Adding a category to the current post
	options.categories.forEach(async (catName) => {
		await page.waitForSelector(`label[for="${catName}"]`);
		await page.getByLabel(catName).check();
	});
	// Adding tags to the current post
	options.tags.forEach(async (tagName) => {
		await page.getByRole('button', { name: tagName }).click();
	});

	// add featured image
	await page.getByRole('button', { name: 'Set featured image' }).click();
	await page.locator('.attachments-wrapper li').first().click();
	// await page.getByLabel('dsc20050604_133440_34211').click();
	await page.getByRole('button', { name: 'Set featured image' }).click();
	if (options.assert) await expect(page.getByLabel('Edit or replace the featured')).toBeVisible();

	// Publishing
	await editor.publishPost();

	// asserting that the post has correctly been published
	if (options.assert)
		await expect(page.getByLabel('Editor publish').getByRole('link', { name: 'View Post' })).toBeVisible();

	return true;
};

// using WP CLI for quick data manipulation (not in use and not tested)
export const execS = async function (command) {
	const { exec } = require('child_process');
	return new Promise((resolve, reject) => {
		exec(command, (error, stdout, stderr) => {
			if (error) {
				reject(error);
			} else {
				resolve(stdout);
			}
		});
	});
};

/**
 * Sometimes the panel Categories is closed. Before we assign the cat
 * we need to open it. Used, tested, and it works
 *
 * @param { page } Page page
 */
export const openCategoriesPanelIfClosed = async function ({ page }) {
	// Open categories panel  (there might be better ways with the plugin
	if (
		!(await page.locator('.editor-post-taxonomies__hierarchical-terms-list[aria-label="Categories"]').isVisible())
	) {
		console.log('>>>> We had to open the Categories Panel because it was closed. ');
		await page.getByRole('button', { name: 'Categories' }).click();
		await page.waitForTimeout(3000);

		return;
	}

	// const panelTextContent = await page.locator('.components-panel__body').first().textContent();
	// console.log('>>>>>>> Is Panel Categories open? Text Content: ', panelTextContent);
	// const panelBodyOpen = await page.evaluate(() => {
	// 	const panel = document.querySelectorAll('.components-panel__body')[0];
	// 	return panel.classList.contains('is-opened');
	// });
	// console.log('>>>>>>>>>>>> ', panelBodyOpen);
	// if (!panelBodyOpen) {
	// 	console.log('>>>>>>>>>>>> Opening ');
	// 	await page.getByRole('button', { name: 'Categories' }).click();
	// }
};
