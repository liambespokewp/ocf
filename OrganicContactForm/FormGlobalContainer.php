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

/**
 * Contains any class that has properties that need to be maintained between
 * it uses throughout multiple classes
 *
 * Class FormGlobalContainer
 * @package OrganicContactForm
 */
class FormGlobalContainer {

	/** @var \OrganicContactForm\FormMarkup  */
	private $form_container;

	public function __construct() {

		$this->form_container = new FormMarkup();

	}

	/**
	 * @return \OrganicContactForm\FormMarkup
	 */
	public function getFormMarkupContainer() {

		return $this->form_container;

	}

}