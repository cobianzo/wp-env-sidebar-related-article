const fs = require('fs');
const archiver = require('archiver');

// Create a new zip file
const output = fs.createWriteStream('wp-read-mode-block-plugin.zip');
const archive = archiver('zip', { zlib: { level: 9 } });

// Add files and directories to the zip file
archive.directory('lib/', 'lib');
archive.directory('src/', 'src');
archive.directory('vendor/', 'vendor');
archive.directory('build/', 'build');
archive.file('README.md', { name: 'README.md' });
archive.file('wp-env-read-more-block.php', { name: 'wp-env-read-more-block.php' });

// Finalize the zip file
archive.pipe(output);
archive.finalize();
