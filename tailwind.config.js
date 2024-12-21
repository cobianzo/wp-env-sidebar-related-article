module.exports = {
	content: [
		'./wp-portfolio-theme/src/**/*.{php,html,js,jsx,ts,tsx}',
		'./wp-portfolio-theme/**/*.php',
		'./wp-portfolio-theme/**/*.html',
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
