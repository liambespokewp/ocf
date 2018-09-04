<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 14:32
 */

namespace OrganicContactForm;

defined( 'ABSPATH' ) or die;

/**
 * Create and manage the WP Admin settings page for the entries
 *
 * Class AdminSettingsPage
 * @package OrganicContactForm
 */
class AdminSettingsPage {

    private $maxPages, $limit, $table, $pagi;

	/**
	 * AdminSettingsPage constructor.
	 */
	public function __construct() {

	    $this->table = OCF_TABLE_PREFIX . OCF_TABLE;

		$this->limit = isset( $_GET['limit'] ) && is_numeric( $_GET['limit'] ) ? (int)$_GET['limit'] : 10;

		if ( $this->limit > 250 )
			$this->limit = 250;

		$this->pagi = isset( $_GET['pagi'] ) && is_numeric( $_GET['pagi'] ) &&  $_GET['pagi'] >= 1 ? (int)$_GET['pagi'] : 1;

		/**
         * Add admin page to the menu
         *
         * @see AdminSettingsPage->plugin_admin_add_page();
         */
		add_action('admin_menu', array( $this, 'plugin_admin_add_page') );

	}

	/**
	 * Creates a WP admin menu item
	 */
	public function plugin_admin_add_page() {

		add_menu_page(
			'Form Entries',
			'Submitted Form Entries',
			'manage_options',
			'ocf_entries',
			array( $this, 'plugin_options_page')
		);

	}

	/**
	 * Output for the entries table
	 */
	public function plugin_options_page() { ?>

		<div>

			<h2>Manage form entries</h2>


		    <?php $entries = $this->getPaginatedEntries(); ?>


            <?php if ( !empty($entries) ) : ?>
                <form target="_blank" class="admin-form__download-entries" method="post" action="<?php echo admin_url('admin.php') ?>">
                    <input class="button button-primary" type="submit" value="Download CSV of all entries">
                    <?php wp_nonce_field('839ytgmhwlcs897tgjhvsbrgyin7kuc', 'download_entries')?>
                </form>
            <?php endif; ?>

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

            <?php $maxPages = $this->getMaxPages(); ?>
            <?php if ( $maxPages > 1 ) : ?>
                <form>
                    <label for="pagi_input">Jump to page: </label>
                    <input id="pagi_input" type="number" max="<?php echo $maxPages; ?>" min="1" <?php if ( isset( $_GET['pagi'] ) ) : ?>value="<?php echo  $_GET['pagi']; ?>"<?php endif; ?> name="pagi"><span>/<?php echo $maxPages; ?></span>
                    <input type="hidden" name="page" value="ocf_entries">
                    <?php if ( isset( $_GET['limit'] ) ) : ?>
                        <input type="hidden" name="limit" value="<?php echo $_GET['limit']; ?>">
                    <?php endif; ?>
                </form>
            <?php endif; ?>

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
     * The current pagination requested
     *
	 * @return int
	 */
	private function getCurrentPage() {
	    return $this->pagi;
    }

	/**
     * Get the DB table name
     *
	 * @return string
	 */
	private function getTable() {
	    return $this->table;
    }

	/**
     * Get only the required entries to display on the relevant page, based on pagination
     *
	 * @return array|null|object
	 */
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
		$query = sprintf( 'SELECT * FROM %s ORDER BY `id_contact_entries` DESC LIMIT %d OFFSET %d', $table, $limit, $offset);

		return $wpdb->get_results($query);

	}


	/**
	 * Create the full output for the Pagination links
	 */
	private function renderPagination() {

	    // init vars
	    $max_pages = (int)$this->getMaxPages();
	    $current_page_url = $_SERVER['REQUEST_URI'];

	    $current_page =  isset( $_GET['pagi'] )
                         && is_numeric($_GET['pagi'] )
                         && $_GET['pagi'] > 0
            ? (int)$_GET['pagi']
            : 1;


		$min_range = ($current_page - 2);
		$max_range = ($current_page + 2);

		$lower_flex = $min_range - 1;
		$higher_flex = $min_range + 1;


	    // Remove pagi from the URL (this is added on each link, as required)
	    if ( isset( $_GET['pagi'] ) )
		    $current_page_url = remove_query_arg('pagi', $current_page_url );

	    $i = 1;

	    // only display pagination if there are more than one pages needed
	    if ( $max_pages > 1 ) : ?>

            <ul class="tablenav-pages ocf-tablenav-pages">
                <?php while ( $i <= $max_pages ) :

                    if (
                        $i === 1
                        || filter_var(
	                        $i,
                            FILTER_VALIDATE_INT,
                            array(
                                'options' => array(
                                    'min_range' => $min_range,
                                    'max_range' => $max_range
                                )
                            )
                        )
                        || $i === $max_pages
                    ) :

                        $pagi_url = add_query_arg( 'pagi', $i, $current_page_url );

                        // only add link to item if it is not the current page
                        if ( $current_page === $i || ( $current_page === 1 && $i === 1 ) ) : ?>
                            <li class="current-pagi"><?php echo $i; ?></li><?php
                        else : ?>
                            <li><a href="<?php echo $pagi_url; ?>"><?php echo $i; ?></a></li><?php
                        endif;

                    elseif ( $i === 2 || $i === $max_pages - 1) : ?>

                        <li class="ellip-item">&hellip;</li><?php

                    endif;

                    $i++;

                endwhile; ?>
            </ul>


        <?php endif;

    }



}