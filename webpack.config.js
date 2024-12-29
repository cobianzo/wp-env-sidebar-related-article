/**
 * Externam dependencies
 */
const glob = require('glob');
const path = require('path');

/**
 * WordPress dependencies
 */
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

/*
scan all files view.css inside of src/blocks/<nameofblock>/view.css
*/
const viewCssEntries = {};

glob.sync('./src/blocks/*/view.css').forEach((file) => {
	const blockName = path.basename(path.dirname(file));
	console.log('>>>>> block ', file, `${blockName}`);
	viewCssEntries[`blocks/${blockName}/view-index`] = `./${file}`;
});

// Add any a new entry point by extending the webpack config.
module.exports = [
	...defaultConfig,
	{
		...defaultConfig[0],
		entry: viewCssEntries,
		/*
		add this fore more custom bundles.
		entry: {
			'public-generic': './inc/js/public-generic.js',
		}, */
	},
];
