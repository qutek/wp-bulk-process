<?php
/**
 * WPBP_Notices Class.
 *
 * @class       WPBP_Notices
 * @version		1.0
 * @author lafif <hello@lafif.me>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPBP_Notices class.
 */
class WPBP_Notices {

	/**
	 * Stores the list of errors.
	 *
	 * @since 1.0
	 * @var array
	 */
	private $errors = array();

	/**
	 * Stores the list of notices.
	 *
	 * @since 1.0
	 * @var array
	 */
	private $notices = array();

	/**
	 * Stores the list of success.
	 *
	 * @since 1.0
	 * @var array
	 */
	private $success = array();

	/**
	 * Add message
	 */
	public function add_message($type, $message) {
		switch ($type) {
			case 'success':
				$this->success[] = $message;
				break;

			case 'notice':
				$this->notices[] = $message;
				break;

			case 'error':
				$this->errors[] = $message;
				break;
		}
	}

	/**
	 * Get error message
	 */
	public function get_error_messages() {
		return $this->errors;
	}

	/**
	 * Get success message
	 */
	public function get_success_messages() {
		return $this->success;
	}

	/**
	 * Get notice message
	 */
	public function get_notice_messages() {
		return $this->notices;
	}

	public function get_all_messages(){
		return array(
			'success' 	=> $this->get_success_messages(),
			'notices' 	=> $this->get_notice_messages(),
			'errors' 	=> $this->get_error_messages(),
		);
	}

}

/**
 * Add error message
 * @param  [type] $message [description]
 * @return [type]          [description]
 */
function wpbp_add_error_message($message){
	return WPBP()->notices->add_message('error', $message);
}

/**
 * Get error messages
 * @return [type]          [description]
 */
function wpbp_get_error_messages(){
	return WPBP()->notices->get_error_messages();
}

/**
 * Add success message
 * @param  [type] $message [description]
 * @return [type]          [description]
 */
function wpbp_add_success_message($message){
	return WPBP()->notices->add_message('success', $message);
}

/**
 * Get success messages
 * @return [type]          [description]
 */
function wpbp_get_success_messages(){
	return WPBP()->notices->get_success_messages();
}

/**
 * Add notice message
 * @param  [type] $message [description]
 * @return [type]          [description]
 */
function wpbp_add_notice_message($message){
	return WPBP()->notices->add_message('notice', $message);
}

/**
 * Get notice messages
 * @return [type]          [description]
 */
function wpbp_get_notice_messages(){
	return WPBP()->notices->get_notice_messages();
}

function wpbp_get_messages(){
	return WPBP()->notices->get_all_messages();
}

// WPBP_Notices::init();