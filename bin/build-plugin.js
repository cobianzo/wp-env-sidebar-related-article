// the default npx wp-scripts plugin-zip has some small errors:
// it doesnt include src and doesnt use my readme-plugin.md renamed into readme.md
// so I create my own

// Usage: node .github/scripts/build-plugin.js [version-number]

const fs = require('fs').promises;
const fsSync = require('fs');
const archiver = require('archiver');
const path = require('path');

class PluginBuilder {
	constructor(version) {
		// Configuración básica del plugin
		this.pluginSlug = 'aside-related-article-block';
		this.version = version;
		this.distDir = 'dist';

		// Lista de archivos y directorios a incluir
		this.directories = ['lib', 'src', 'inc', 'screenshots', 'build'];

		this.files = [
			{
				source: 'README-plugin.md',
				target: 'README.md',
			},
			{
				source: `${this.pluginSlug}.php`,
				target: `${this.pluginSlug}.php`,
			},
		];
	}

	// Limpia archivos .DS_Store que pueden causar problemas
	async cleanDsStoreFiles() {
		const dsStoreFiles = ['build/.DS_Store', '.DS_Store'];

		for (const file of dsStoreFiles) {
			try {
				await fs.access(file);
				await fs.unlink(file);
				console.log(`Removed ${file}`);
			} catch (error) {
				// Ignoramos errores de archivos que no existen
				if (error.code !== 'ENOENT') {
					throw error;
				}
			}
		}
	}

	// Verifica que todos los archivos y directorios necesarios existan
	async validateFiles() {
		// Verificar directorios
		for (const dir of this.directories) {
			try {
				await fs.access(dir);
			} catch (error) {
				console.warn(`Warning: Directory '${dir}' not found, skipping...`);
				// Removemos el directorio de la lista
				this.directories = this.directories.filter((d) => d !== dir);
			}
		}

		// Verificar archivos individuales
		for (const file of this.files) {
			try {
				await fs.access(file.source);
			} catch (error) {
				throw new Error(`Required file '${file.source}' not found!`);
			}
		}
	}

	// Crea el directorio dist si no existe
	async createDistDirectory() {
		try {
			await fs.access(this.distDir);
		} catch {
			await fs.mkdir(this.distDir);
			console.log(`Created ${this.distDir} directory`);
		}
	}

	// Crea el archivo zip
	async createZip() {
		const zipFileName = `${this.distDir}/${this.pluginSlug}-${this.version}.zip`;
		const output = fsSync.createWriteStream(zipFileName);
		const archive = archiver('zip', { zlib: { level: 9 } });

		// Manejamos eventos del archiver para mejor feedback
		archive.on('warning', function (err) {
			if (err.code === 'ENOENT') {
				console.warn('Warning:', err);
			} else {
				throw err;
			}
		});

		archive.on('error', function (err) {
			throw err;
		});

		return new Promise((resolve, reject) => {
			output.on('close', () => {
				console.log(`Created ${zipFileName} (${archive.pointer()} bytes)`);
				resolve(zipFileName);
			});

			archive.pipe(output);

			// Añadir directorios
			for (const dir of this.directories) {
				archive.directory(dir, dir);
			}

			// Añadir archivos individuales
			for (const file of this.files) {
				archive.file(file.source, { name: file.target });
			}

			archive.finalize();
		});
	}

	// Método principal que ejecuta todo el proceso
	async build() {
		try {
			console.log(`Building plugin version ${this.version}...`);

			await this.cleanDsStoreFiles();
			await this.validateFiles();
			await this.createDistDirectory();
			const zipFile = await this.createZip();

			console.log('Build completed successfully!');
			return zipFile;
		} catch (error) {
			console.error('Build failed:', error.message);
			throw error;
		}
	}
}

// Función principal para ejecutar el script
async function main() {
	// Obtener la versión del argumento de línea de comandos
	const version = process.argv[2];

	if (!version) {
		console.error('Error: Version parameter is required');
		console.log('Usage: node build-plugin.js <version>');
		console.log('Example: node build-plugin.js 1.0.0');
		process.exit(1);
	}

	// Validar formato de versión
	if (!/^\d+\.\d+\.\d+$/.test(version)) {
		console.error('Error: Version must be in format X.Y.Z (e.g., 1.0.0)');
		process.exit(1);
	}

	try {
		const builder = new PluginBuilder(version);
		await builder.build();
	} catch (error) {
		console.error('Build process failed:', error);
		process.exit(1);
	}
}

// Ejecutar solo si es llamado directamente
if (require.main === module) {
	main();
}

module.exports = PluginBuilder;
