<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 16:31
 */

namespace OrganicContactForm;


class AdminDownloadEntries {

	public function __construct() {
		$entries = $this->getEntries();
		$file = $this->arrayToCSV($entries);
	}

	public function getEntries() {

		global $wpdb;

		$table = OCF_TABLE_PREFIX . OCF_TABLE;
		$query = sprintf( 'SELECT * FROM %s', $table );

		return $wpdb->get_results($query);

	}

	private function arrayToCSV($array, $filename = "export.csv", $delimiter=",") {

		// open raw memory as file so no temp files needed, you might run out of memory though
		$f = fopen('php://memory', 'w');

		$headers = array('entry_id', 'name', 'email', 'tel_number', 'enquiry', 'date_sent', 'page_referrer');

		fputcsv($f, $headers, $delimiter);

		// loop over the input array
		foreach ($array as $line) {
			// generate csv lines from the inner arrays
			if( is_object($line) )
				$line = (array) $line;
			fputcsv($f, $line, $delimiter);
		}
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