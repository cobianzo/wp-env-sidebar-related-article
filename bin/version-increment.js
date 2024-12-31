// Usage: `node ./bin/version-increment.js`

const fs = require('fs');
const path = require('path');
const semver = require('semver');

// Paths to your files
const FILES = {
	pluginPHP: path.join(__dirname, '../', 'aside-related-article-block.php'),
	packageJSON: path.join(__dirname, '../', 'package.json'),
	readmeTXT: path.join(__dirname, '../', 'README-plugin.md'),
};

/**
 * Update version in a file based on the type of file.
 * @param {string} filePath       - Path to the file.
 * @param {string} currentVersion - Current version to replace.
 * @param {string} newVersion     - New version to insert.
 */
function updateFileVersion(filePath, currentVersion, newVersion) {
	const content = fs.readFileSync(filePath, 'utf-8');
	const updatedContent = content.replace(new RegExp(currentVersion, 'g'), newVersion);
	fs.writeFileSync(filePath, updatedContent, 'utf-8');
	console.log(`Updated version in ${filePath}`);
}

/**
 * Extract the current version from a file.
 * @param {string} filePath - Path to the file.
 * @return {string} - The current version.
 */
function extractVersion(filePath) {
	const content = fs.readFileSync(filePath, 'utf-8');
	const match = content.match(/\b\d+\.\d+\.\d+\b/); // Matches versions like 1.0.3
	if (!match) {
		throw new Error(`No version found in ${filePath}`);
	}
	return match[0];
}

/**
 * Main function to handle the version update.
 * @param {string} incrementType - Type of version increment (patch, minor, major).
 */
function updateVersion(incrementType = 'patch') {
	try {
		console.log(`Reading current version from ${FILES.pluginPHP}`);

		// Extract the current version from plugin.php (source of truth)
		const currentVersion = extractVersion(FILES.pluginPHP);
		console.log(`Current version: ${currentVersion}`);

		// Calculate the new version
		const newVersion = semver.inc(currentVersion, incrementType);
		if (!newVersion) {
			throw new Error(`Failed to calculate new version from ${currentVersion}`);
		}
		console.log(`New version: ${newVersion}`);

		// Update all files
		updateFileVersion(FILES.pluginPHP, currentVersion, newVersion);
		updateFileVersion(FILES.packageJSON, currentVersion, newVersion);
		updateFileVersion(FILES.readmeTXT, currentVersion, newVersion);

		console.log('Version updated successfully in all files.');

		return newVersion;
	} catch (error) {
		console.error(`Error: ${error.message}`);
		return '';
	}
}

// Command-line arguments for increment type
const args = process.argv.slice(2);
const incrementType = args[0] || 'patch';
if (!['patch', 'minor', 'major'].includes(incrementType)) {
	console.error('Invalid increment type. Use "patch", "minor", or "major".');
	process.exit(1);
}

// Execute the updateVersion function
const newVersion = updateVersion(incrementType);

module.exports = newVersion;
