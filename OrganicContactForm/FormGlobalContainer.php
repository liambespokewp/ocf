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

	}

	/**
	 * @return \OrganicContactForm\FormMarkup
	 */
	public function getFormMarkupContainer() {

		return $this->form_markup_container;

	}

	/**
	 * @return bool|\OrganicContactForm\FormData
	 */
	public function getFormData() {

		// if nothing has been set before this is called, return false
		if ( !is_a( $this->form_data, 'OrganicContactForm\FormData' ) )
			return false;

		return $this->form_data;

	}

	/**
	 * @var $form_data array an array containing all the relevant fields to persist
	 * to the database
	 */
	public function setFormData( $form_data ) {
		$this->form_data = new FormData( $form_data );
	}

}