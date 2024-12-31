`install-wp-tests.sh` is used by wp-env to create the phpunit folders, so `bootstrap.php` can find the worpdress core files. You don't need to edit it. Simply run `npm run test:php`.

In `.github/workflows/create-zip-plugin.yml` I indlude the call to
- `version-increment.js`
- `build-plugin.js`

after calling updating the new `version-increment.js` there is a new commit and push with that version.
