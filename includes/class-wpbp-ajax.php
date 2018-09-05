<?php
/**
 * WPBP_Ajax Class.
 *
 * @class       WPBP_Ajax
 * @version		1.0.0
 * @author lafif <hello@lafif.me>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPBP_Ajax class.
 */
class WPBP_Ajax {

    /**
     * Singleton method
     *
     * @return self
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new WPBP_Ajax();
        }

        return $instance;
    }

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->includes();

        add_action( 'wp_ajax_run_bulk_process', array($this, 'run_bulk_process') );
        add_action( 'wp_ajax_reset_bulk_process', array( $this, 'reset_bulk_process' ) );
	}

    public function run_bulk_process(){

        $errors = array();

        check_ajax_referer( 'wpbp-ajax', 'nonce' );

        $registered_process = wpbp_get_registered_process();
        $process_id = (isset($_POST['process_id'])) ? esc_attr( $_POST['process_id'] ) : 0;
        if ( empty( $process_id ) || !isset($registered_process[$process_id]) ) {
            wpbp_add_error_message( __( 'Process id not found.', 'wpbp' ) );
        }

        $args = $registered_process[$process_id];
        
        // load processor 
        wpbp_load_processor($process_id, $args);

        $step = absint( $_POST['step'] );

        if ( ! empty( $errors ) ) {
            wp_send_json( array(
                'success' => false,
                'messages' => wpbp_get_messages(),
            ) );
        }

        do_action( 'wpbp_process_' . $process_id, $step );

        /**
         * In case there is no hook attached to `wpbp_process_{process_id}`
         */
        wpbp_add_error_message( __( 'Callback not found.', 'wpbp' ) );
        wp_send_json( array(
            'success' => false,
            'messages' => wpbp_get_messages(),
        ) );

    }

    /**
     * Reset process
     */
    public function reset_bulk_process() {

        check_ajax_referer( 'wpbp-ajax', 'nonce' );

        if ( empty( $_POST['process_id'] ) ) {
            wpbp_add_error_message( __( 'Process not found.', 'wpbp' ) );
        } else {
            $process_id = esc_attr( $_POST['process_id'] );
        }

        if ( ! empty( $errors ) ) {
            wp_send_json( array(
                'success' => false,
                'messages' => wpbp_get_messages(),
            ) );
        }

        do_action( 'wpbp_reset_' . $process_id );

        /**
         * In case there is no hook attached to `wpbp_reset_{process_id}`
         */
        wpbp_add_error_message( __( 'Callback not found.', 'wpbp' ) );
        wp_send_json( array( 
            'success' => false,
            'messages' => wpbp_get_messages(),
        ) );
    }

	public function includes(){
	
	}

}

WPBP_Ajax::init();