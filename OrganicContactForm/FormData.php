<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 11:26
 */

namespace OrganicContactForm;

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
	public function __construct( array $form_data ) {

		if ( is_array( $form_data ) ) :

			// name of enquirer
			if ( isset( $form_data['ocf_name'] ) && !empty( $form_data['ocf_name'] ) )
				$this->setName( $form_data['ocf_name'] );

			// email of enquirer
			if ( isset( $form_data['ocf_email'] ) && !empty( $form_data['ocf_email'] ) )
				$this->setEmail( $form_data['ocf_email'] );

			// phone number of enquirer
			if ( isset( $form_data['ocf_tel'] ) )
				$this->setTel( $form_data['ocf_tel'] );

			// message from enquirer
			if ( isset( $form_data['ocf_enquiry'] ) && !empty( $form_data['ocf_enquiry'] ) )
				$this->setEnquiry( $form_data['ocf_enquiry'] );

			// set the form ref page
			if ( isset( $_SERVER["HTTP_REFERER"] ) && !empty( $_SERVER["HTTP_REFERER"] ) )
				$this->setRefPage( $_SERVER["HTTP_REFERER"] );

		endif;


	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	private function setName( $name ): void {
		// handle validation here
		$this->name = sanitize_text_field( $name );
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	private function setEmail( $email ): void {
		$this->email = sanitize_email( $email );
	}

	/**
	 * @return string
	 */
	public function getTel() {
		return $this->tel;
	}

	/**
	 * @param string $tel
	 */
	private function setTel( $tel ): void {
		$this->tel = sanitize_text_field( $tel );
	}

	/**
	 * @return string
	 */
	public function getEnquiry() {
		return $this->enquiry;
	}

	/**
	 * @param string $enquiry
	 */
	private function setEnquiry( $enquiry ): void {
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
	private function setRefPage( $ref_page ): void {
		$this->ref_page = $ref_page;
	}

}