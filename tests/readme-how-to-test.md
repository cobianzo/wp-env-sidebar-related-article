# TEST SUITES in playwright

## Tips
- aside-related-article-block.spec.js is the only test made so far.
- We use the @wordpress/e2e-test-utils-playwright and we try to use its functions
- We check the repo of gutenberg to learn about the valid functions: https://github.com/WordPress/gutenberg/tree/trunk/test/e2e/specs
	- I suggest you download the repo and ur Cmd + F to find stuff
- The testing env is the one of wp-env, in this project http://localhost:8891 (.wp.env.json)
	- we set it in playwright.config.js
- If we need to reset the test DB:

IMPORTANT: in real development I had to run this several times.
```
npx wp-env run tests-cli wp db reset --yes
npx wp-env run tests-cli wp core install --url="http://localhost:8891" --title="Mi Test Site WP" --admin_user="admin" --admin_password="password" --admin_email="admin@example.com"
npx wp-env run tests-cli -- wp plugin activate aside-related-article-block
```


- In my computer, sometimes I run out of memory creating dummy data by hand
	- So I created a page that, when visited, creates everything (only if it was not created before) -  tests/class-create-dummy-data.php.
- I suggest to use the extension for playwright tests of VS Code
	- It shws the error in the editor, and allows you to record actions.
- and also the UI of playwright
	- `npm run test:js -- --ui`
	- to easily access to the screenshots and inspect them after the test is ran.
- At some point, with console logs you won't need to see the browser. It consumes too many resources.
	- Just run te test and check the console logs and errors in the TEST RESULTS tab of VSCode.

# TEST SUITES in phpunit

We run it in two cases:
- when developing in local (npm run test:php)
- in git action when pushing to repo

PHPUnit is not very useful in this project, but it has been set up and works, even in the watching mode

I suggest you run in watch mode (`npm run test:php:watch` which uses `composer run test:watch`).

> **Important Note**: The phpUnit test is set differently when we work in wp-env development, from when we run the tests in github actions or anywhere outside wp-env. That is why
`test/bootstrap.php` calls two different .php files, for different setups.

## PHPUnit in wp-env docker development

	- wp-env phpunit -> check `test:php` in `package.json`
	- wp-env phpunit is ran with `npm run test:php`, which loads the command inside wp-env docker container.
	- wp-env phpunit takes longer because we install WP every time we run it.
	- wp-env apparently works better because when I'm outside it, I need to call `do_action('init')` for it to work

	- The PHPUnit WATCH MODE works very well. It set ups wp differently, creates a different DB ... but it works
	- I call it in composer setting a env var IS_WATCHING, to use the bootstrap.php for the wp-env envionrment.

## PHPUnit in git action (Github)
	- in github actions, check .github/workflows/tests.yml
	- There I install a full wordpress with WP CLI, place the plugin by
cloning the repo, and run `composer run test`, which uses
