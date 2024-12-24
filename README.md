
# Summary

wp-env / wp-scripts (experimental) / phpunit / playwright / linting (phpcs, phpstan, eslint, stylelint) / browser-sync

This plugin creates a block called **Aside Related Article**. When inserted, it shows a widget in one side of the text,
floated to the left, with the preview of the related article. You can choose what related article you want to display,
or you can let the system find the newsest on the category or tag that you choose (amont the cats and tags associated to
the current post).

## Dependencies

- package `@cobianzo/gutenberg-post-lookup-component`

# Start to work:

```
npm i
composer install
npm run start
```

Start environment with
```sh
npm run up (or > npx wp-env start)
```

you'll see somethign like this, where the MySQL ports might change.
```
WordPress development site started at http://localhost:8890
WordPress test site started at http://localhost:8891
MySQL is listening on port 49313
MySQL for automated testing is listening on port 49412
```

Sometimes the installation didn't work ok and I had to run
`npx wp-env run cli wp core install --url="http://localhost:8890" --title="Mi Sitio WP" --admin_user="admin" --admin_password="password" --admin_email="admin@example.com"`

Inside the docker container the MySQL ports are the regular 3306 and 3307 for testing. They are just mapped out.

the password for MySQL inside the wp-env docker container is 'password', for the user 'root'

You can use terminal commands in the wp-env with the wp-env cli commands:

`npx wp-env run cli wp option get siteurl` or `npx wp-env run cli -- wp option get siteurl`

> you can use `npm run wp-env ...` instead of `npx wp-env run ...`


or if you want to actually get inside the docker container and work locally, you will run

`npm run wp-env run cli sh -c`

---

You can install the demo sample page with:

Maybe you'll need to run
npx wp-env run cli -- wp plugin activate wordpress-importer
npx wp-env run cli -- wp plugin activate aside-related-article-block

```sh

npx wp-env run cli -- wp import wp-content/plugins/aside-related-article-block/themeunittestdata.wordpress.xml --authors=create

npx wp-env run cli -- wp rewrite structure '/%postname%/'

npx wp-env run cli -- wp rewrite flush --hard
```

and visit it with `http://localhost:8890/wp-6-1-spacing-presets`


## Developing

You will normally have one or two terminal windows running:

`npm run start` > runs the tailwind compilation on every change, and if you are writing js in /src, it will compile it into /build with wp-scripts

`npm run browser-sync` > if you want to work on `http://localhost:3000` and have the hot reload.

I suggest to watch the e2e tests as you work.
This can't be done right now in terminal, but you can do it either using the
playwright ui ( `npm run test -- --ui` ) or using the VSCode Playwright Test and activate the watch.

**PHP lint**

Lint your work. In a php file run the `composer lint <name.php>` and `composer format <name.php>` in any file.
Use `composer analize <name.php>` for deep linting.
From time to time run it for the whole project `composer lint .` and `composer format .`.

**JS lint**

With prettier, your files js and css should format automatically on save.

`npm run lint:js` - lints all files (for a reason in my last work it doesnt seem to work)

`npm run lint:css` - lints all files

## Summary: After making changes in js, css and php files

Linting formatting js, css, and php files.

For PHP
```
npm run format
composer lint .
composer analyze <name.php>
(or > npm run lint:php)
composer format .
```

Reccomended VSCode extensions
phpcs, phpcbf, eslint, stylelint, prettier

---

# PHPUNIT

it works, I copied it from the create-block-theme plugin, with some adaptations because this is a theme not a plugin. The file `bin/install-wp-tests.sh` is not used.

It's not really needed for this simple plugin, where there are no php functionalities or APIs on itself. You can run it individually or with watch

`npm run test:php`

`npm run test:php:watch`

# Playwright e2e tests

check the 'readme-how-to-test.md'. You'll find tips about how to test. There is an e2e test with dummy data.

`❯ npx playwright test --ui`

 or

 `npm run test:js`

# TODO

- Add support to align the block to the left or right
- We need CI/CD (circle CI or git hooks)
- Refactor to typescript
- Transform most of the styles in the css into json styles, with variations.
- Create a version bump script
- Create the script to publish the plugin
- Create screenshots and a video demo and upload it to my website
