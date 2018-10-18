<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Caaps_Post_Products {
	public static $initiated = false;
	
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}		
	}
	
	private static function initiate_hooks() {
		if ( is_admin() ) {			
			add_action( 'media_buttons', array( __CLASS__, 'caaps_media_button' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_media_scripts') );	
			self::$initiated = true;
		}
	}
	
	public static function caaps_media_button() {
		 $posttype = get_post_type();
		 if ( in_array( $posttype, array( 'post', 'page' ) ) ) {
			 echo '<a class="button" id="caaps_insert_shortcode" title="Add amazon products"><span class="dashicons dashicons-products" style="vertical-align:middle;font-size:16px;"></span>CodeShop</a>';
			 include_once( dirname( __FILE__ ).'/../admin/views/caaps-addproducts-withpost-container.php' );
		 }
	}
	
	public static function admin_media_scripts() {
		    global $pagenow;
			add_thickbox();
			wp_enqueue_media();
			wp_enqueue_script('caaps_addproducts_script', plugins_url( '../admin/js/add-post-products.js', __FILE__ ) , array('jquery'), AMZONPRODUCTSHOP_VERSION, true);
			// localize script
			$loading_img = '<img src="' . esc_url( plugins_url( 'public/images/loader.gif', dirname(__FILE__) ) ) . '" alt="Loading" /> ';
			$nonce = wp_create_nonce( 'caaps_wpnonce' );
			wp_localize_script( 'caaps_addproducts_script', 'caaps_addproducts_script_obj', 
					 array( 'adminajax_url'            => admin_url('admin-ajax.php'),
							'nonce'                    => $nonce, 
							'current_pagenow'          => $pagenow,
							'added_products_msg'       => __( 'Shortcode created successfully.', 'codeshop-amazon-affiliate'),
							'adding_products_msg'      => __( 'Creating Shortcode...Please wait.', 'codeshop-amazon-affiliate'),
							'no_products_selected_msg' => __( 'Please select product to create Shortcode.', 'codeshop-amazon-affiliate'),
							'product_searching_msg'    => __( 'Searching Products...Please Wait.', 'codeshop-amazon-affiliate'),
							'sort_by'                  => __( 'Sort Results', 'codeshop-amazon-affiliate'),
							'product_searching_loadimage' => $loading_img							
						  )
			);
		
	}
		
} // End class
?>