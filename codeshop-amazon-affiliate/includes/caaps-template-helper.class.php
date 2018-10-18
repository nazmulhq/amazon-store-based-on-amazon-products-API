<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Caaps_Template_Helper {

	private static $initiated = false;
	public static $currentpage = 1;
	public static $totalpages = 0;
	public static $allproductsnums = 0;
	public static $products_per_page = 12;
	public static $breadcrumbs = array();
	public static $shortcode_asins = array();
	
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}
	}
	
	private static function initiate_hooks() {			    				
	    add_action( 'init', array( __CLASS__, 'amazonshop_rewrite_rules' ) ); 
	    add_filter( 'template_include', array( __CLASS__, 'set_shop_template' ) );
		add_filter( 'frontpage_template', array( __CLASS__, 'set_shop_frontpage' ) );								
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_template_scripts' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'update_pre_get_posts') );
		// on priority 0 to remove 'redirect_canonical' added with priority 10
		add_action( 'template_redirect', array( __CLASS__, 'update_template_redirect' ), 0 );
		add_filter( 'body_class', array( __CLASS__, 'add_body_classes' ) );		
		self::$initiated = true;
	}	
	
	public static function add_body_classes( $classes ) {		
		$newclass = 'caaps-' . get_template();
        return array_merge( $classes, array( $newclass ) );
	}
	public static function update_template_redirect() {	
		 // To make pagination on single post page
		 if ( is_singular( array( 'amazonproductshop', 'post' ) ) ) {	
			global $wp_query;
			$page = ( int ) $wp_query->get( 'page' );
			if ( $page > 1 ) {
				// convert 'page' to 'paged'
				//$query->set( 'page', 1 );
				$query->set( 'paged', $page );
			}
			// prevent redirect
			remove_action( 'template_redirect', 'redirect_canonical' );
		 }
	}
		
	public static function update_pre_get_posts( $query_instance ) {		
		// To work pagination on custom txonomy / archive pages
		if ( $query_instance->is_tax('amazonshop_products_cat') ) { 
			$query_instance->set( 'posts_per_page', -1);
		}
	}
			
	public static function register_template_scripts() {
		//wp_register_script( 'caaps_template_scripts', AMZONPRODUCTSHOP_PLUGIN_DIR . 'amazonshop-templates/assets/js/template-scripts.js', ['jquery'], AMZONPRODUCTSHOP_VERSION, true);				
		$template_styles = array( 'product-one-column.css',
		                          'product-two-columns.css',
		                          'product-three-columns.css',
								  'product-four-columns.css'
								 );   		
		foreach ( $template_styles as $template_style ) {			
			$stylesheet_handler = 'caaps_' . str_replace( '-', '_', basename( $template_style, '.css' ) );				
			$theme_stylesheet_path = '/amazonshop-templates/assets/css/' . $template_style;
			// Use active theme template stylesheet 
			if ( file_exists( get_stylesheet_directory() . $theme_stylesheet_path ) ) {					
				$template_stylesheet_path = get_stylesheet_directory_uri() . $theme_stylesheet_path;
			}
			// Else use default plugin template stylesheet
			else {
				$template_stylesheet_path = plugins_url( '../amazonshop-templates/assets/css/'.$template_style, __FILE__ );
			}
			wp_register_style( $stylesheet_handler, $template_stylesheet_path , array(), AMZONPRODUCTSHOP_VERSION, 'all');	
		}					
	}
	
	public static function set_shop_template( $template_path ) {
	    $qobj = get_queried_object();	    						
		// When single post 'amazonproductshop' post type pages
		if ( isset( $qobj->post_type ) && ( $qobj->post_type == 'amazonproductshop' ) && is_single() ) {
			// check first if template exists in current theme otherwise serve template from plugin directory
			if ( $theme_template = locate_template( 'amazonshop-templates/single-amazonproductshop.php' ) ) {
				$template_path = $theme_template;
			} else {
				$template_path = AMZONPRODUCTSHOP_PLUGIN_DIR . 'amazonshop-templates/single-amazonproductshop.php';
			}
		}		                
		// When custom taxonomy 'amazonshop_products_cat' archive / category pages 
		elseif ( isset( $qobj->taxonomy ) && ( $qobj->taxonomy == 'amazonshop_products_cat' ) && is_archive()  ) {		
			if ( $theme_template = locate_template( 'amazonshop-templates/archive-amazonproductshop.php' ) ) {
				$template_path = $theme_template;
			} else {
				$template_path = AMZONPRODUCTSHOP_PLUGIN_DIR . 'amazonshop-templates/archive-amazonproductshop.php';
			}
		}		
		// When wordpress single page - post type 'page'
		elseif ( is_page( 'amazon-product-shop' )) {		
			if ( $theme_template = locate_template( 'amazonshop-templates/homepage.php' ) ) {
				$template_path = $theme_template;
			} else {
				$template_path = AMZONPRODUCTSHOP_PLUGIN_DIR . 'amazonshop-templates/homepage.php';
			}
		}				
		return $template_path;				
	}
	
	public static function set_shop_frontpage( $template_path ) {														
		$frontpage_id = get_option( 'page_on_front' );
		$amazonshop_frontpageid = get_option( 'caaps_amazonshop_frontpageid' );		
		// Check Amazon Shop is set as front page
		if ( $frontpage_id == $amazonshop_frontpageid ) {
				if ( $theme_template = locate_template( 'amazonshop-templates/homepage.php' ) ) {
					$template_path = $theme_template;
				} else {
					$template_path = AMZONPRODUCTSHOP_PLUGIN_DIR . 'amazonshop-templates/homepage.php';
				}				
		}				
		return $template_path;				
	}
	
	public static function get_post_title() {
		$queried_obj = get_queried_object();				
		if ( isset( $queried_obj->post_title ) ) {
			return $queried_obj->post_title;
		}
		else {
			return;
		}
	}
	
	public static function get_category_title() {
		$queried_obj = get_queried_object();				
		if ( isset( $queried_obj->name ) ) {
			return ucfirst( $queried_obj->name );
		}
		else {
			return;
		}		
	}	

	public static function get_post_content() {
		$queried_obj = get_queried_object();				
		if ( isset( $queried_obj->post_content ) ) {
			return $queried_obj->post_content;
		}
		else {
			return;
		}
	}	
	
	public static function get_amazon_products( $products_per_page = 12 ) {		
	    // If requested products per page not valid number
	    if ( $products_per_page <= 0 ) { $products_per_page = 12;}
		self::$products_per_page = $products_per_page;
		if ( is_page( 'amazon-product-shop' ) ) {
			return self::process_products_forpages( $products_per_page, $request_pagetype = 'homepage' );
		}
		elseif ( is_singular( 'amazonproductshop' ) ) {
			return self::process_products_forpages( $products_per_page, $request_pagetype = 'single' );
		}
		elseif ( is_tax() || is_category() ) {
			return self::process_products_forpages( $products_per_page, $request_pagetype = 'category' );
		}
		// When using Shortcode on built-in 'post' pages
		elseif ( is_singular( array( 'post', 'page' ) ) ) {
			return self::process_products_forpages( $products_per_page, $request_pagetype = 'singlepostorpage' );
		}
	}
	
	public static function create_breadcrumb( $request_pagetype = null ) {		
		$breadcrumbs = array();
		switch ( $request_pagetype ) {
			
			case 'homepage':
                  $breadcrumbs[] = 'Home'; 
				  break;
			
			case 'single':
			      global $post;
			      $breadcrumbs[] = '<a href="'.get_home_url().'" >Home</a>';				  
				  $terminfo = get_the_terms( $post->ID, 'amazonshop_products_cat' );
				  if ( isset( $terminfo) && $terminfo ) {
					  $breadcrumbs[] = '<a href="'.get_term_link( $terminfo[0]->term_id, 'amazonshop_products_cat').'">'.$terminfo[0]->name.'</a>';
				  }
				  // Last active part of breadcrumb as the post title name
				  $breadcrumbs[] = $post->post_title;
			      break;
				  
			// When products added with wordpress default 'post' or 'page' type posts
			case 'singlepostorpage':
			      global $post;
			      $breadcrumbs[] = '<a href="'.get_home_url().'" >Home</a>';				  
				  $terminfo = get_the_category( $post->ID );
				  if ( isset( $terminfo) && $terminfo ) {
					  $breadcrumbs[] = '<a href="'.get_category_link( $terminfo[0]->term_id ).'">'.$terminfo[0]->name.'</a>';
				  }
				  // Last active part of breadcrumb as the post title name
				  $breadcrumbs[] = $post->post_title;
			      break;				  
			
			case 'category':
			    $breadcrumbs[] = '<a href="'.get_home_url().'" >Home</a>';
				$qobj = get_queried_object();
				// Current term info
				$current_terminfo = get_term( $qobj->term_id, 'amazonshop_products_cat' );
				// Get ancestors of current term
				$ancestors = get_ancestors( $qobj->term_id, 'amazonshop_products_cat');
				if ( isset( $ancestors) && count( $ancestors) > 0 ) {
					// Reverse to to make breadcrumb top to bottom order display of categories
					$ancestors_reverse = array_reverse( $ancestors);					
					foreach ( $ancestors_reverse as $ancestor_id) {
						$ancestorinfo  = get_term( $ancestor_id, 'amazonshop_products_cat' );
						$termlink = get_term_link( $ancestor_id, 'amazonshop_products_cat' );
						if ( $termlink ) {
							$breadcrumbs[] = '<a href="'.$termlink.'" >'.$ancestorinfo->name.'</a>';
						}
					}									
				}
				// Add at last of array since visiting category page no clickable
				$breadcrumbs[] = $current_terminfo->name;
			break;
		} // End switch
		// Set to use on templates
		self::$breadcrumbs = $breadcrumbs;				
	}
	
	public static function process_products_forpages( $products_per_page, $request_pagetype = null) {		
		// Hold all added products asins found which will be chunked later based on products per page value
		$all_added_products = array();
		// Get current paged number
		global $wp_query;			
		if ( empty( get_query_var( 'paged') )  || get_query_var( 'paged' ) == 0 ) {
			$paged = 1;
		}
		else {
			$paged = get_query_var( 'paged');
		}
		// Set to use it template pages
		self::$currentpage = $paged;				
		// Chunked array starts from 0 index to hold each chunked asins array to display per page
		$chunked_array_index = $paged - 1;
				
		switch ( $request_pagetype ) {
			
			case 'homepage':
			      self::create_breadcrumb( $request_pagetype );
				  $all_added_products = self::get_alladded_products_asins();				  
				  break;
			
			case 'single':
			      self::create_breadcrumb( $request_pagetype );
				  $all_added_products = self::get_post_products_asins();
			      break;
			
			case 'category':
			      self::create_breadcrumb( $request_pagetype );
				  $all_added_products = self::get_category_products_asins();				  								  			
			break;
			
			case 'singlepostorpage':
			      self::create_breadcrumb( $request_pagetype );
				  $all_added_products = self::$shortcode_asins;
			      break;			
		} // End switch
		
		// Set all added products to use on templates
		self::$allproductsnums = count( $all_added_products );
		// Chunk array based on requested products per page value
		$chunked_array = array_chunk( $all_added_products, $products_per_page );
		// Count total pages based on chunked arrays - set to use on template pages
		self::$totalpages = count( $chunked_array );			
		// If added products asins found then get products info against asins
		if ( isset( $chunked_array[ $chunked_array_index ] ) && count( $chunked_array[ $chunked_array_index ] ) > 0 ) {							            Caaps_Amazon_Ajax_Handler::$search_type = 'itemlookup';
			Caaps_Amazon_Ajax_Handler::$search_asin = $chunked_array[ $chunked_array_index ];
			// Better to reset since $amazon_products used as global variable
			$amazon_products = array(); 
			// Use transient mode to load faster to display added products			
			$amazon_products = Caaps_Amazon_Ajax_Handler::amazonsearch_products_transientmode();
			return $amazon_products;
		}		
		else {
			return;
		}				
	}
	
	public static function get_alladded_products_asins() {
		$all_added_products = array();
		// get all posts ids of post type 'amazonproductshop'
		$args = array( 'post_type'   => 'amazonproductshop',
					   'post_status' => 'publish',
					   'posts_per_page' => -1,
					   'fields' => 'ids'
					 );
		$post_ids = get_posts( $args );				
		// loop over each post ID to get all post meta		
		foreach( $post_ids as $post_id ){
			$meta_key = '_caaps_added_products_'.$post_id;
			$addedproducts = get_post_meta( $post_id, $meta_key );						
			if ( isset( $addedproducts[0] ) && count( $addedproducts[0] ) > 0 ) {
				$all_added_products = array_unique( array_merge( $all_added_products, $addedproducts[0] ) );
			}
		}		
		return $all_added_products;
	}	
	
	
	public static function get_post_products_asins() {		
		// hold all product asins		
		$all_added_products = array();									
		$queried_obj = get_queried_object();
		$post_id = $queried_obj->ID;
		$meta_key = '_caaps_added_products_'.$post_id;
		$addedproducts = get_post_meta( $post_id, $meta_key );		
		// Check if added products asins found
		if ( isset( $addedproducts[0] ) && count( $addedproducts[0] ) > 0 ) {		        
			 $all_added_products = $addedproducts[0];
		}
		return $all_added_products;		
	}	
		
	public static function get_category_products_asins() {
		// hold all product asins		
		$all_added_products = array();		
		// Get all posts belongs requested category
		$qobj = get_queried_object();
		$term_id = $qobj->term_id;
		$args = array( 'posts_per_page' => -1,
					   'post_type' => 'amazonproductshop',
					   'tax_query' => array( array(
												   'taxonomy'  => 'amazonshop_products_cat',
												   'field'     => 'term_id',
												   'terms'     => $term_id,
										           )
									         )
					 );
		$allposts = get_posts( $args );						
		// Check if posts exist then get all posts postmeta asins values
		if ( isset( $allposts ) && count( $allposts ) > 0 ) {	
			foreach ( $allposts as $post ) {
				$meta_key = '_caaps_added_products_'.$post->ID;
				$addedproducts = get_post_meta( $post->ID, $meta_key );						
				if ( isset( $addedproducts[0] ) && count( $addedproducts[0] ) > 0 ) {
					$all_added_products = array_unique( array_merge( $all_added_products, $addedproducts[0] ) );
				}				
			}			
		}
		return $all_added_products;		
	}	
	
	public static function get_amazonshop_pagination( $format_return = 'plain' ) {
		// need an unlikely integer
		$big = 999999999; 
		// When using 'Plain' permalink settings
		if ( ! get_option('permalink_structure') && is_page( 'amazon-product-shop' ) ) {
			// Remove garbage (#038;) which converts ampersand (&) into URL in case of 'Plain' permalink settings
			$base = str_replace( $big, '%#%', str_replace( '#038;', '&', get_pagenum_link( $big ) ) );
			$page = get_page_by_path( 'amazon-product-shop' );
			$page_id = $page->ID;
			$add_args = array( 'page_id' => $page_id );
		}
		elseif ( is_page( 'amazon-product-shop' ) ) {
			$add_args = false;
			$base = get_home_url() . '/amazon-product-shop' . '%_%';
		}
		else {
			$add_args = false;
			$base = str_replace( $big, '%#%', get_pagenum_link( $big ) );
		}

		$translated = __( 'Page', 'codeshop-amazon-affiliate' );		
		$args = array( 'base'               => $base,
			           'format'             => '?paged=%#%',
			           'current'            => max( 1, get_query_var('paged') ),
			           'total'              => self::$totalpages,
					   'add_args'           => $add_args,
					   'type'               => $format_return,
				       'before_page_number' => '<span class="screen-reader-text">'.$translated.' </span>'		
		              );       		
		return paginate_links($args);									
	}
	
	public static function products_result_count() {
		global $amazon_products;
		// Display page total products
		$total_product_nums = count( $amazon_products );
		// All total products found
		$all_totalproduct_nums = self::$allproductsnums;		
		// Get current display page number
		$paged = max( 1, get_query_var('paged') );		
		// Find ptoduct starting from number
		$starting_from = ( $paged * self::$products_per_page + 1 ) - self::$products_per_page;
		// Find product end to number
		$to = ( $total_product_nums < self::$products_per_page )? $all_totalproduct_nums : ( $paged * self::$products_per_page );
		
		if ( $all_totalproduct_nums <= 1 ) {
			_e( sprintf( 'Showing %1$d of %1$d product', $all_totalproduct_nums, 'codeshop-amazon-affiliate' ) );		
		}
		else {
			_e( sprintf( 'Showing %1$d to %2$d of %3$d products', $starting_from, $to, $all_totalproduct_nums, 'codeshop-amazon-affiliate' ) );		
		}
	}
	
	
	public static function get_post_products() {								
		$queried_obj = get_queried_object();
		$post_id = $queried_obj->ID;
		$meta_key = '_caaps_added_products_'.$post_id;
		$addedproducts = get_post_meta( $post_id, $meta_key );		
		// Check if added products asins found
		if ( isset( $addedproducts[0] ) && count( $addedproducts[0] ) > 0 ) {		        
			Caaps_Amazon_Ajax_Handler::$search_type = 'itemlookup';
			Caaps_Amazon_Ajax_Handler::$search_asin = $addedproducts[0];
			// Use transient mode to load faster to display added products
			$processed_responses = Caaps_Amazon_Ajax_Handler::amazonsearch_products_transientmode();			
			return $processed_responses;
		}		
		// No products asins found
		else {
			return;
		}				
	}		
	
	public static function get_category_products() {
		$processed_responses = array();		
		// Get all posts belongs the category ID
		$qobj = get_queried_object();
		$term_id = $qobj->term_id;
		$args = array( 'posts_per_page' => -1,
					   'post_type' => 'amazonproductshop',
					   'tax_query' => array( array(
												   'taxonomy'  => 'amazonshop_products_cat',
												   'field'     => 'term_id',
												   'terms'     => $term_id,
										           )
									         )
					 );
		$allposts = get_posts( $args );				
		
		// Check if posts exist then get all posts postmeta values
		if ( isset( $allposts ) && count( $allposts ) > 0 ) {	
			$all_added_products = array(); // holds all product asins		
			foreach ( $allposts as $post ) {
				$meta_key = '_caaps_added_products_'.$post->ID;
				$addedproducts = get_post_meta( $post->ID, $meta_key );						
				if ( isset( $addedproducts[0] ) && count( $addedproducts[0] ) > 0 ) {
					$all_added_products = array_unique( array_merge( $all_added_products, $addedproducts[0] ) );
				}				
			}			
		}
		
		// Check products asins found then call to get product data
		if ( isset( $all_added_products ) && count( $all_added_products ) > 0 ) {
			Caaps_Amazon_Ajax_Handler::$search_type = 'itemlookup';
			Caaps_Amazon_Ajax_Handler::$search_asin = $all_added_products;
			// Use transient mode to load faster to display added products
			$processed_responses = Caaps_Amazon_Ajax_Handler::amazonsearch_products_transientmode();						
		}
		
		return $processed_responses;			
	}	
		
	public static function get_currentpage_products( $display_products_perpage = 12 ) {
		global $wp_query;			
		if ( empty( get_query_var( 'paged') )  || get_query_var( 'paged' ) == 0 ) {
			$paged = 1;
		}
		else {
			$paged = get_query_var( 'paged');
		}
		self::$currentpage = $paged;
		// Chunked array starts from 0 index to hold each chunked asins array to display per page
		$chunked_array_index = $paged - 1;
		$all_added_products = self::get_alladded_products_asins();
		$chunked_array = array_chunk( $all_added_products, $display_products_perpage);				
		self::$totalpages = count( $chunked_array );
		// If added products asins found then show
		if ( isset( $chunked_array[ $chunked_array_index ] ) && count( $chunked_array[ $chunked_array_index ] ) > 0 ) {		        
			Caaps_Amazon_Ajax_Handler::$search_type = 'itemlookup';
			Caaps_Amazon_Ajax_Handler::$search_asin = $chunked_array[ $chunked_array_index ];
			// Use transient mode to load faster to display added products
			$amazon_products = Caaps_Amazon_Ajax_Handler::amazonsearch_products_transientmode();
			return $amazon_products;
		}		
		else {
			return;
		}		
	}	
	
	public static function get_addedproducts_posts_per_page( $numberposts = 1) {
		$all_added_products = array();
		// get all posts ids of post type 'amazonproductshop'
		$args = array( 'post_type'      => 'amazonproductshop',
					   'post_status'    => 'publish',
					   'numberposts' => $numberposts,
					   'fields'         => 'ids'
					 );
		$post_ids = get_posts( $args );						
		// loop over each post ID to get all post meta		
		foreach( $post_ids as $post_id ){
			$meta_key = '_caaps_added_products_'.$post_id;
			$addedproducts = get_post_meta( $post_id, $meta_key );						
			if ( isset( $addedproducts[0] ) && count( $addedproducts[0] ) > 0 ) {
				$all_added_products = array_unique( array_merge( $all_added_products, $addedproducts[0] ) );
			}
		}		
		return $all_added_products;
	}	
	
					
	public static function caaps_load_template( $template_name = null ) {
		// Load registered stylesheet based on template loading
		$stylesheet_handler = 'caaps_' . str_replace( "-", "_", basename( $template_name, '.php') );
		//print_r( $stylesheet_handler );
		wp_enqueue_style( $stylesheet_handler );        
	   // If found in parent or child theme 'amazonshop-templates' folder
	   if ( $template_path = locate_template( 'amazonshop-templates/'.$template_name ) ) {
		 load_template( $template_path );		 
	   } else {		 
	     // If not found then load from plugin templates		
		 load_template( AMZONPRODUCTSHOP_PLUGIN_DIR . '/amazonshop-templates/'.$template_name );
	   }		
	}
		
	public static function set_product_title( $title = null ) {
		if ( empty( $title) || strlen( $title ) < 1 ) return $title;
		// Get product title display option		
		$title_options = get_option('caaps_amazon-product-shop-displayoptions');
		if ( isset( $title_options['caaps_displayoptions_field_titlelength'] )  && 
					$title_options['caaps_displayoptions_field_titlelength'] > 0 ) {			
					$length = $title_options['caaps_displayoptions_field_titlelength'];
					return mb_strimwidth( $title, 0, $length, '...' ); 
		}		
		return $title;
	}

	public static function set_product_buybutton() {
		// Get product buy button display option		
		$title_options = get_option('caaps_amazon-product-shop-displayoptions');
		if ( isset( $title_options['caaps_displayoptions_field_buybutton'] )  && 
					! empty( $title_options['caaps_displayoptions_field_buybutton'] ) ) {			
					return $title_options['caaps_displayoptions_field_buybutton'];
		}		
		return 'AMAZON BUY';
	}		
	
	public static function amazonshop_rewrite_rules() {
	}
			
} // End class
?>