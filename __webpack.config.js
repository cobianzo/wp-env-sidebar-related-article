const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const glob = require('glob');

const entryPoints = defaultConfig.entry();

// Scan the standard blocks (using block.json, jsx for Edit...) inside src/blocks
// NOTE: it works, but it duplicates the js in /build/blocks/name-of-block/index.js and build/name-of-block.js
glob.sync('./src/blocks/*/').forEach((folder) => {
	const entry = path.basename(folder);
	entryPoints[entry] = path.resolve(__dirname, folder);
});

// One inidividual file any other public helper related to the site
entryPoints['public-generic'] = './inc/js/public-generic.js';

module.exports = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry,
		...entryPoints,
	},
	output: {
		...defaultConfig.output,
		path: path.resolve(__dirname, 'build'),
		filename: '[name].js',
	},
};
