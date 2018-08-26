<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 14:32
 */

namespace OrganicContactForm;


class AdminSettingsPage {

    private $maxPages, $limit, $table, $pagi;

	public function __construct() {

	    $this->table = OCF_TABLE_PREFIX . OCF_TABLE;

		$this->limit = isset( $_GET['limit'] ) && is_numeric( $_GET['limit'] ) ? (int)$_GET['limit'] : 10;

		if ( $this->limit > 250 )
			$this->limit = 250;

		$this->pagi = isset( $_GET['pagi'] ) && is_numeric( $_GET['pagi'] ) &&  $_GET['pagi'] >= 1 ? (int)$_GET['pagi'] : 1;

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

			<form target="_blank" class="admin-form__download-entries" method="post" action="<?php echo admin_url('admin.php') ?>">
				<input class="button button-primary" type="submit" value="Download CSV of all entries">
                <?php wp_nonce_field('839ytgmhwlcs897tgjhvsbrgyin7kuc', 'download_entries')?>
			</form>

			<form class="admin-form__actions" method="get" action="<?php echo admin_url('admin.php') ?>">
                <label for="limit">Show per page:</label>
				<select name="limit" id="limit">
                    <option value="10" <?php if ( isset( $_GET['limit'] ) && $_GET['limit'] == '10' ) echo ' selected' ;?>>10</option>
                    <option value="25" <?php if ( isset( $_GET['limit'] ) && $_GET['limit'] == '25' ) echo ' selected' ;?>>25</option>
                    <option value="50" <?php if ( isset( $_GET['limit'] ) && $_GET['limit'] == '50' ) echo ' selected' ;?>>50</option>
                    <option value="100" <?php if ( isset( $_GET['limit'] ) && $_GET['limit'] == '100' ) echo ' selected' ;?>>100</option>
                    <option value="250" <?php if ( isset( $_GET['limit'] ) && $_GET['limit'] == '250' ) echo ' selected' ;?>>250</option>
                </select>
                <input class="button" type="submit" value="Apply filter">
                <input type="hidden" name="page" value="ocf_entries">
			</form>

            <?php  $this->renderPagination(); ?>

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

						<?php $entries = $this->getPaginatedEntries(); ?>

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


		    <?php  $this->renderPagination(); ?>

		</div><?php


	}

	/**
     * Get the max availble pages with the current limi
     *
	 * @return float
	 */
	private function getMaxPages() {

	    global $wpdb;

	    if ( isset($this->maxPages) || is_null( $this->maxPages ) ) :

		    $table = $this->getTable();
            $limit = $this->getLimit();

		    $max_pages_query = sprintf('SELECT `id_contact_entries` FROM %s', $table );
		    $this->maxPages = ceil($wpdb->query($max_pages_query)/$limit);
        endif;

        return $this->maxPages;


    }

	/**
	 * @return int
	 */
	private function getLimit() {
	    return $this->limit;
    }

	/**
	 * @return int
	 */
	private function getCurrentPage() {
	    return $this->pagi;
    }

	/**
	 * @return string
	 */
	private function getTable() {
	    return $this->table;
    }

	private function getPaginatedEntries() {

		global $wpdb;

		$table = $this->getTable();
		$limit = $this->getLimit();
		$page = $this->getCurrentPage();
		$max_pages = $this->getMaxPages();

		// make sure the offset doesn't overshoot what is possible
		if ( $page > $max_pages )
		    $page = $max_pages;

		$offset = ($limit * $page) - $limit;
		$query = sprintf( 'SELECT * FROM %s ORDER BY `id_contact_entries` ASC LIMIT %d OFFSET %d', $table, $limit, $offset);

		return $wpdb->get_results($query);

	}


	/**
	 * Create the full output for the Pagination links
	 */
	private function renderPagination() {

	    $max_pages = $this->getMaxPages();
	    $current_page_url = $_SERVER['REQUEST_URI'];
	    $current_page =  isset( $_GET['pagi'] ) ? $_GET['pagi'] : 1;

	    if ( isset( $_GET['pagi'] ) )
		    $current_page_url = remove_query_arg('pagi', $current_page_url );

	    $i = 1;

	    if ( $max_pages > 1 ) : ?>

            <ul class="tablenav-pages ocf-tablenav-pages">
                <?php while ( $i <= $max_pages ) :

                    $pagi_url = add_query_arg( 'pagi', $i, $current_page_url );


	                if ( $current_page == $i ) : ?>
                        <li class="current-pagi"><?php echo $i; ?></li><?php
                    else : ?>
                        <li><a href="<?php echo $pagi_url; ?>"><?php echo $i; ?></a></li><?php
                    endif;

                    $i++;

                endwhile; ?>
            </ul>


        <?php endif;

    }



}