
# Summary

wp-env / wp-scripts (experimental) / phpunit / playwright / linting (phpcs, phpstan, eslint, stylelint) / browser-sync

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


## After making changes in js, css and php files

Linting formatting js, css, and php files.

```
npm run format
composer lint .
composer analyze .
(or > npm run lint:php)
composer format .
```

Reccomended VSCode extensions
phpcs, phpcbf, eslint, stylelint, prettier

---

# PHPUNIT

it works, I copied it from the create-block-theme plugin, with some adaptations because this is a theme not a plugin. The file `bin/install-wp-tests.sh` is not used.

`npm run test:php`

# Playwright e2e tests

`❯ npx playwright test --ui`

 or

 `npm run test:js`

# TODO

- Add support to align the block to the left or right
- add more filters and actions to the block.
- We need Husky and CI/CD
- Refactor to typescript
- Add e2e testing
- Transform most of the styles in the css into json styles, with variations.
