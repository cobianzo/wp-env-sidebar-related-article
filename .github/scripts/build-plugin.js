// the default npx wp-scripts plugin-zip has some small errors:
// it doesnt include src and doesnt use my readme-plugin.md renamed into readme.md
// so I create my own

// Usage: node .github/scripts/build-plugin.js

const fs = require('fs');
const archiver = require('archiver');

let filePath = 'build/.DS_Store';
if (fs.existsSync(filePath)) {
	fs.unlinkSync(filePath);
}
filePath = '.DS_Store';
if (fs.existsSync(filePath)) {
	fs.unlinkSync(filePath);
}

// Create a new zip file
const output = fs.createWriteStream('aside-related-article-block.zip');
const archive = archiver('zip', { zlib: { level: 9 } });

// Add files and directories to the zip file
archive.directory('lib/', 'lib');
archive.directory('src/', 'src');
archive.directory('inc/', 'inc');
archive.directory('screenshots/', 'screenshots');
// archive.directory('vendor/', 'vendor'); // there are no dependencies for production, only dev
archive.directory('build/', 'build');
archive.file('README-plugin.md', { name: 'README.md' });
archive.file('aside-related-article-block.php', { name: 'aside-related-article-block.php' });

// Close the zip file
archive.on('end', () => {
	console.log('Plugin zip file created successfully.');
});

// Finalize the zip file
archive.pipe(output);
archive.finalize();
