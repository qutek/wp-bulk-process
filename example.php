<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'wpbp_get_registered_process', 'sample_wpbp_process', 10, 1 );
function sample_wpbp_process( $process ){

	$process['foo-user'] = array(
		'name' => 'Sample bulk process for users.',
		'type' => 'user',
		'args' => array(
			'number' => 10,
			'offset' => 0,
			// 'role' => 'seller',
		),
		'callback' => 'foo_add_user_metas',
	);

	$process['foo-posts'] = array(
		'name' => 'Convert Workshop Date to correct format',
		'type' => 'post',
		'args' => array(
			'posts_per_page' => 10,
			'post_type' => 'postxx',
		),
		'callback' => 'convert_workshop_date',
	);

	return $process;
}

function foo_add_user_metas($user){
	update_user_meta( $user->ID, 'test_bulk_process', current_time( 'mysql' ) );
	wpbp_add_success_message( sprintf( __('Added new user meta `test_bulk_process` for user %s (%d)', 'wpbp'), $user->display_name, $user->ID ) );
}

function convert_workshop_date( $post ) {

	update_post_meta( $post->ID, 'test_bulk_process', current_time( 'mysql' ) );
	wpbp_add_success_message( sprintf( __('Added new post meta `test_bulk_process` for post %s (%d)', 'wpbp'), $post->post_title, $post->ID ) );
}