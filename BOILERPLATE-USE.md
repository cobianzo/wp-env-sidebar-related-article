# Start a new plugin from this boilerplate

Original boilerplate: https://github.com/cobianzo/wp-env-sidebar-related-article

## Git clone and rename repo

clone the original repo: `https://github.com/cobianzo/wp-env-sidebar-related-article`
rm -rf .git
git init
git remote add origin git@github.com:cobianzo/asim-gravity-form-map-field.git

## renaming

rename initial file to your plugin's: `asim-gravity-form-map-field`
replace all occurrences of the plugin's slug into `asim-gravity-form-map-field`
replace plugin name in the original php name.
rename references to the repo `https://github.com/cobianzo/wp-env-sidebar-related-article`
change description and other fields in `asim-gravity-form-map-field.php`

## tests renaming

delete in `tests/playwright` the current tests, and create you own
adapt the phpunit tests
decide if we need the page to create dummy data.

## cleanup and setup

remove `screenshots` files
`package.json`, remove packages you don't need.
setup  `.wp-env.json`  
setup husky with `npm run prepare`
add files to ignore in linting: .stylelintignore, .eslintrc, 

## Start working (read the documentation in README.md)

npm install, composer install
npm run prepare
start docker and npm run up
start testing phpUnit and e2e.
run lintings
npm run browser-sync

## Make 1st commit and confirm husky work

## Make a pull request and confirm that the PHPCS and PHPUnit test run

## Make first push and confirm plugin version increments and the zip is created.


## Finally 

Recreate the README-plugin.txt, README.md
Edit `build-plugin.js` to use the right files on the creation of the distribution plugin.

