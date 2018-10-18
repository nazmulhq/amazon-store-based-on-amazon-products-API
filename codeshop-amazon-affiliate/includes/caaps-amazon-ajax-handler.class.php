<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Search;
use ApaiIO\Operations\Lookup;
use ApaiIO\ApaiIO;						

class Caaps_Amazon_Ajax_Handler {
	
	private static $initiated             = false;
	public static $search_country         = null;
	public static $search_kword           = null;
	public static $sort_by                = null; //If search category 'All' then no sort paramaters work
	public static $search_asin            = null;
	public static $invalid_asins          = array();
	public static $search_type            = null;
	public static $result_page            = 1;
	public static $search_category        = 'All';
	public static $response_group         = array( 'Small', 'OfferFull', 'ItemAttributes', 'Images');
	public static $transient_mode         = true;
	public static $transientexpire_hours  = 24;
	
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}				
		
		// Update cache duration as set
		$display_options = get_option('caaps_amazon-product-shop-displayoptions');
		$cachedays       = $display_options['caaps_displayoptions_field_cachedays'];
		if ( isset( $cachedays ) && is_numeric( $cachedays ) ) {
			self::$transientexpire_hours = $cachedays * 24; // Convert days into hours
		}
		
	}
	
	private static function initiate_hooks() {		
		  add_action('admin_enqueue_scripts', array( __CLASS__, 'admin_required_scripts') );	
		  add_action('wp_ajax_caaps_searchby_kword_display', array( __CLASS__, 'searchby_kword_display') );	
		  add_action('wp_ajax_caaps_searchby_asin_display', array( __CLASS__, 'searchby_asin_display') );
		  add_action('wp_ajax_caaps_add_selected_products', array( __CLASS__, 'add_selected_products') );	
		  add_action('wp_ajax_caaps_remove_selected_products', array( __CLASS__, 'remove_selected_products') );	
		  add_action('wp_ajax_caaps_test_api_settings', array( __CLASS__, 'test_api_settings') );	
		  self::$initiated = true;
	}	
			
	public static function admin_required_scripts() {
		// get current admin screen
		global $pagenow;
		$screen = get_current_screen();
		if ( is_object($screen ) ) {
			if ( in_array( $screen->post_type, array( 'amazonproductshop') ) ) {				
			    $loading_img = '<img src="' . esc_url( plugins_url( 'public/images/loader.gif', dirname(__FILE__) ) ) . '" alt="Loading" /> ';
				wp_enqueue_style('caaps_style', plugins_url( '../admin/css/codeshop-styles.css', __FILE__ ) , array(), AMZONPRODUCTSHOP_VERSION, 'all' );
				wp_enqueue_script('caaps_metabox_script', plugins_url( '../admin/js/amazon-product-shop.js', __FILE__ ) , array('jquery' ), AMZONPRODUCTSHOP_VERSION, true);
				// localize script
				$nonce = wp_create_nonce( 'caaps_wpnonce' );
				wp_localize_script(
					'caaps_metabox_script',
					'caaps_metabox_script_obj',
					array(
						'adminajax_url'                  => admin_url('admin-ajax.php'),
						'nonce'                          => $nonce, 
						'current_screenid'               => $screen->id,
						'current_posttype'               => $screen->post_type,
						'current_pagenow'                => $pagenow,
						'added_products_msg'             => __( 'Added products successfully.', 'codeshop-amazon-affiliate'),
						'removed_products_msg'           => __( 'Removed selected products successfully.', 'codeshop-amazon-affiliate'),
						'adding_products_msg'            => __( 'Adding selected products...Please wait.', 'codeshop-amazon-affiliate'),
						'removing_products_msg'          => __( 'Removing selected products...Please wait.', 'codeshop-amazon-affiliate'),
						'no_products_selected_msg'       => __( 'No products selected to add.', 'codeshop-amazon-affiliate'),
						'no_products_removeselected_msg' => __( 'No products selected to remove.', 'codeshop-amazon-affiliate'),
						'product_searching_msg'          => __( 'Searching Products...Please Wait.', 'codeshop-amazon-affiliate'),
						'sort_by'                        => __( 'Sort Results', 'codeshop-amazon-affiliate'),
						'product_searching_loadimage'    =>    $loading_img
					)
				);
			}
		}		
	}
		
	public static function searchby_kword_display() {
		check_ajax_referer( 'caaps_wpnonce', 'security' );
		//self::$search_country = $_POST['search_country'];		
		self::$search_category = ( ! isset ( $_POST['search_category'] ) || empty( $_POST['search_category'] ) )? 'All' : $_POST['search_category'];
		// When search category 'All' then no sorting option available - If sort_by empty then assign 'null' value
		self::$sort_by = ( self::$search_category == 'All' )? null : ( empty( $_POST['sort_by'] )? null : trim( $_POST['sort_by'] ) );
		self::$search_kword = $_POST['search_kword'];
		self::$search_type = 'itemsearch';
		self::$result_page = intval($_POST['result_page']);
		if ( empty( self::$search_kword ) ) {
			printf( '<div class="notice notice-warning"><h4>' . __('Search Keyword Required. Please input your search text / keyword to get results.', 'codeshop-amazon-affiliate') . '</h4></div>' );
		}
		else {        	
			// Set transient mode search
			if ( isset( self::$transient_mode ) && self::$transient_mode ) {
				$processed_responses = self::amazonsearch_products_transientmode();	
			}
			// Set non transient mode search
			else {				
				$processed_responses = self::amazonsearch_products();
			}												
			// Display products
			if ( is_array( $processed_responses ) ) {	
			    // Display thickbox search results
			    if ( isset( $_POST['thickbox_search'] ) && $_POST['thickbox_search'] ) {
					include_once AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/caaps_display_response_thickbox_results.php';
				}
				else {
					include_once AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/caaps_display_response_results.php';						
				}
			}
			else {
				_e( $processed_responses, 'codeshop-amazon-affiliate' );
			}							
			
		}
		wp_die();		
	}

	public static function searchby_asin_display() {
		check_ajax_referer( 'caaps_wpnonce', 'security' );
		//self::$search_country = $_POST['search_country'];
		self::$search_type = 'itemlookup';
		$request_asins = $_POST['search_asin'];		
		if ( empty( $request_asins ) ) {
			printf( '<div class="notice notice-warning"><h4>' . __('ASIN(s) Required. Please input your each ASIN per line to get search results.', 'codeshop-amazon-affiliate') . '</h4></div>' );
		}
		else {
			self::$search_asin = preg_split("/[\r\n|\n|\r,\s]+/", $request_asins, -1, PREG_SPLIT_NO_EMPTY );			
			// Set transient mode search
			if ( isset( self::$transient_mode ) && self::$transient_mode ) {
				$processed_responses = self::amazonsearch_products_transientmode();	
			}
			// Set non transient mode search
			else {				
				$processed_responses = self::amazonsearch_products();
			}			
			// Display products
			if ( is_array( $processed_responses ) ) {						
				include_once AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/caaps_display_response_results.php';						
			}
			else {
				_e( $processed_responses, 'codeshop-amazon-affiliate' );
			}				
												
		}	
		wp_die();
	}
	
	public static function initapi_settings() {
		$options = get_option('caaps_amazon-product-shop-settings');		
		//$cc = self::$search_country;
		$cc = $options['caaps_settings_field_country'];
		$accesskeyid = $options['caaps_settings_field_accesskeyid'];
		$secretaccesskey = $options['caaps_settings_field_secretaccesskey'];
		$associateid = $options['caaps_settings_field_associateid'];
		if ( ! isset( $accesskeyid ) || empty( $accesskeyid ) ) {
			wp_die( __('Access Key Required.'), 'codeshop-amazon-affiliate' );
		}
		if ( ! isset( $secretaccesskey ) || empty( $secretaccesskey ) ) {
			wp_die( __('Secret Access Key Required.'), 'codeshop-amazon-affiliate' );
		}
		if ( ! isset( $associateid ) || empty( $associateid ) ) {
			wp_die( __('Associate ID Required.'), 'codeshop-amazon-affiliate' );
		}		
		$conf = new GenericConfiguration();
		$conf
			->setCountry($cc)
			->setAccessKey($accesskeyid)
			->setSecretKey($secretaccesskey)
			->setAssociateTag($associateid)
			->setRequest('\ApaiIO\Request\Soap\Request')
			->setResponseTransformer('\ApaiIO\ResponseTransformer\ObjectToArray');				
		$apa_api = new ApaiIO($conf);
		return $apa_api;
	}
	
	public static function amazonsearch_products() {		
		$apa_api = self::initapi_settings();	
		$processed_responses = array();					
		switch ( self::$search_type ) {
			case 'itemsearch':
				$search = new Search();
				$search->setCategory( self::$search_category );			
				$search->setKeywords( self::$search_kword );
				$search->setPage( self::$result_page );
				$search->setResponsegroup( self::$response_group );
				$response = $apa_api->runOperation( $search );
				$processed_responses = Caaps_Amazon_Response_Process::process_response( $response );				
			break;
			
			case 'itemlookup':			    
				$asins = array_map( array( __CLASS__, 'validate_asins'), self::$search_asin );				
				// Amazon allows maximum 10 asins per request
				if ( count( $asins ) > 10 ) {					
					$chunked_asins = array_chunk( $asins, 10 );
					foreach ( $chunked_asins as $chunk_asin ) {
						$chunk_asin = implode( ',', $chunk_asin );
						$lookup = new Lookup();						
						$lookup->setItemId( $chunk_asin );
						$lookup->setResponseGroup( self::$response_group );				
						$response = $apa_api->runOperation( $lookup );				
						$processed_response = Caaps_Amazon_Response_Process::process_response( $response );						
						$processed_responses = array_merge( $processed_responses, $processed_response );
					}
				}
				else {
					$asins = implode(',', $asins);
					$lookup = new Lookup();
					$lookup->setItemId( $asins );
					$lookup->setResponseGroup( self::$response_group );				
					$response = $apa_api->runOperation( $lookup );				
					$processed_responses = Caaps_Amazon_Response_Process::process_response( $response );					
				}								
			break;
			
		} // End switch
		return $processed_responses;
	}
	
	
	public static function amazonsearch_products_transientmode() {		
		$apa_api = self::initapi_settings();	
		$processed_responses = array();					
		switch ( self::$search_type ) {
			case 'itemsearch':
				$search = new Search();
				$search->setCategory( self::$search_category );			
				$search->setKeywords( self::$search_kword );
				if ( self::$sort_by !== null ) {
					$search->setSort( self::$sort_by );
				}
				$search->setPage( self::$result_page );
				$search->setResponsegroup( self::$response_group );
				$response = $apa_api->runOperation( $search );
				$processed_responses = Caaps_Amazon_Response_Process::process_response( $response );			
				if ( is_array( $processed_responses ) ) {						
					self::transient_amazon_products( $processed_responses );					
				}
			break;
			
			case 'itemlookup':
				$asins = array_map( array( __CLASS__, 'validate_asins'), self::$search_asin );								
				$nontransient_asins = self::skip_transient_products_tocall( $asins );								
				// Amazon allows maximum 10 asins per request
				if ( count( $nontransient_asins ) > 10 ) {					
					$chunked_asins = array_chunk( $nontransient_asins, 10 );
					foreach ( $chunked_asins as $chunk_asin ) {
						$chunk_asin = implode( ',', $chunk_asin );
						$lookup = new Lookup();						
						$lookup->setItemId( $chunk_asin );
						$lookup->setResponseGroup( self::$response_group );				
						$response = $apa_api->runOperation( $lookup );				
						$processed_response = Caaps_Amazon_Response_Process::process_response( $response );						
						$processed_responses = array_merge( $processed_responses, $processed_response );
					}					
				}
				elseif ( count( $nontransient_asins ) > 0 ) {
					$nontransient_asin = implode(',', $nontransient_asins);
					$lookup = new Lookup();
					$lookup->setItemId( $nontransient_asin );
					$lookup->setResponseGroup( self::$response_group );				
					$response = $apa_api->runOperation( $lookup );				
					$processed_responses = Caaps_Amazon_Response_Process::process_response( $response );						
				}
				if ( is_array( $processed_responses ) ) {						
				    self::transient_amazon_products( $processed_responses );
					// Get earlier transient products and merge them with current api call products																				
					$transient_products = self::getall_transient_products( array_diff( $asins, $nontransient_asins) );
					$processed_responses = array_merge( $processed_responses, $transient_products );					
				}
			break;						
		} // End switch
		return $processed_responses;
	}	
	
	public static function transient_amazon_products( $products = array() ) {
		$i = 0;
		while ( $i < count( $products ) ) {
			if ( isset( $products[$i]['ASIN'] ) && ! empty( $products[$i]['ASIN'] ) ) {
				if ( false === get_transient( 'caaps_transient_'.$products[$i]['ASIN'] ) ) {
					set_transient( 'caaps_transient_'.$products[$i]['ASIN'], $products[$i], self::$transientexpire_hours * HOUR_IN_SECONDS );
				}
			}
		$i++;
		}
	}
	
	public static function skip_transient_products_tocall( $asins = array() ) {
		$asins_tocall = array();
		if( isset( $asins ) && count( $asins ) > 0 ) {
			foreach ( $asins as $asin) {
				if ( false === get_transient( 'caaps_transient_'.$asin) ) {
					$asins_tocall[] = $asin;
				}
			}			
		}
		return $asins_tocall;
	}
	
	public static function getall_transient_products ( $asins = array() ) {
		$transient_products = array();
		if ( isset( $asins ) && count( $asins ) > 0 ) {
			foreach ( $asins as $asin ) {				
				if ( $product = get_transient( 'caaps_transient_'.$asin ) ) {
					$transient_products[] = $product;
				}
			}
		}
		return $transient_products;
	}
	
	public static function amazonsearch_products_byasins( $asins = array() ) {		
		$asins = implode( ',', array_map( array( __CLASS__, 'validate_asins'), $asins ) );
		$options = get_option('caaps_amazon-product-shop-settings');
		self::$search_country = $options['caaps_settings_field_country'];		
		$apa_api = self::initapi_settings();
		$lookup = new Lookup();
		$lookup->setItemId( $asins );
		$lookup->setResponseGroup( self::$response_group );				
		$response = $apa_api->runOperation( $lookup );				
		$processed_response = Caaps_Amazon_Response_Process::process_response( $response );
		return $processed_response;
	}
	
	public static function validate_asins( $asin = null ) {
		// Valid asins char ranges A-Z, a-z, 0-9
		if ( ! preg_match("/[^0-9A-Za-z]/i", trim($asin) ) ) {
			return $asin;
		}
		else {
			self::$invalid_asins[] = $asin;
		}
	}
	
	public static function add_selected_products() {
		check_ajax_referer( 'caaps_wpnonce', 'security' );					     		
		if ( isset( $_POST['selected_products'] ) ) {
			$productsto_add = $_POST['selected_products'];			
			$post_id = $_POST['post_id'];			
			if ( isset($post_id) && $post_id ) {
				$meta_key = '_caaps_added_products_'.$post_id;
				$exist_products = get_post_meta( $post_id, $meta_key );
				// Merge with exist products If exist - also remove duplicates
				if ( $exist_products ) {
					$productsto_add = array_unique( array_merge( $exist_products[0], $productsto_add ) );
				}
				$add_products_status = update_post_meta( $post_id, $meta_key, $productsto_add );
				echo $add_products_status;
			}			
		}
	}
	
	public static function remove_selected_products() {
		check_ajax_referer( 'caaps_wpnonce', 'security' );					     		
		if ( isset( $_POST['selected_products'] ) ) {
			$productsto_remove = array_unique( $_POST['selected_products'] );			
			$post_id = $_POST['post_id'];			
			if ( isset( $post_id ) && $post_id ) {
				$meta_key = '_caaps_added_products_'.$post_id;
				$exist_products = get_post_meta( $post_id, $meta_key );
				// Remove selected asins from exist product asins
				if ( $exist_products ) {
					$productsto_remove = array_diff( $exist_products[0], $productsto_remove );
				}
				$removed_products_status = update_post_meta( $post_id, $meta_key, $productsto_remove );
				echo $removed_products_status;
			}			
		}
	}
	
	public static function test_api_settings() {
		check_ajax_referer( 'caaps_wpnonce', 'security' );
		$apa_api = self::initapi_settings();			
		$search = new Search();
		$search->setCategory( self::$search_category );			
		$search->setKeywords( 'softwares' );
		$search->setPage( self::$result_page );
		$search->setResponsegroup( self::$response_group );
		$response = $apa_api->runOperation( $search );
		$processed_response = Caaps_Amazon_Response_Process::process_response( $response );
		print_r( $processed_response );
		wp_die();
	}
							
				
} // End Class
?>