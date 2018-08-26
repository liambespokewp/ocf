<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 10:15
 */

namespace OrganicContactForm;

defined( 'ABSPATH' ) or die;


class FormShortcodes {

	public function __construct() {

		if ( !shortcode_exists( 'contact_form' ) )
			add_shortcode( 'contact_form', array( $this, 'displayForm' ) );

	}

	public function displayForm(){

		global $ocf_container;

		// Must use the container to maintain the unique ID's for pages with
		// repeated pages
		/** @var FormMarkup $forms */
		$form_container = $ocf_container->getFormMarkupContainer();

		// output the HTML
		return $form_container->getNewForm();

	}

}