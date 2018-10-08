<?php
/**
 * Sample use of custom processor
 */

add_action( 'wpbp_loaded', 'load_custom_processor_class' );
function load_custom_processor_class(){
	include_once( 'wpbp-processors/class-wpbp-custom.php' );
}

add_filter( 'wpbp_processors', 'add_custom_processor', 10, 1 );
function add_custom_processor( $processors ){

	$processors[ 'custom' ] = 'WPBP_Custom'; // register custom processor

	return $processors;
}

add_filter( 'wpbp_get_registered_process', 'sample_wpbp_process', 10, 1 );
function sample_wpbp_process( $process ){

	$process['remove-script-tags'] = array(
		'name' => 'Remove script tag from all posts',
		'type' => 'custom',
		'args' => array(
			'posts_per_page' => 10,
			// 'post_type' => 'any',
		),
		'callback' => 'remove_script_tags',
	);

	return $process;
}

function remove_script_tags( $post ) {
	global $wpdb;

	preg_match('#<script(.*?)>(.*?)</script>#is', $post->post_content, $scripts);

	if( !empty($scripts) ){
		$new_content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $post->post_content);
		
		// $update = wp_update_post( array(
		// 	'ID' => $post->ID,
		// 	'post_content' => $new_content
		// ) );

		$wpdb->update( $wpdb->posts,
			array( 
				'post_content' => $new_content 
			),
			array( 'ID' => $post->ID ),
			array( 
				'%s',	// value1
			), 
			array( '%d' ) 
		);

		wpbp_add_success_message( sprintf( '[%s] [%s] Found script `%s`', $post->ID, $post->post_type, htmlspecialchars($scripts[ 0 ]) ) );
	}
}	