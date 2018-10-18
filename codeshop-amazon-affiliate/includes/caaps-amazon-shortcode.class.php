<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Caaps_Amazon_Shortcode {
	
	public static $initiated = false;
	
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}		
	}
	
	public static function initiate_hooks() {
		add_action( 'init', array( __CLASS__, 'caaps_add_shortcodes' ) );
		self::$initiated = true;
	}
	
	public static function caaps_add_shortcodes() {
		add_shortcode( 'caaps', array( __CLASS__, 'caaps_shortcode_func' ) );
	}
	
	public static function caaps_shortcode_func( $atts, $content ) {
		 global $amazon_products;
	     // normalize attribute keys, lowercase
		 $atts = array_change_key_case( (array) $atts, CASE_LOWER);						 
		 if ( isset( $atts['asins'] ) && ! empty( $atts['asins'] ) ) {
			 // Get product ASINs
			 $shortcode_asins = explode( ',', $atts['asins'] );
			 $templates = array( 'product-one-column.php', 
			                     'product-two-columns.php',
								 'product-three-columns.php',
								 'product-four-columns.php' );
			 // If request template name is not set or not available then use default one								 
			 $request_template = ! isset( $atts['template'] )? 'product-one-column.php' : $atts['template'].'.php';		
			 if ( ! in_array( $request_template, $templates ) ) {
				 $request_template = 'product-one-column.php';
			 }	
			 		 
			 // Products per page - default 6 
			 if ( isset( $atts['product_perpage'] ) && ! empty( $atts['product_perpage'] ) && $atts['product_perpage'] > 0 ) {
				 $products_per_page = $atts['product_perpage'];
			 }
			 else {
				 $products_per_page = 6;
			 }
			 
			 Caaps_Template_Helper::$shortcode_asins = $shortcode_asins;
			 $amazon_products = Caaps_Template_Helper::get_amazon_products( $products_per_page );	
			 ob_start();		 
			 //return Caaps_Template_Helper::caaps_load_template( $request_template );  
			 Caaps_Template_Helper::caaps_load_template( $request_template );  
			 return ob_get_clean();
		 }
		 else {
			 return __( 'No products added with post to show.', 'codeshop-amazon-affiliate' );
		 }
	}
} // End class
?>