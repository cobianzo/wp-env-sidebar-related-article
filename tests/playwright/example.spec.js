const { test, expect } = require( '@playwright/test' );

test.describe( 'Theme Tests', () => {
	test( 'Verify home page loads', async ( { page } ) => {
		// Navegar al sitio web de wp-env
		await page.goto( 'http://localhost:8890' );

		// Verificar que el título de la página contiene el nombre del sitio
		const title = await page.title();
		expect( title ).toContain( 'wp-env-portfolio-backtrack-theme' );
	} );

	test( 'Check theme header visibility', async ( { page } ) => {
		await page.goto( 'http://localhost:8890' );

		// Verificar que el encabezado existe
		const header = await page.locator( 'header' );
		await expect( header ).toBeVisible();
	} );

	test( 'Verify Gutenberg editor loads', async ( { page } ) => {
		// Acceder al panel de administración
		await page.goto( 'http://localhost:8890/wp-admin' );

		// Iniciar sesión si es necesario
		await page.fill( '#user_login', 'admin' );
		await page.fill( '#user_pass', 'password' );
		await page.click( '#wp-submit' );

		// Abrir el editor de Gutenberg
		await page.goto( 'http://localhost:8890/wp-admin/post-new.php' );
		await expect( page ).toHaveURL( 'http://localhost:8890/wp-admin/post-new.php' );

		// Verificar que el editor cargue
		const blockEditor = page.locator( '.block-editor-writing-flow' );
		await expect( blockEditor ).toBeVisible();
	} );
} );
