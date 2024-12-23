/**
 * WordPress dependencies
 */
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

// Add any a new entry point by extending the webpack config.
module.exports = [
	...defaultConfig,
	{
		...defaultConfig[0],
		entry: {
			'public-generic': './inc/js/public-generic.js',
		},
	},
];
