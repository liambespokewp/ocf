<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 16:31
 */

namespace OrganicContactForm;

defined( 'ABSPATH' ) or die;

/**
 * Handles all functions related to downloading the CSV file of entries
 *
 * Class AdminDownloadEntries
 * @package OrganicContactForm
 */
class AdminDownloadEntries {

	/**
	 * Grab then entries and, if any exist, create a downloadable file. Otherwise
	 * close the tab (should only be accessed from the form)
	 *
	 * AdminDownloadEntries constructor.
	 */
	public function __construct() {

		// if function is not accessed from a form
		if (
			!isset( $_POST['download_entries'] )
			|| !wp_verify_nonce($_POST['download_entries'], '839ytgmhwlcs897tgjhvsbrgyin7kuc' )
		) return;

		$entries = $this->getEntries();

		// Fallback. Kills browser tab if no entries exist.
		if ( empty( $entries ) ) :
			echo "<script>window.close();</script>";
			die;
		endif;

		$this->arrayToCSV($entries);
	}

	/**
	 * Get all the entries from the database (used in CSV download)
	 *
	 * @return array|null|object
	 */
	public function getEntries() {

		global $wpdb;

		$table = OCF_TABLE_PREFIX . OCF_TABLE;
		$query = sprintf( 'SELECT * FROM %s', $table );

		return $wpdb->get_results($query);

	}

	/**
	 * Convert the PHP array|object to a CSV file to download
	 *
	 * @param $array
	 * @param string $filename
	 * @param string $delimiter
	 */
	private function arrayToCSV($array, $filename = "export.csv", $delimiter=",") {

		// open raw memory as file so no temp files needed, you might run out of memory though
		$f = fopen('php://memory', 'w');

		$headers = array('entry_id', 'name', 'email', 'tel_number', 'enquiry', 'date_sent', 'page_referrer');

		fputcsv($f, $headers, $delimiter);

		// loop over the input array

		if ( !empty($array) ) :
			foreach ($array as $line) :

				// generate csv lines from the inner arrays
				if( is_object($line) )
					$line = (array) $line;

				fputcsv($f, $line, $delimiter);

			endforeach;
		endif;

		// reset the file pointer to the start of the file
		fseek($f, 0);
		// tell the browser it's going to be a csv file
		header('Content-Type: application/csv');
		// tell the browser we want to save it instead of displaying it
		header('Content-Disposition: attachment; filename="'.$filename.'";');
		// make php send the generated csv lines to the browser
		fpassthru($f);
	}
}