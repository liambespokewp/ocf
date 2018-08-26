<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 14:32
 */

namespace OrganicContactForm;


class AdminSettingsPage {

	public function __construct() {

		add_action('admin_menu', array( $this, 'plugin_admin_add_page') );

	}

	public function plugin_admin_add_page() {

		add_menu_page(
			'Form Entries',
			'Submitted Form Entries',
			'manage_options',
			'ocf_entries',
			array( $this, 'plugin_options_page')
		);

	}

	public function plugin_options_page() { ?>

		<div>

			<h2>Manage form entries</h2>

			<form class="admin-form__download-entries" method="post">
				<input class="button button-primary" type="submit" value="Download CSV of entries">
			</form>

			<div class="table__responsive-container">
				<table class="widefat fixed striped posts admin-table table-responsive">
					<thead>
						<tr>
							<th style="width: 100px">ID</th>
							<th>Name</th>
							<th>Email</th>
							<th>Tel</th>
							<th>Enquiry</th>
							<th>Date</th>
							<th>Ref page</th>
						</tr>
					</thead>

					<tbody>

						<?php $entries = $this->getEntries(); ?>
						<?php $date_format = get_option( 'date_format' ); ?>

						<?php foreach ( $entries as $entry ) : ?>

                            <?php // convert the MYSQL date time to match the WordPress output options ?>

						    <?php $formatted_date = \DateTime::createFromFormat( 'Y-m-d H:i:s', $entry->date )->format('jS F Y H:ia'); ?>
							<tr>
								<td><?php echo $entry->id_contact_entries; ?></td>
								<td><?php echo $entry->name; ?></td>
								<td><?php echo $entry->email; ?></td>
								<td><?php echo $entry->tel; ?></td>
								<td><?php echo $entry->enquiry; ?></td>
								<td><?php echo $formatted_date; ?></td>
								<td><a href="<?php echo $entry->ref_page; ?>" target="_blank"><?php echo $entry->ref_page; ?></a></td>
							</tr>

						<?php endforeach; ?>

					</tbody>

					<tfoot>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Email</th>
							<th>Tel</th>
							<th>Enquiry</th>
							<th>Date</th>
							<th>Ref page</th>
						</tr>
					</tfoot>
				</table>

			</div>

		</div><?php


	}

	private function getEntries() {

		global $wpdb;

		$table = OCF_TABLE_PREFIX . OCF_TABLE;
		$query = sprintf( 'SELECT * FROM %s', $table );

		return $wpdb->get_results($query);

	}

	private function getPaginatedEntries() {



	}



}