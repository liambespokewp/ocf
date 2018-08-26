<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 09:55
 */

namespace OrganicContactForm;

defined( 'ABSPATH' ) or die;

class FormMarkup {

    private $form_count;

    public function __construct() {
        $this->form_count = 0;
    }

	/**
	 * @return int
	 */
	public function getFormCount() {
		return $this->form_count;
	}

	/**
	 * @param int $form_count
	 */
	public function setFormCount( $form_count ) {
		$this->form_count = (int)$form_count;
	}

	/**
	 * Helps to keep track of the outputted forms on a page (unique ID's etc.)
	 */
    private function increaseFormCount() {
        $current_count = $this->getFormCount();
        $this->setFormCount( $current_count + 1 );
    }

	/**
     * Generate and return the HTML for a new form for the page.
     *
	 * @return string
	 */
	public function getNewForm() {

        // make sure the form ID's are unique
		$this->increaseFormCount();
		$form_ID = $this->getFormCount();

		// begin output
		ob_start(); ?>

		<form method="post">

			<div class="container">

                <!-- start name field-->
				<div class="row">
                    <div class="col-md-4">
                        <label for="ocf_name_<?php echo $form_ID; ?>">Name</label>
                    </div>
                    <div class="col-md-8">
                        <input name="ocf_name" id="ocf_name_<?php echo $form_ID; ?>" type="text" required>
                    </div>
                </div>

                <!-- start email field-->
				<div class="row">
                    <div class="col-md-4">
                        <label for="ocf_email_<?php echo $form_ID; ?>">Email</label>
                    </div>
                    <div class="col-md-8">
                        <input name="ocf_email" id="ocf_email_<?php echo $form_ID; ?>" type="email" required>
                    </div>
                </div>

                <!-- start tel field-->
				<div class="row">
                    <div class="col-md-4">
                        <label for="ocf_tel_<?php echo $form_ID; ?>">Tel.</label>
                    </div>
                    <div class="col-md-8">
                        <input name="ocf_tel" id="ocf_tel_<?php echo $form_ID; ?>" type="tel">
                    </div>
                </div>

                <!-- start enquiry field-->
				<div class="row">
                    <div class="col-md-4">
                        <label for="ocf_enquiry_<?php echo $form_ID; ?>">Enquiry.</label>
                    </div>
                    <div class="col-md-8">
                        <textarea name="ocf_enquiry" id="ocf_enquiry_<?php echo $form_ID; ?>" required></textarea>
                    </div>
                </div>

                <!--  start ajax spinner container/submission field -->
				<div class="row ocf-submit__container">
                    <div class="col-md-4">
                        <span class="ocf-ajax-spinner"></span>
                    </div>
                    <div class="col-md-8">
                        <input class="ocf-submit__button" type="submit">
                    </div>
                </div>

			</div>

            <?php wp_nonce_field('a893y4ygmvpd9y8n7iku3haexinuyfjeg', 'ocf_submission'); ?>

		</form><?php

        // return HTML for handling
		return ob_get_clean();

	}

	/**
	 * Display a new form directly to the page
	 */
	public function displayNewForm() {

        $new_form = $this->getNewForm();

        echo $new_form;

    }
}