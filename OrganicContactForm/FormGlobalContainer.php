<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 10:20
 */

namespace OrganicContactForm;

defined( 'ABSPATH' ) or die;

use OrganicContactForm\FormMarkup as FormMarkup;
use OrganicContactForm\FormData as FormData;

/**
 * Contains any class that has properties that need to be maintained between
 * it uses throughout multiple classes in a single request
 *
 * Class FormGlobalContainer
 * @package OrganicContactForm
 */
class FormGlobalContainer {

	/** @var \OrganicContactForm\FormMarkup  */
	private $form_markup_container;

	/** @var \OrganicContactForm\FormData */
	private $form_data;

	public function __construct() {

		// Will keep the form ID's unique
		$this->form_markup_container = new FormMarkup();

		// Keep the form data in a global object
		if ( isset( $_POST['ocf_submission'] )
		     && wp_verify_nonce( $_POST['ocf_submission'], 'a893y4ygmvpd9y8n7iku3haexinuyfjeg' )
		) :
			$this->form_data = new FormData( $_POST );
		endif;

	}

	/**
	 * @return \OrganicContactForm\FormMarkup
	 */
	public function getFormMarkupContainer() {

		return $this->form_markup_container;

	}

	/**
	 * @return \OrganicContactForm\FormData
	 */
	public function getFormData() {

		return $this->form_data;

	}

}