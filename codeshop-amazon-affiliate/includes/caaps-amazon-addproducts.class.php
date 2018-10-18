<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Caaps_Amazon_Addproducts {
	
	private static $initiated = false;
	
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}
	}
	
	private static function initiate_hooks() {			    					    
		add_action('add_meta_boxes', array( __CLASS__, 'addamazon_products' ) );
		self::$initiated = true;
	}	
		
	public static function addamazon_products() {		    	   	    	    
		add_meta_box(
			'caaps_country_metabox',           
			__( 'Select Country', 'codeshop-amazon-affiliate' ),  
			array( __CLASS__, 'choose_country_callback' ), 
			'amazonproductshop'
		); 				
		add_meta_box(
			'caaps_bykword_metabox',           
			__( 'Search By Keyword', 'codeshop-amazon-affiliate' ), 
			array( __CLASS__, 'addby_kword_callback' ), 
			'amazonproductshop'
		); 		
		add_meta_box(
			'caaps_byasin_metabox',           
			__( 'Search By ASIN(s)', 'codeshop-amazon-affiliate' ),  
			array( __CLASS__, 'addby_asin_callback' ), 
			'amazonproductshop'
		); 	

		add_meta_box(
			'caaps_displayresults_metabox',           
			__( 'Products - Search Results', 'codeshop-amazon-affiliate' ),  
			array( __CLASS__, 'display_results_callback' ), 
			'amazonproductshop'
		); 					
		// Is Post edit page		
		if( Caaps_Amazon_Helper::is_post_edit_page() ) { 
			add_meta_box(
				'caaps_addedproducts_display_metabox',           
				__( 'Added Products', 'codeshop-amazon-affiliate' ),  
				array( __CLASS__, 'addedproducts_display_callback' ), 
				'amazonproductshop'
			); 			
		}			
	}
		
	public static function choose_country_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		global $post;
		$countries = Caaps_Amazon_Shop::supported_countries();
		include_once AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/choose_country_callback.php';				
	}

	public static function addby_kword_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		include_once AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/addby_kword_callback.php';				
	}

	public static function addby_asin_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		include_once AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/addby_asin_callback.php';				
	}
	
	public static function display_results_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		echo '<div class="wrap caaps-display-results-metabox"></div>';		
	}
	
	public static function addedproducts_display_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}						
		global $post;		
		include_once AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/addedproducts_display_callback.php';
	}
			
} // End Class
?>