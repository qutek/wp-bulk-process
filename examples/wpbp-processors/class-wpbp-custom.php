<?php
/**
 * WPBP_Custom Class.
 *
 * @class       WPBP_Custom
 * @version		1.0.0
 * @author lafif <hello@lafif.me>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Bulk_Process Posts class.
 */
class WPBP_Custom extends Bulk_Process {
	/**
	 * The individual batch's parameter for specifying the amount of results to return.
	 *
	 * @var string
	 */
	public $per_batch_param = 'posts_per_page';

	/**
	 * Default args for the query.
	 *
	 * @var array
	 */
	public $default_args = array(
		'post_type'      => 'post',
		'posts_per_page' => 10,
		'offset'         => 0,
	);

	/**
	 * Get results function for the registered batch process.
	 *
	 * @return array \WP_Query->get_posts() result.
	 */
	public function batch_get_results() {
		global $wpdb;

		$per_page = absint( $this->args[ 'posts_per_page' ] );

		$sql = $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS ID, post_type, post_content FROM {$wpdb->posts} ORDER BY ID ASC LIMIT %d OFFSET %d", $per_page, ( $this->current_step - 1 ) * $per_page );

		$items = $wpdb->get_results( $sql );

		$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );

		$this->set_total_num_results( $total );
		return $items;
	}

	/**
	 * Clear the result status for a batch.
	 *
	 * @return bool
	 */
	public function batch_clear_result_status() {
		return delete_post_meta_by_key( $this->process_id . '_status' );
	}

	/**
	 * Get the status of a result.
	 *
	 * @param \WP_Post $result The result we want to get status of.
	 */
	public function get_result_item_status( $result ) {
		return '';  // disable log status
	}

	/**
	 * Update the meta info on a result.
	 *
	 * @param \WP_Post $result  The result we want to track meta data on.
	 * @param string   $status  Status of this result in the batch.
	 */
	public function update_result_item_status( $result, $status ) {
		return true; // disable log status
	}
}
