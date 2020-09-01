<?php
/**
 * Plugin Name: WP Bulk Process
 * Description: Plugin for bulk processing large dataset
 * Author: Lafif Astahdziq
 * Author URI: https://lafif.me
 * Author Email: hello@lafif.me
 * Version: 1.1.0
 * Text Domain: wpbp
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPBulkProcess' ) ) :

/**
 * Main WPBulkProcess Class
 *
 * @class WPBulkProcess
 * @version	1.0.0
 */
final class WPBulkProcess {

	/**
	 * @var string
	 */
	public $version = '1.0.1';

	public $capability = 'manage_options';

	public $notices;

	/**
	 * @var WPBulkProcess The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main WPBulkProcess Instance
	 *
	 * Ensures only one instance of WPBulkProcess is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return WPBulkProcess - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * WPBulkProcess Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'wpbp_loaded' );
	}

	/**
	 * Hook into actions and filters
	 * @since  1.0.0
	 */
	private function init_hooks() {

		$this->notices = new WPBP_Notices();

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'init' ), 0 );

		register_uninstall_hook( __FILE__, 'uninstall' );
	}

	/**
	 * All install stuff
	 * @return [type] [description]
	 */
	public function install() {

		// we did something on install
		do_action( 'on_wpbp_install' );
	}

	/**
	 * All uninstall stuff
	 * @return [type] [description]
	 */
	public function uninstall() {

		// we remove what we did
		do_action( 'on_wpbp_uninstall' );
	}

	/**
	 * Init WPBulkProcess when WordPress Initialises.
	 */
	public function init() {

		// register all scripts
		$this->register_scripts();
	}

	/**
	 * Register all scripts to used on our pages
	 * @return [type] [description]
	 */
	private function register_scripts(){

		wp_register_style( 'wpbp', plugins_url( '/assets/css/wpbp.css', __FILE__ ) );
		wp_register_script( 'asPieProgress', plugins_url( '/assets/js/jquery-asPieProgress.js', __FILE__ ), array('jquery'), '', true );
		wp_register_script( 'wpbp', plugins_url( '/assets/js/wpbp.js', __FILE__ ), array('jquery', 'asPieProgress'), '', true );

		do_action( 'wpbp_register_script' );
 	}

	/**
	 * Define WPBulkProcess Constants
	 */
	private function define_constants() {

		$this->define( 'WPBP_PLUGIN_FILE', __FILE__ );
		$this->define( 'WPBP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'WPBP_VERSION', $this->version );
	}

	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
	public function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		// all public includes
		include_once( 'includes/abstracts/abstract-bulk-process.php' );
		include_once( 'includes/processors/class-wpbp-posts.php' );
		include_once( 'includes/processors/class-wpbp-users.php' );
		include_once( 'includes/processors/class-wpbp-sites.php' );
		include_once( 'includes/processors/class-wpbp-terms.php' );
		include_once( 'includes/processors/class-wpbp-comments.php' );

		include_once( 'includes/class-wpbp-notices.php' );
		include_once( 'includes/functions-wpbp.php' );

		if ( $this->is_request( 'admin' ) ) {
			include_once( 'includes/class-wpbp-admin.php' );
		}

		if ( $this->is_request( 'ajax' ) ) {
			include_once( 'includes/class-wpbp-ajax.php' );
		}

		if ( $this->is_request( 'frontend' ) ) {
		}
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get Ajax URL.
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

}

endif;

/**
 * Returns the main instance of WPBulkProcess to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WPBulkProcess
 */
function WPBP() {
	return WPBulkProcess::instance();
}

WPBP();
