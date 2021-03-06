<?php

/*
Plugin Name: Organic Contact Form
Description: A simple contact form plugin.
Version: 1.0
Author: Liam Maclachlan
Author URI: https://www.linkedin.com/in/devlime/
*/

defined( 'ABSPATH' ) or die;

/** the prefix that is applied to any generated table */
define('OCF_TABLE_PREFIX', 'ocf_');
define('OCF_TABLE', 'contact_entries');

////////////////
/// Autoloader
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


// instances must be called after the autoloader script.
use OrganicContactForm\FormGlobalContainer as Container;
use OrganicContactForm\FormShortcodes as Shortcodes;
use OrganicContactForm\FormData as FormData;
use OrganicContactForm\FormSubmission as Submission;
use OrganicContactForm\AdminSettingsPage as AdminSettingsPage;
use OrganicContactForm\AdminDownloadEntries as AdminDownloadEntries;

////////////////
/// Global variables
////////////////
	/** @var Container $ocf_container */
	$ocf_container = new Container();

	/**
	 * allow access to the required shortcodes
	 * @var Shortcodes $shortcodes
	 */
	$shortcodes = new Shortcodes();

	if (session_status() == PHP_SESSION_NONE)
		session_start();

	$_SESSION['error_container'] = array();


////////////////
/// Stylesheets and scripts
////////////////

	if ( !function_exists('ocf_load_plugin_scripts' ) ) :

		/**
		 * Load the scripts required in the plugin
		 */
		add_action( 'wp_enqueue_scripts', 'ocf_load_plugin_scripts' );
		function ocf_load_plugin_scripts() {

			$plugin_url = fsi( plugin_dir_url( __FILE__ ) );


			// styles
			wp_enqueue_style( 'bootstrap', $plugin_url . 'node_modules/bootstrap/dist/css/bootstrap.min.css' );
			wp_enqueue_style( 'ocf_styles', $plugin_url . 'style.css' );


			// scripts
			wp_enqueue_script('parsleyjs' , $plugin_url . 'node_modules/parsleyjs/dist/parsley.js', array('jquery'), false, true );
			wp_enqueue_script( 'ocf_scripts', $plugin_url . 'scripts.js', array('jquery', 'parsleyjs'), false, true );

		}

	endif;

	if ( !function_exists('ocf_load_plugin_admin_scripts' ) ) :

		/**
		 * Load the admin scripts required in the plugin
		 */
		add_action( 'admin_enqueue_scripts', 'ocf_load_plugin_admin_scripts' );
		function ocf_load_plugin_admin_scripts() {

			$plugin_url = fsi( plugin_dir_url( __FILE__ ) );

			// scripts
			wp_enqueue_script('admin-scripts' , $plugin_url . 'admin-scripts.js', array('jquery'), false, true );


			// styles
			wp_enqueue_style('font-awesome', $plugin_url . 'node_modules/@fortawesome/fontawesome-free/css/all.min.css');
			wp_enqueue_style('admin-styles', $plugin_url . 'admin.css');

		}

	endif;



///////////////
/// Form handling
///////////////


	if ( !function_exists( 'ocf_handleFormSubmission' ) ) :

		// if form is submitted without JavaScript
		add_action('init', 'ocf_handleFormSubmission' );

		function ocf_handleFormSubmission() {

			global $ocf_container;

			// add contact form to DB (after validation)
			if (
				isset( $_POST['ocf_submission'] )
				&& wp_verify_nonce( $_POST['ocf_submission'], 'a893y4ygmvpd9y8n7iku3haexinuyfjeg')
			) :

				// make sure all required form data is available in the $_POST request
				if (
					(
						!isset( $_POST['ocf_name'] )
						|| !isset( $_POST['ocf_email'] )
						|| !isset( $_POST['ocf_enquiry'] )
					)
					&& $_SERVER['REQUEST_METHOD'] !== 'POST'
				) return false;

				$ocf_container->setFormData( $_POST );

				/** @var FormData|bool $form_data */
				$form_data = $ocf_container->getFormData();

				// make sure the object instantiated
				if ( $form_data === false )
					return false;

				new Submission( $form_data );

			endif;

		}

	endif;

	add_action('admin_init', function() {

		// Download CSV file on button press, (nonce checks in construct of class)
		new AdminDownloadEntries();

	});


///////////////
/// Error message function
///////////////

	if ( !function_exists('render_error_message') ) :

		/**
		 * If the server has parsed an error message for a form, display it as required
		 */
		function render_error_message( $field, $form_ID ) {


			if (
				isset( $_SESSION['error_container'][$form_ID][$field] )
	            && $_SERVER['REQUEST_METHOD'] === 'POST'
			) :
				echo '<br><span class="message-error"> ' .  $_SESSION['error_container'][$form_ID][$field] .'</span>';
			endif;
		}

	endif;


///////////////
/// Settings pages
///////////////
	new AdminSettingsPage();


///////////////
/// Admin ajax functions
///////////////
	add_action('admin_init', function() {
		new \OrganicContactForm\AdminAjax();
	});

///////////////
/// Widgets
///////////////

	if ( !function_exists('register_ocf_widget') ) :

		/**
		 * Create widget
		 * @see OrganicContactForm\FormWidget
		 */
		add_action( 'widgets_init', 'register_ocf_widget' );
		function register_ocf_widget() {
			register_widget( 'OrganicContactForm\FormWidget' );
		}

	endif;

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

			// prepare the queries array that needs to be looped to run all the queries individually
			$queries = array();

			// make sure the right table will be used in the plugin initialisation
			$queries[] = sprintf(
				'USE %s;',
				DB_NAME
			);

			$table = OCF_TABLE_PREFIX . OCF_TABLE;

			// Handles the core contact form data
			$queries[] = sprintf('
		                    CREATE TABLE IF NOT EXISTS `%s`.`%s`
		                        (
		                            `id_contact_entries` INT NOT NULL AUTO_INCREMENT,
		                            `name` VARCHAR(100) NOT NULL,
		                            `email` VARCHAR(50) NOT NULL,
		                            `tel` VARCHAR(20),
		                            `enquiry` TEXT NOT NULL,
		                            `date` DATETIME NOT NULL,
		                            `ref_page` TEXT NOT NULL,
		                            PRIMARY KEY (`id_contact_entries`)
		                        )
		                    ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;',
				DB_NAME,
				$table
			);

			// Fire off each query, one by one
			foreach ( $queries as $query )
				$wpdb->query( $query );

		}

	endif;


////////////////
/// Utility functions
////////////////
	if ( !function_exists( 'fsi' ) ) :

		/**
		 * Add a trailing slash on to the end of a string
		 * @param string $string
		 *
		 * @return string;
		 */
		function fsi( $string ) {
			return  rtrim( $string, '/') . '/';
		}

	endif;

	if ( !function_exists('dump') ) :

		/**
		 * Echo out a variable within pre tags. Useful for arrays and objects
		 * @param $var mixed
		 */
		function dump( $var ) {
			echo '<pre>' . print_r( $var, true ) . '</pre>';
		}

	endif;


	// remove error messages after they have been presented on the frontend
	add_action('shutdown', function() {
		if ( isset( $_SESSION['error_container'] )  )
			unset( $_SESSION['error_container'] );

		if ( isset( $_SESSION['form_submitted'] )  )
			unset( $_SESSION['form_submitted'] );

		if ( isset( $_SESSION['form_data'] )  )
			unset( $_SESSION['form_data'] );
	});
