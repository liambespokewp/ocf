<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 04/09/2018
 * Time: 20:29
 */

namespace OrganicContactForm;


class AdminAjax {

	public function __construct() {

		add_action('wp_ajax_delete_entry', array( $this, 'validateDeleteEntryRequest') );

	}

	/**
	 * Validate the ajax request for deleting entries
	 */
	public function validateDeleteEntryRequest() {

		$form_data = array();
		parse_str( $_POST['form'], $form_data );

		$entry_id = isset( $form_data['entry_id'] )
			? (int)$form_data['entry_id']
			: false;

		$action = '89y4hf8vy78heuwnpoj290hg89hd' . $entry_id;


		if (
			is_int( $entry_id )
			&& wp_verify_nonce( $form_data['delete_entry'], $action )
		) :

			$deleted_row = $this->deleteEntry( $entry_id );

			if ( $deleted_row ) :
				echo json_encode( array(
					'message' => 'row_deleted',
					'entry_id' => $entry_id
				) );
			else :
				echo json_encode( array(
					'message' => 'deletion_failed',
					'entry_id' => $entry_id
				) );
			endif;
			die;

		endif;

		echo json_encode( array(
			'message' => 'form_validation_failed',
			'entry_id' => $entry_id
		) );
		die;

	}

	/**
	 * Remove the entry from the database
	 * @param $entry_id     ID that matches the id_contact_entries field in the entries  table
	 *
	 * @return false|int
	 */
	private function deleteEntry( $entry_id ) {

		global $wpdb;

		$table = OCF_TABLE_PREFIX . OCF_TABLE;

		$where = array(
			'id_contact_entries' => $entry_id
		);

		$where_format = array(
			'%d'
		);

		return $wpdb->delete( $table, $where, $where_format );

	}

}