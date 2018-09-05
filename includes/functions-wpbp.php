<?php
/**
 * Function collections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function wpbp_get_registered_process(){
	return apply_filters( 'wpbp_get_registered_process', get_option( 'wpbp_registered_process', array() ) );
}

function wpbp_get_processor_from_type($type = 'post'){

	$processors = apply_filters( 'wpbp_processors', array(
		'post' => 'WPBP_Posts',
		'user' => 'WPBP_Users',
		'site' => 'WPBP_Sites',
		'term' => 'WPBP_Terms',
		'comment' => 'WPBP_Comments',
	) );

	if( !isset($processors[$type]) ){
		return false;
	}

	return $processors[$type];
}

function wpbp_load_processor($process_id, $args){
	
	if( !isset($args['type']) || ( !$processor = wpbp_get_processor_from_type($args['type']) )){
		wpbp_add_error_message( __('Processor not registered.', 'wpbp') );
		return false;
	}

	if(!class_exists($processor)){
		wpbp_add_error_message( sprintf( __('Processor %s not found.', 'wpbp'), $processor ) );
		return false;
	}

	$p = new $processor;
	return $p->setup_args($process_id, $args);
}

/**
 * Get the batch hooks that have been added and some info about them.
 *
 * @return array
 */
function wpbp_get_all_processor() {
	$registered_process = wpbp_get_registered_process();

	foreach ( $registered_process as $k => $args ) {
		if ( $process_status = get_option( 'wpbp_process_' . $k ) ) {
			$last_run = wpbp_time_ago( $process_status['timestamp'] );
			$status = $process_status['status'];
		} else {
			$last_run = 'never';
			$status = 'new';
		}

		$registered_process[ $k ]['last_run'] = $last_run;
		$registered_process[ $k ]['status'] = $status;
	}

	return $registered_process;
}

/**
 * Template function for showing time ago.
 *
 * @todo Move this to a template functions file.
 *
 * @param  integer $time Timestamp.
 */
function wpbp_time_ago( $time ) {
	return sprintf( _x( '%s ago', 'amount of time that has passed', 'locomotive' ), human_time_diff( $time, current_time( 'timestamp' ) ) );
}