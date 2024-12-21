module.exports = {
	content: [
		'./src/**/*.{php,html,js,jsx,ts,tsx}',
		'./**/*.php',
		'./**/*.css',
		'./**/*.html',
		'./*.php',
		'./**/*.php',
	],
	theme: {
		extend: {},
	},
	plugins: [],
	corePlugins: {
		preflight: false, // Disable base styles that might conflict with WordPress
	},
};
