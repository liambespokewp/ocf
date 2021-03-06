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

		$form_submitted_class = '';
		$submission_state = null;

		if ( isset( $_SESSION['form_submitted'][$form_ID] ) ) :

		    $form_submitted = $_SESSION['form_submitted'][$form_ID];

		    if ( $form_submitted === true ) :
			    $form_submitted_class = ' submitted';
		        $submission_state = true;
            else :
	            $form_submitted_class = ' failed-submission';
			    $submission_state = false;
            endif;

        endif;



		// begin output
		ob_start(); ?>



		<form method="post" class="ocf-form" data-parsley-validate="">

			<div class="container">

                <!-- start name field-->
				<div class="row">
                    <div class="col-md-4">
                        <label for="ocf_name_<?php echo $form_ID; ?>">Name <sup>(required)</sup><?php render_error_message('name', $form_ID); ?></label>
                    </div>
                    <div class="col-md-8">
                        <input name="ocf_name_<?php echo $form_ID; ?>" id="ocf_name_<?php echo $form_ID; ?>" type="text" required<?php $this->displayParsedValue('name', $form_ID);?>>
                    </div>
                </div>

                <!-- start email field-->
				<div class="row">
                    <div class="col-md-4">
                        <label for="ocf_email_<?php echo $form_ID; ?>" >Email <sup>(required)</sup><?php render_error_message('email', $form_ID); ?></label>
                    </div>
                    <div class="col-md-8">
                        <input  name="ocf_email_<?php echo $form_ID; ?>" id="ocf_email_<?php echo $form_ID; ?>" type="email" data-parsley-trigger="change" required="" type="email" required<?php $this->displayParsedValue('email', $form_ID);?>>
                    </div>
                </div>

                <!-- start tel field-->
				<div class="row">
                    <div class="col-md-4">
                        <label for="ocf_tel_<?php echo $form_ID; ?>">Tel.<?php render_error_message('tel', $form_ID); ?></label>
                    </div>
                    <div class="col-md-8">
                        <input name="ocf_tel_<?php echo $form_ID; ?>" data-parsley-type="digits" data-parsley-trigger="change" id="ocf_tel_<?php echo $form_ID; ?>" type="tel"<?php $this->displayParsedValue('tel', $form_ID);?>>
                    </div>
                </div>

                <!-- start enquiry field-->
				<div class="row">
                    <div class="col-md-4">
                        <label for="ocf_enquiry_<?php echo $form_ID; ?>">Enquiry <sup>(required)</sup><?php render_error_message('enquiry', $form_ID); ?></label>
                    </div>
                    <div class="col-md-8">
                        <textarea name="ocf_enquiry_<?php echo $form_ID; ?>" id="ocf_enquiry_<?php echo $form_ID; ?>" required><?php $this->displayParsedValue('enquiry', $form_ID);?></textarea>
                    </div>
                </div>

                <!--  start submission button field -->
				<div class="row ocf-submit__container">
                    <div class="col-md-4">
                        <span></span>
                    </div>
                    <div class="col-md-8">
                        <input class="ocf-submit__button<?php echo $form_submitted_class; ?>" type="submit" value="<?php $this->setSubmitText( $submission_state ); ?>">
                    </div>
                </div>

			</div>

            <?php wp_nonce_field('a893y4ygmvpd9y8n7iku3haexinuyfjeg', 'ocf_submission'); ?>
            <input type="hidden" name="form_id" value="<?php echo $this->getFormCount(); ?>" >

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

    private function setSubmitText( $submission_state ) {

	    $message = 'Submit Query';

	    if ( $submission_state === true )
		    $message = 'Thanks for the query!';
	    elseif ( $submission_state === false )
		    $message = 'Try again?';

        echo $message;


    }

    private function displayParsedValue( $field_type, $form_id ) {

	    if ( !isset( $_SESSION['form_data'] ) )
	        return;

	    $message = '';

	    switch ( $field_type ) :

            case 'name' :
                $message = $_SESSION['form_data'][$form_id]['name'];
                break;
            case 'email' :
                $message = $_SESSION['form_data'][$form_id]['email'];
                break;
            case 'tel' :
                $message = $_SESSION['form_data'][$form_id]['tel'];
                break;
            case 'enquiry' :
                $message = $_SESSION['form_data'][$form_id]['enquiry'];
                echo $message;
                return;
                break;

        endswitch;

        echo ' value="' . $message .'"';


    }
}