<?php

/**
 * Useful for debugging:
 * print_r( getenv() );
 */

if ( 'tests-mysql' === getenv( 'WORDPRESS_DB_HOST' ) || ! empty( getenv( 'IS_WATCHING' ) ) ) {
	require 'bootstrap-wp-env.php';
	// we are in wp-env (local), we know it because the host is tests-mysql and the db is tests-wordpress
} else {
	// we are in github actions, in wp-content/plugins/aside-related-article-block/ folder of  wordpress installation
	require 'bootstrap-git-action.php';
}
return;
