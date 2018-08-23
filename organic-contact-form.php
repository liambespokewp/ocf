<?php

/*
Plugin Name: Organic Contact Form
Description: A brief description of the Plugin.
Version: 1.0
Author: Liam Maclachlan
Author URI: https://www.linkedin.com/in/devlime/
*/

/** the prefix that is applied to any generated table */
define('OCF_TABLE_PREFIX', 'ocf_');

////////////////
// Autoloader
////////////////

spl_autoload_register(function ($class) {

	// base directory for the namespace prefix
	$base_dir = __DIR__ . '/';

	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = $base_dir . str_replace('\\', '/', $class) . '.php';

	// if the file exists, require it
	if ( file_exists($file) )
		require $file;

});


////////////////
/// Activation
////////////////

if ( !function_exists('ocf_create_database') ) :

	/**
	 * Create the tables required for the plugin if they don't exist
	 */
	register_activation_hook( __FILE__, 'ocf_create_database' );
	function ocf_create_database() {

		// grab to run the SQL queries
		global $wpdb;

		$queries = array();


		// make sure the right table will be used in the plugin initialisation
		$queries[] = sprintf(
			'USE %s;',
			DB_NAME
		);


		$queries[] = sprintf('
	                    CREATE TABLE IF NOT EXISTS `%s`.`%scontact_enteries`
	                        (
	                            `id_contact_entries` INT NOT NULL AUTO_INCREMENT,
	                            `name` VARCHAR(100) NOT NULL,
	                            `email` VARCHAR(50) NOT NULL,
	                            `tel` VARCHAR(20) NOT NULL,
	                            `enquiry` TEXT NOT NULL,
	                            `date` DATETIME NOT NULL,
	                            `ref_page` TEXT NOT NULL,
	                            PRIMARY KEY (`id_contact_entries`)
	                        )
	                    ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;',
			DB_NAME,
			OCF_TABLE_PREFIX
		);

		foreach ( $queries as $query )
			$wpdb->query( $query );

	}

endif;
