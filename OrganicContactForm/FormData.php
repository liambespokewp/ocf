<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 11:26
 */

namespace OrganicContactForm;

defined( 'ABSPATH' ) or die;

/**
 * Handles the validation of the data to be parsed to the
 * database/handled appropriately
 *
 * Class FormData
 * @package OrganicContactForm
 */
class FormData {

	// expected form data object
	private
		$name,
		$email,
		$tel,
		$enquiry,
		$ref_page;

	/**
	 * FormData constructor.
	 *
	 * @param array $form_data
	 */
	public function __construct( $form_data ) {

		if ( is_array( $form_data ) && isset( $form_data['form_id'] ) ) :
			
			$form_ID = $form_data['form_id'];

			// name of enquirer
			if ( isset( $form_data['ocf_name_' . $form_ID ] ) && !empty( $form_data['ocf_name_' . $form_ID ] ) )
				$this->setName( $form_data['ocf_name_' . $form_ID ] );

			// email of enquirer
			if ( isset( $form_data['ocf_email_' . $form_ID] ) && !empty( $form_data['ocf_email_' . $form_ID] ) )
				$this->setEmail( $form_data['ocf_email_' . $form_ID] );

			// phone number of enquirer
			if ( isset( $form_data['ocf_tel_' . $form_ID] ) )
				$this->setTel( $form_data['ocf_tel_' . $form_ID] );

			// message from enquirer
			if ( isset( $form_data['ocf_enquiry_' . $form_ID] ) && !empty( $form_data['ocf_enquiry_' . $form_ID] ) )
				$this->setEnquiry( $form_data['ocf_enquiry_' . $form_ID] );

			// set the form ref page
			if ( isset( $_SERVER["HTTP_REFERER"] ) && !empty( $_SERVER["HTTP_REFERER"] ) ) :
				// remove the get vars from the URL!
				$purged_url = strtok( $_SERVER["HTTP_REFERER"], '?');
				$this->setRefPage( $purged_url );
			endif;

		endif;

	}

	/**
	 * @return string
	 */
	public function getName() {

		if (
			empty( trim( $this->name ) )
	        || !is_string( $this->name )
		) return null;

		return $this->name;
	}

	/**
	 * @param string $name
	 */
	private function setName( $name ) {
		// handle validation here
		$this->name = sanitize_text_field( $name );
	}

	/**
	 * @return string|null
	 */
	public function getEmail() {

		if (
			empty( trim( $this->email) )
			|| !is_string( $this->email )
		) return null;

		return $this->email;
	}

	/**
	 * @param string $email
	 */
	private function setEmail( $email ) {

		if ( filter_var($email, FILTER_VALIDATE_EMAIL) )
			$this->email = sanitize_email( $email );
	}

	/**
	 * @return int|null
	 */
	public function getTel() {

		return $this->tel;

	}

	/**
	 * @param string $tel
	 */
	private function setTel( $tel ) {

		$tel = trim($tel);


		if ( !empty( $tel ) && preg_match( "/^[0-9 ]+$/", $tel ) )
			$this->tel = (int)sanitize_text_field( $tel );

		elseif ( empty( $tel ) )
			$this->tel = null;

		else
			$this->tel = false;
	}

	/**
	 * @return string
	 */
	public function getEnquiry() {

		if (
			empty( trim( $this->enquiry) )
			|| !is_string( $this->enquiry )
		) return null;

		return $this->enquiry;

	}

	/**
	 * @param string $enquiry
	 */
	private function setEnquiry( $enquiry ) {
		$this->enquiry = sanitize_textarea_field( $enquiry );
	}

	/**
	 * @return string
	 */
	public function getRefPage() {
		return $this->ref_page;
	}

	/**
	 * @param string $ref_page
	 */
	private function setRefPage( $ref_page ) {
		$this->ref_page = $ref_page;
	}

}