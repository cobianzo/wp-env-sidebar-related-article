const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );
const glob = require( 'glob' );

const entryPoints = defaultConfig.entry();

// We will scan and
const configFile = path.resolve( __dirname, 'dynamic-partials-plugin/config.json' );
const config = require( configFile );

// Scan the standard blocks (using block.json, jsx for Edit...) inside src/blocks
// NOTE: it works, but it duplicates the js in /build/blocks/name-of-block/index.js and build/name-of-block.js
glob.sync( './src/blocks/*/' ).forEach( ( folder ) => {
	const entry = path.basename( folder );
	entryPoints[ entry ] = path.resolve( __dirname, folder );
} );

// Now DYNAMIC PARTIALS SETUP: creating one bundle for the helpers to reload templates with ajax
// and create a bundle for the js of every partial which has it.

// One individual file for the helper that allow me to use ajax to load templates easily
entryPoints[ 'dynamic-partials-public-helpers' ] = './dynamic-partials-plugin/public-helpers.js';

// One inidividual file any other public helper related to the site
entryPoints[ 'public-generic' ] = './inc/js/public-generic.js';

// Now dynamic blocks, scan all the js files inside the dynamic-blocks js folders
const dynamicBlocksDir = '.' + config[ 'js-source-path' ];
glob.sync( dynamicBlocksDir ).forEach( ( file ) => {
	const folder = path.basename( path.dirname( file ) );
	console.log(
		'creating dynamicBlocksDir >>>>>> build js: ',
		file,
		folder,
		path.resolve( __dirname, file )
	);
	entryPoints[ folder ] = path.resolve( __dirname, file );
} );

module.exports = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry,
		...entryPoints,
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'build' ),
		filename: '[name].js',
	},
};
