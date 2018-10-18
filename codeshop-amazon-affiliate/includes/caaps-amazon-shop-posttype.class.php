<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Caaps_Amazon_Shop_Posttype {
	
	private static $initiated = false;
	
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}
	}
		
	private static function initiate_hooks() {			    				
	    add_action( 'init', array( __CLASS__, 'register_custom_post' ) );
		add_action( 'init', array( __CLASS__, 'register_custom_taxonomy' ) );
		self::$initiated = true;
	}	
	
	public static function register_custom_post() {						 		
		$labels = array(
			'name'                  => __( 'Amazon Products', 'codeshop-amazon-affiliate' ),
			'singular_name'         => __( 'Amazon Product', 'codeshop-amazon-affiliate' ),
			'menu_name'             => __( 'CodeShop', 'codeshop-amazon-affiliate' ),
			'name_admin_bar'        => __( 'CodeShop', 'codeshop-amazon-affiliate' ),
			'add_new'               => __( 'Add New Products', 'codeshop-amazon-affiliate' ),
			'add_new_item'          => __( 'Add New Products', 'codeshop-amazon-affiliate' ),
			'new_item'              => __( 'New Product', 'codeshop-amazon-affiliate' ),
			'edit_item'             => __( 'Edit Product', 'codeshop-amazon-affiliate' ),
			'view_item'             => __( 'View Product', 'codeshop-amazon-affiliate' ),
			'all_items'             => __( 'All Products', 'codeshop-amazon-affiliate' ),
			'search_items'          => __( 'Search Amazon Products', 'codeshop-amazon-affiliate' ),
			'parent_item_colon'     => __( 'Parent Products:', 'codeshop-amazon-affiliate' ),
			'not_found'             => __( 'No products found.', 'codeshop-amazon-affiliate' ),
			'not_found_in_trash'    => __( 'No products found in Trash.', 'codeshop-amazon-affiliate' ),
			'featured_image'        => __( 'Product Cover Image', 'codeshop-amazon-affiliate' ),
			'set_featured_image'    => __( 'Set product image', 'codeshop-amazon-affiliate' ),
			'remove_featured_image' => __( 'Remove product image', 'codeshop-amazon-affiliate' ),
			'use_featured_image'    => __( 'Use as product image', 'codeshop-amazon-affiliate' ),
			'archives'              => __( 'Product archives', 'codeshop-amazon-affiliate' ),
			'insert_into_item'      => __( 'Insert into Product', 'codeshop-amazon-affiliate' ),			
			'uploaded_to_this_item' => __( 'Uploaded to this Product', 'codeshop-amazon-affiliate' ),
			'filter_items_list'     => __( 'Filter products list', 'codeshop-amazon-affiliate' ),
			'items_list_navigation' => __( 'Products list navigation', 'codeshop-amazon-affiliate' ),
			'items_list'            => __( 'Products list', 'codeshop-amazon-affiliate' ),
		);
	 
		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => 'amazon-products', 'with_front' => false ),
			'capability_type'     => 'post',
			'has_archive'         => true,
			'hierarchical'        => false,
			'hierarchical'        => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-products',
			'taxonomies'          => array( 'amazonshop_products_cat' ),
			'exclude_from_search' => false,
			'supports'            => array('title', 'editor', 'thumbnail')
		);
	 
		register_post_type( 'amazonproductshop', $args );										
	    // clear permalinks after post type has been registered
		flush_rewrite_rules();	
	}
	
	public static function register_custom_taxonomy() {
	  $labels = array(
		  'name'              => __('Categories', 'codeshop-amazon-affiliate'),
		  'singular_name'     => __('Category', 'codeshop-amazon-affiliate'),
		  'search_items'      => __('Search Categories', 'codeshop-amazon-affiliate'),
		  'all_items'         => __('All Categories', 'codeshop-amazon-affiliate'),
		  'parent_item'       => __('Parent Category', 'codeshop-amazon-affiliate'),
		  'parent_item_colon' => __('Parent Category:', 'codeshop-amazon-affiliate'),
		  'edit_item'         => __('Edit Category', 'codeshop-amazon-affiliate'),
		  'update_item'       => __('Update Category', 'codeshop-amazon-affiliate'),
		  'add_new_item'      => __('Add New Category', 'codeshop-amazon-affiliate'),
		  'new_item_name'     => __('New Category Name', 'codeshop-amazon-affiliate'),
		  'menu_name'         => __('Categories', 'codeshop-amazon-affiliate'),
	  );
	  $args = array(
	      'public'            => true,
		  'hierarchical'      => true, 
		  'labels'            => $labels,
		  'show_ui'           => true,
		  'show_admin_column' => true,
		  'query_var'         => true,
		  'rewrite'           => array( 'slug' => 'amazon-products-category', 'hierarchical' => true, 'with_front' => false ),
	  );
	  register_taxonomy('amazonshop_products_cat', array( 'amazonproductshop' ), $args);		
	}
	
} // End Class
