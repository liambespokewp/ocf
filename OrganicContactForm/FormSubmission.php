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

	/** @var FormData form fields submitted via an OCF form */
	private $formData;

	public function __construct( $form_data ) {

		// make sure data has been validated
		if ( !is_a($form_data, 'OrganicContactForm\FormData') )
			return false;

		$this->formData = $form_data;

		// returns bool if persisted/failed
		return $this->persistToDatabase();

	}


	/**
	 * Add the sanitized form data to the database
	 * @return bool
	 */
	private function persistToDatabase() {

		global $wpdb;
		$form_data = $this->getFormData();

		$table = OCF_TABLE_PREFIX . OCF_TABLE;

		$today = date("Y-m-d H:i:s");

		$name       = $form_data->getName();
		$email      = $form_data->getEmail();
		$enquiry    = $form_data->getEnquiry();
		$tel = $form_data->getTel();

		$data = array(
			'date' => $today,
			'ref_page' => $form_data->getRefPage(),
		);


		$format = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
		);

		if ( $name !== false && !is_null( $name ) ) :
			$data['name'] = $name;
			$format[] = '%s';
		endif;

		if ( $email !== false && !is_null( $email ) ) :
			$data['email'] = $email;
			$format[] = '%s';
		endif;

		if ( $enquiry !== false && !is_null( $enquiry ) ) :
			$data['enquiry'] = $enquiry;
			$format[] = '%s';
		endif;

		if ( $tel !== false && !is_null( $tel ) ) :
			$data['tel'] = $tel;
			$format[] = '%d';
		endif;


		if ( !$wpdb->insert(
			$table,
			$data,
			$format
		) ) return false;

		return true;

	}


	/**
	 * @return FormData
	 */
	public function getFormData() {
		return $this->formData;
	}





}