<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 11:19
 */

namespace OrganicContactForm;

defined( 'ABSPATH' ) or die;


/**
 * Handles all data submissions to the database
 *
 * Class FormSubmission
 * @package OrganicContactForm
 */
class FormSubmission {

	/** @var array form fields submitted via an OCF form */
	private $formData;

	public function __construct( $form_data ) {
		$this->formData = $form_data;
	}


	/**
	 * @return array
	 */
	public function getFormData() {
		return $this->formData;
	}





}