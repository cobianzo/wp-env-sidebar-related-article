# TEST SUITES in PHPUnit and playwright

# Playwright E2E

# troubleshooting (problems that I found when using e2e testing):

- **IMPORTANT**: in real development I had to reset the db myself several times with wp cli, with this:

```

npx wp-env run tests-cli wp db reset --yes
npx wp-env run tests-cli wp core install --url="http://localhost:8891" --title="Mi Test Site WP" --admin_user="admin" --admin_password="password" --admin_email="admin@example.com"
npx wp-env run tests-cli -- wp plugin activate aside-related-article-block

```

- If, when running your test, you see `Failed to load resource: ...` in the results, it means that - sometimes the enviroment of wp-env gets crazy and tries to find the assets of the block in :
http://localhost:8890/wp-content/themes/default/var/www/html/wp-content/plugins/aside-related-article-block/build/blocks/aside-related-article/style-index.css?ver=1.0
	- I still don't know why it happens and how to fix it. I restart docker and push the env up again to fix it.

- In my computer, sometimes I run out of memory creating dummy data by hand
	- So I created a page that, when visited, creates everything (only if it was not created before) -  tests/class-create-dummy-data.php.

## Tips
- aside-related-article-block.spec.js is the only test made so far.
- We use the @wordpress/e2e-test-utils-playwright and we try to use its functions
- We check the repo of gutenberg to learn about the valid functions: https://github.com/WordPress/gutenberg/tree/trunk/test/e2e/specs
	- I suggest you download the repo and ur Cmd + F to find stuff
- The testing env is the one of wp-env, in this project http://localhost:8891 (.wp.env.json)
	- we set it in playwright.config.js

- I suggest to use the extension for playwright tests of VS Code
	- It shws the error in the editor, and allows you to record actions.
- and also the UI of playwright
	- `npm run test:js -- --ui`
	- to easily access to the screenshots and inspect them after the test is ran.
- At some point, with console logs you won't need to see the browser. It consumes too many resources.
	- Just run te test and check the console logs and errors in the TEST RESULTS tab of VSCode.

# TEST SUITES in PHPUnit

We run it in two different environments, two cases:
- when developing in local (`npm run test:php` and `npm run test:php:watch`)
- in git action when pushing to repo (in workflow `tests.yml`)

PHPUnit is not very useful in this project, but it has been set up and works, even in the watching mode.
But the watching mode is magic, it works ok over the wp-env test database.

## Watch mode: works great

- I suggest you run in watch mode: `npm run test:php:watch` (which uses `composer run test:watch`).
- It will clean the test database every time it runs.
- It will reload every time you edit the test code, but it doesnt reload when you work in the project
- Use ENTER from time to time to reload the testing inthe terminal running the watch mode
- Note that in watch mode, in composer, we set the env IS_WATCHING=true so we use th right `bootstrap.php`

> **Important Note**: The phpUnit test is set differently when we work in wp-env development, from when we run the tests in github actions or anywhere outside wp-env. That is why
`test/bootstrap.php` calls two different .php files, for different setups.

## PHPUnit in wp-env docker development

	- wp-env phpunit -> check `test:php` in `package.json`
	- wp-env phpunit is ran with `npm run test:php`, which loads the command inside wp-env docker container.
	- wp-env phpunit takes longer because we install WP every time we run it.
	- wp-env apparently works better because when I'm outside it, I need to call `do_action('init')` for it to work

	- The PHPUnit WATCH MODE works very well. (I insist)
	- Again: I call it in composer setting a env var IS_WATCHING, to use the bootstrap.php for the wp-env envionment.

## PHPUnit in WP LOCAL or any regular WP installation (also in git actions for Github)

	- in github actions, check .github/workflows/tests.yml
	- There I install a full WordPress with WP CLI, place the plugin by
cloning the repo, and run `WP_PHPUNIT__TESTS_CONFIG=tests/wp-config.php composer run test`, which uses
the connection to the DB that we have just created

When we run it in github actions, if we want to change the

DB connection, we can use for example `WP_PHPUNIT__TESTS_CONFIG=tests/wp-config.php WP_DB_HOST=127.0.0.1 composer run test`

	- We can simulate the same steps by working in local.
	- wp-env apparently works better because in this git action case, I need to call `do_action('init')` for it to work
