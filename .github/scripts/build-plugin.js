// the default npx wp-scripts plugin-zip has some small errors:
// it doesnt include src and doesnt use my readme-plugin.md renamed into readme.md
// so I create my own

const fs = require('fs');
const archiver = require('archiver');

// Create a new zip file
const output = fs.createWriteStream('aside-related-article-block.zip');
const archive = archiver('zip', { zlib: { level: 9 } });

// Add files and directories to the zip file
archive.directory('lib/', 'lib');
archive.directory('src/', 'src');
// archive.directory('vendor/', 'vendor'); // there are no dependencies for production, only dev
archive.directory('build/', 'build');
archive.file('README-plugin.md', { name: 'README.md' });
archive.file('aside-related-article-block.php', { name: 'aside-related-article-block.php' });

// Finalize the zip file
archive.pipe(output);
archive.finalize();
