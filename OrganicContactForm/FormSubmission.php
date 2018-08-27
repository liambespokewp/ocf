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
	private $formData, $submitted;


	public function __construct( $form_data ) {

		$this->submitted = false;

		// make sure data has been validated
		if ( !is_a($form_data, 'OrganicContactForm\FormData') )
			return false;

		$this->formData = $form_data;

		// returns bool if persisted/failed
		return $this->persistToDatabase();

	}

	/**
	 * @return bool
	 */
	public function getSubmittedState() {
		return $this->submitted;
	}


	/**
	 * Add the sanitized form data to the database
	 * @return bool
	 */
	private function persistToDatabase() {

		global $wpdb;

		/** @var \OrganicContactForm\FormData $form_data */
		$form_data = $this->getFormData();

		$table = OCF_TABLE_PREFIX . OCF_TABLE;

		$today = date("Y-m-d H:i:s");

		$name           = $form_data->getName();
		$email          = $form_data->getEmail();
		$enquiry        = $form_data->getEnquiry();
		$tel            = $form_data->getTel();
		$form_id = $_POST['form_id'];

		$data = array(
			'date' => $today,
			'ref_page' => $form_data->getRefPage(),
		);


		$format = array(
			'%s',
			'%s'
		);

		if ( $name !== false && !is_null( $name ) ) :
			$data['name'] = $name;
			$format[] = '%s';
		else :
			$_SESSION['error_container'][$form_id]['name'] = 'Need to supply a valid name';
		endif;

		if ( $email !== false && !is_null( $email ) ) :
			$data['email'] = $email;
			$format[] = '%s';
		else :
			$_SESSION['error_container'][$form_id]['email'] = 'Need to supply a valid email';
		endif;

		if ( $enquiry !== false && !is_null( $enquiry ) ) :
			$data['enquiry'] = $enquiry;
			$format[] = '%s';
		else :
			$_SESSION['error_container'][$form_id]['enquiry'] = 'Need to supply a valid enquiry';
		endif;

		if ( $tel !== false ) :
			$data['tel'] = $tel;
			$format[] = '%d';
		else :
			$_SESSION['error_container'][$form_id]['tel'] = 'Need to supply a valid number';
		endif;


		if (
			!empty( $_SESSION['error_container'] )
			|| !$wpdb->insert(
				$table,
				$data,
				$format
			)
		)  {

			$_SESSION['form_submitted'][$form_id] = false;
			$this->submitted = false;

			$_SESSION['form_data'][$form_id] = array(
				'name' => $form_data->getName(),
				'email' => $form_data->getEmail(),
				'tel' => $form_data->getTel(),
				'enquiry' => $form_data->getEnquiry()
			);
		}

		else {
			$_SESSION['form_submitted'][$form_id] = true;
			$this->submitted = true;

		}

	}


	/**
	 * @return FormData
	 */
	public function getFormData() {
		return $this->formData;
	}





}