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

PHPUnit is not very useful in this project, but it has been set up and works.
Initially I set it up loading it in wp-env environment: `"test:php": "npm run test:php:setup && wp-env run tests-wordpress --env-cwd='wp-content/plugins/aside-related-article-block' composer run test",`

We run it in two cases:
- when developing in local (npm run test:php)
- in git action when pushing to repo

## PHPUnit in local

## PHPUnit in git action (Github)
- check the workflow test.yml. There I install a full wordpress with WP CLI, place the plugin by
cloning the repo, and run `composer run test`, which uses
