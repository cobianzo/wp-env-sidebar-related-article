<?php

if ( 'tests-mysql' === getenv( 'WORDPRESS_DB_HOST' ) ) {
	// we are in wp-env (local), we know it because the host is tests-mysql and the db is tests-wordpress
	require 'bootstrap-wp-env.php';
} else {
	// we are in github actions, in wp-content/plugins/aside-related-article-block/ folder of  wordpress installation
	require 'bootstrap-git-action.php';
}
return;
