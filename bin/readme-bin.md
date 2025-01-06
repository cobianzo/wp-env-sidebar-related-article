There are 4 files here
├── build-plugin.js - creates the /dist folder with the plugin files (compressed or not) ready for distribution
├── install-wp-tests.sh - irrelevant
├── version-helpers.js - I need one function to be reused in both build-plugin.js and version-increment.js
└── version-increment.js - extracts the current version, and updates it to a patch/minor or major version in every file that uses it.

`install-wp-tests.sh` is not relevant: it is used by wp-env to create the phpunit folders, so `bootstrap.php` can find the worpdress core files. 
You don't need to edit it. Simply run `npm run test:php`.

# Create zip plugin (with version increment)

**Usage for testing**

```
node ./bin/version-increment.js [patch|minor|major]
```

In `.github/workflows/create-zip-plugin.yml` I indlude the call to
- `version-increment.js`
- `build-plugin.js`

If you push with a commit saying `[skip version ci]`, it will not execute this workflow of the creation of a new version.

If you push with a commit saying `[major update]`, it will increase the version to a major number (eg from 1.3.1 to 2.0.0), same with `[minor update]` (eg from 1.3.1 to 1.4.0). By default it increases a a patch (eg from 1.3.1 to 1.3.2)

after calling updating the new `version-increment.js` there is a new commit 
`- [skip version ci] Incrementar versión del plugin a ${{ env.VERSION }}`
and push with that version.
