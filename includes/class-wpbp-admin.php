<?php
/**
 * WPBP_Admin Class.
 *
 * @class       WPBP_Admin
 * @version		1.0.0
 * @author lafif <hello@lafif.me>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPBP_Admin class.
 */
class WPBP_Admin {

	private $menu_id;

    /**
     * Singleton method
     *
     * @return self
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new WPBP_Admin();
        }

        return $instance;
    }

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->includes();

		add_action('admin_menu', array($this, 'add_menu') );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts($hook){

		if($hook != $this->menu_id)
			return;

		wp_enqueue_style( 'wpbp' );
		wp_enqueue_script( 'wpbp' );
		wp_localize_script( 'wpbp', 'WPBP', array(
			'ajax_url' => WPBP()->ajax_url(),
			'nonce' => wp_create_nonce( 'wpbp-ajax' ),
			'start_process_text' => __('Start processing', 'wpbp'),
			'stop_process_text' => __('Stoping process..', 'wpbp'),
		) );
	}

	public function add_menu(){
		$this->menu_id = add_management_page(
			__( 'Bulk Process', 'wpbp' ),
			__( 'Bulk Process', 'wpbp' ),
			WPBP()->capability,
			'wpbp-bulk-process',
			array( $this, 'bulk_process_page' )
		);
	}

	public function bulk_process_page(){
		?>
		<div id="bulk_process_container" class="wpbp-container">
			<div class="wpbp-row">
			  	<div class="wpbp-left">
			     	<div class="wpbp-holder">
				        <h1>
				        	<span class="dashicons dashicons-forms"></span> 
				        	<?php _e('Bulk Update', 'wpbp'); ?>
				        </h1>
				        <div class="wpbp-clear"></div>
				        <hr class="wpbp-hr">
				        <noscript>
				        	<p class="alert error"><?php echo sprintf(__('<strong>%s</strong> Javascript have to be enabled to run the bulk process', 'wpbp'), 'Error'); ?></p>
					    </noscript>
				        <div class="wpbp-content">
							<form>
								<?php  
								$registered_process = wpbp_get_all_processor();

								// echo "<pre>";
								// print_r($registered_process);
								// echo "</pre>";
								
								if(!empty($registered_process)):
								foreach ($registered_process as $process_id => $data) {
								?>
								<p>
								<label class="option">
						          	<input type="radio" id="process_<?php echo $process_id; ?>" name="process" value="<?php echo $process_id; ?>" checked="checked" class="focus">
						          	<span class="radio"></span>
				        		</label>
				        		<label class="option-label" for="process_<?php echo $process_id; ?>"><?php echo $data['name']; ?></label>
								</p>
								<?php 
								}
								else:
								?>
								<p><?php echo __('No bulk process registered, please see the example.php file.', 'textdomain'); ?></p>
								<?php 
								endif;
								?>
								<hr class="wpbp-hr">
								<a href="#" class="start-bulk-process btn btn-green"><?php _e('Process', 'wpbp'); ?></a>
								<a href="#" class="stop-bulk-process btn btn-default hide" style="float:right;"><?php _e('Stop', 'wpbp'); ?></a>
							</form>
				        </div>
			     	</div>
			  	</div>

			  	<div class="wpbp-right">
					<div class="wpbp-holder">
				        <h1><span class="dashicons dashicons-backup"></span> <?php _e('Log', 'wpbp'); ?></h1>
						<div class="wpbp-clear"></div>
				        <hr class="wpbp-hr">
				        <div class="wpbp-content">
							<div class="wpbp_progress pie_progress" role="progressbar">
					          	<div class="pie_progress__number">0%</div>
					          	<div class="pie_progress__label"><?php __('Completed', 'wpbp') ?></div>
					        </div>

							<div>
								<div id="window">>_ Log<span id="persen"></span></div>
								<div id="cmd">
									<div id="cmd_text"></div>
									<!-- <div id="cmd_text">
										<span class="process">Mengambil data produk</span>
										<span class="result success">Produk berhasil diambil</span>
										<span class="process">Mengimport produk</span>
										<span class="result success">Produk berhasil diimport</span>
										<span class="result detail">
											<span class="content-detail">Gambar gagal diupload</span>
										</span>
										<span class="process">Selesai</span>
										<span class="result success">Semua produk berhasil di import.</span>
									</div> -->
								</div>
							</div>
				        </div>
				    </div>
			  	</div>
			</div>
		</div>
		<?php
	}

	public function includes(){
	
	}

}

WPBP_Admin::init();