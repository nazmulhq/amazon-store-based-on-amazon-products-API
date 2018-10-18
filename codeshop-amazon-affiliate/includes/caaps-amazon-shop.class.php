<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Caaps_Amazon_Shop {
	private static $initiated = false;
	
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}
	}
	
	private static function initiate_hooks() {			    				
	    add_action( 'admin_init', array( __CLASS__, 'add_amazonshop_settings' ) );
		add_action( 'admin_init', array( __CLASS__, 'add_amazonshop_displayoptions' ) );		
		add_action( 'admin_menu', array( __CLASS__, 'add_amazonshop_submenu_settings' ) );
		add_action( 'init', array( __CLASS__, 'create_shop_homepage' ) );
		add_action( 'admin_notices', array( __CLASS__, 'caaps_admin_notices' ) );		
		add_action( 'plugins_loaded', array( __CLASS__, 'amazonshop_load_textdomain') );
		add_filter( 'plugin_row_meta',     array( __CLASS__, 'amazonshop_row_link'), 10, 2 );
		self::$initiated = true;
	}
			
	public static function activate_amazonshop() {
		self::check_preactivation_requirements();
		flush_rewrite_rules( true );
		
	}
	
	public static function check_preactivation_requirements() {				
		if ( version_compare( PHP_VERSION, AMZONPRODUCTSHOP_MINIMUM_PHP_VERSION, '<' ) ) {
			wp_die('Minimum PHP Version required: ' . AMZONPRODUCTSHOP_MINIMUM_PHP_VERSION );
		}
        global $wp_version;
		if ( version_compare( $wp_version, AMZONPRODUCTSHOP_MINIMUM_WP_VERSION, '<' ) ) {
			wp_die('Minimum Wordpress Version required: ' . AMZONPRODUCTSHOP_MINIMUM_WP_VERSION );
		}
		if ( ! extension_loaded( 'soap' ) ) {
			wp_die('PHP SOAP extension is not active on your server, it requires before activate plugin!');
		}
	}
	
	public static function amazonshop_load_textdomain() {
		load_plugin_textdomain( 'codeshop-amazon-affiliate', false, AMZONPRODUCTSHOP_PLUGIN_DIR . 'languages/' ); 
	}
		
	public static function add_amazonshop_settings() {
		register_setting( 'caaps_amazon-product-shop-settings', 'caaps_amazon-product-shop-settings' );
		add_settings_section( 'caaps_settings_section', __( 'CodeShop Amazon Affiliate Settings' ), array( __CLASS__, 'settings_section_callback' ), 'caaps_amazon-product-shop-settings' );
		// Access Key Field
		add_settings_field( 'caaps_settings_field_accesskeyid', __( 'Access Key ID', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'settings_section_fields_callback' ), 'caaps_amazon-product-shop-settings', 'caaps_settings_section', $args = array( 'fieldname' => 'accesskey', 'label_for' => 'caaps_settings_field_accesskeyid' ) );
		// Secrect Access Key Field
		add_settings_field( 'caaps_settings_field_secretaccesskey', __( 'Secret Access Key', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'settings_section_fields_callback' ), 'caaps_amazon-product-shop-settings', 'caaps_settings_section', $args = array( 'fieldname' => 'secretaccesskey', 'label_for' => 'caaps_settings_field_secretaccesskey' ) );
		// Associate ID Field
		add_settings_field( 'caaps_settings_field_associateid', __( 'Associate ID', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'settings_section_fields_callback' ), 'caaps_amazon-product-shop-settings', 'caaps_settings_section', $args = array( 'fieldname' => 'associateid', 'label_for' => 'caaps_settings_field_associateid' ) );
		// Country Selection Field
		add_settings_field( 'caaps_settings_field_country', __( 'Select Country', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'settings_section_fields_callback' ), 'caaps_amazon-product-shop-settings', 'caaps_settings_section', $args = array( 'fieldname' => 'selectcountry', 'label_for' => 'caaps_settings_field_country' ) );				
	}

	public static function add_amazonshop_displayoptions() {
		register_setting( 'caaps_amazon-product-shop-displayoptions', 'caaps_amazon-product-shop-displayoptions' );
		add_settings_section( 'caaps_displayoptions_section', __( 'CodeShop Amazon Affiliate Display Options' ), null, 'caaps_amazon-product-shop-displayoptions' );
		// Product Title Length Field
		add_settings_field( 'caaps_settings_field_titlelength', __( 'Products Title Length', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'displayoptions_section_fields_callback' ), 'caaps_amazon-product-shop-displayoptions', 'caaps_displayoptions_section', $args = array( 'fieldname' => 'titlelength', 'label_for' => 'caaps_displayoptions_field_titlelength' ) );		
		// Product Buy Button Text Field
		add_settings_field( 'caaps_settings_field_buybutton', __( 'Products Buy Button Text', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'displayoptions_section_fields_callback' ), 'caaps_amazon-product-shop-displayoptions', 'caaps_displayoptions_section', $args = array( 'fieldname' => 'buybutton', 'label_for' => 'caaps_displayoptions_field_buybutton' ) );		
		
		// Homepage Template Selection Field
		add_settings_field( 'caaps_settings_field_hometemplate', __( 'Homepage Products Display Template', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'displayoptions_section_fields_callback' ), 'caaps_amazon-product-shop-displayoptions', 'caaps_displayoptions_section', $args = array( 'fieldname' => 'hometemplate', 'label_for' => 'caaps_displayoptions_field_hometemplate' ) );
		// Postspage Template Selection Field
		add_settings_field( 'caaps_settings_field_posttemplate', __( 'Posts Page Products Display Template', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'displayoptions_section_fields_callback' ), 'caaps_amazon-product-shop-displayoptions', 'caaps_displayoptions_section', $args = array( 'fieldname' => 'posttemplate', 'label_for' => 'caaps_displayoptions_field_posttemplate' ) );
		// Categorypage Template Selection Field
		add_settings_field( 'caaps_settings_field_categorytemplate', __( 'Category Page Products Display Template', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'displayoptions_section_fields_callback' ), 'caaps_amazon-product-shop-displayoptions', 'caaps_displayoptions_section', $args = array( 'fieldname' => 'categorytemplate', 'label_for' => 'caaps_displayoptions_field_categorytemplate' ) );			
		
		// Template Four Columns Image Size
		add_settings_field( 'caaps_settings_field_fourcolumns', __( 'Set Image Size ( Four Columns Template)', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'displayoptions_section_fields_callback' ), 'caaps_amazon-product-shop-displayoptions', 'caaps_displayoptions_section', $args = array( 'fieldname' => 'fourcolumns', 'label_for' => 'caaps_displayoptions_field_fourcolumns' ) );											
		
		// Template Three Columns Image Size
		add_settings_field( 'caaps_settings_field_threecolumns', __( 'Set Image Size ( Three Columns Template)', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'displayoptions_section_fields_callback' ), 'caaps_amazon-product-shop-displayoptions', 'caaps_displayoptions_section', $args = array( 'fieldname' => 'threecolumns', 'label_for' => 'caaps_displayoptions_field_threecolumns' ) );											

		// Template Two Columns Image Size
		add_settings_field( 'caaps_settings_field_twocolumns', __( 'Set Image Size ( Two Columns Template)', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'displayoptions_section_fields_callback' ), 'caaps_amazon-product-shop-displayoptions', 'caaps_displayoptions_section', $args = array( 'fieldname' => 'twocolumns', 'label_for' => 'caaps_displayoptions_field_twocolumns' ) );											

		// Template One Column Image Size
		add_settings_field( 'caaps_settings_field_onecolumn', __( 'Set Image Size ( One Column Template)', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'displayoptions_section_fields_callback' ), 'caaps_amazon-product-shop-displayoptions', 'caaps_displayoptions_section', $args = array( 'fieldname' => 'onecolumn', 'label_for' => 'caaps_displayoptions_field_onecolumn' ) );											
		
		
		
		// Products API Data Cache Duration 
		add_settings_field( 'caaps_settings_field_cachedays', __( 'Products Cache Duration', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'displayoptions_section_fields_callback' ), 'caaps_amazon-product-shop-displayoptions', 'caaps_displayoptions_section', $args = array( 'fieldname' => 'cachedays', 'label_for' => 'caaps_displayoptions_field_cachedays' ) );		
		// Products API Show Last Cache Duration 
		add_settings_field( 'caaps_settings_field_lastcachetime', __( 'Show Last Cached Time', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'displayoptions_section_fields_callback' ), 'caaps_amazon-product-shop-displayoptions', 'caaps_displayoptions_section', $args = array( 'fieldname' => 'lastcachetime', 'label_for' => 'caaps_displayoptions_field_lastcachetime' ) );											
	}
		
	public static function displayoptions_section_fields_callback( $args = null ) {		
		$options = get_option('caaps_amazon-product-shop-displayoptions');
		//echo '<pre>';
		//print_r($options);
		//echo '</pre>';
		
		switch ($args['fieldname']) {

			case 'titlelength':
				$value = isset( $options[ $args['label_for'] ] )? ( empty( $options[$args['label_for'] ] )? 0 : $options[$args['label_for'] ] ) : 0 ;
				echo '<input type="text" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="'.$value.'" size="25" autocomplete="off" placeholder="Product Title Length" maxlength="10" /> Characters';
				echo '<br/><small>'. __('Product title characters length can be shorten, put any valid positive number, any value equals 0 or less than 0 will show full product title length. Sometimes longer product title names can break products display layout so you can make them shorter as your required.', 'codeshop-amazon-affiliate') .'</small>';			
			break;
			
			case 'buybutton':
				$value = isset( $options[ $args['label_for'] ] )? ( empty( $options[$args['label_for'] ] )? 'AMAZON BUY' : $options[$args['label_for'] ] ) : 'AMAZON BUY' ;
				echo '<input type="text" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="'.$value.'" size="25" autocomplete="off" placeholder="Amazon Buy Button" maxlength="30" />';
				echo '<br/><small>'. __('As default products buy button text AMAZON BUY will be shown to users. Set any other text what you want to show to users.', 'codeshop-amazon-affiliate') .'</small>';			
			break;
			
			
			
			case 'hometemplate':
				$value = isset( $options[ $args['label_for'] ] )? $options[$args['label_for']] : '';									
				$templates = array( 'product-one-column'     => 'One Column Template',
									'product-two-columns'    => 'Two Columns Template',
									'product-three-columns'  => 'Three Columns Template',
									'product-four-columns'   => 'Four Columns Template',
								  );			
				
				// If not selected yet use default 'product-four-columns' template for homepage
				$options[ $args['label_for'] ] = empty( $value ) ? 'product-four-columns' : $value ;
				
				echo '<select id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']">';
				foreach ( $templates as $tslug => $tname ) {
					$selected = isset( $options[ $args['label_for'] ] ) ? selected( $options[ $args['label_for'] ], $tslug, false ) : '';
					
					echo '<option value="'.$tslug.'" ' .$selected.'>'.$tname.'</option>';
				}			
				echo '</select>';
				echo '<br/><small>'. __( 'When you set your homepgae Amazon Shop then all homepage products display will use this template.', 'codeshop-amazon-affiliate') .'</small>';			
			break;
			
			
			case 'posttemplate':
				$value = isset( $options[ $args['label_for'] ] )? $options[$args['label_for']] : '';									
				$templates = array( 'product-one-column'     => 'One Column Template',
									'product-two-columns'    => 'Two Columns Template',
									'product-three-columns'  => 'Three Columns Template',
									'product-four-columns'   => 'Four Columns Template',
								  );			
				// If not selected yet use default 'product-two-columns' template for posts page products
				$options[ $args['label_for'] ] = empty( $value ) ? 'product-two-columns' : $value ;
				
				echo '<select id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']">';
				foreach ( $templates as $tslug => $tname ) {
					$selected = isset( $options[ $args['label_for'] ] ) ? selected( $options[ $args['label_for'] ], $tslug, false) : '';
					echo '<option value="'.$tslug.'" ' .$selected.'>'.$tname.'</option>';
				}			
				echo '</select>';
				echo '<br/><small>'. __( 'All posts page products display will use this template.', 'codeshop-amazon-affiliate') .'</small>';			
			break;

			case 'categorytemplate':
				$value = isset( $options[ $args['label_for'] ] )? $options[$args['label_for']] : '';									
				$templates = array( 'product-one-column'     => 'One Column Template',
									'product-two-columns'    => 'Two Columns Template',
									'product-three-columns'  => 'Three Columns Template',
									'product-four-columns'   => 'Four Columns Template',
								  );			
				// If not selected yet use default 'product-three-columns' template for category page products
				$options[ $args['label_for'] ] = empty( $value ) ? 'product-three-columns' : $value ;
				
				echo '<select id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']">';
				foreach ( $templates as $tslug => $tname ) {
					$selected = isset( $options[ $args['label_for'] ] ) ? selected( $options[ $args['label_for'] ], $tslug, false) : '';
					echo '<option value="'.$tslug.'" ' .$selected.'>'.$tname.'</option>';
				}			
				echo '</select>';
				echo '<br/><small>'. __( 'All category page products display will use this template.', 'codeshop-amazon-affiliate') .'</small>';			
			break;
			
			case 'fourcolumns':
				$value = isset( $options[ $args['label_for'] ] ) && ! empty( $options[ $args['label_for'] ])? $options[ $args['label_for'] ] : 'MediumImage';				
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="SmallImage" ' . checked( $value, 'SmallImage', false ) . ' /> Small ';
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="MediumImage" ' . checked( $value, 'MediumImage', false ) . ' /> Medium ';
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="LargeImage" ' . checked( $value, 'LargeImage', false ) . ' /> Large ';				
			break;			

			case 'threecolumns':
				$value = isset( $options[ $args['label_for'] ] ) && ! empty( $options[ $args['label_for'] ])? $options[ $args['label_for'] ] : 'MediumImage';				
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="SmallImage" ' . checked( $value, 'SmallImage', false ) . ' /> Small ';
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="MediumImage" ' . checked( $value, 'MediumImage', false ) . ' /> Medium ';
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="LargeImage" ' . checked( $value, 'LargeImage', false ) . ' /> Large ';				
			break;			

			case 'twocolumns':
				$value = isset( $options[ $args['label_for'] ] ) && ! empty( $options[ $args['label_for'] ])? $options[ $args['label_for'] ] : 'MediumImage';				
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="SmallImage" ' . checked( $value, 'SmallImage', false ) . ' /> Small ';
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="MediumImage" ' . checked( $value, 'MediumImage', false ) . ' /> Medium ';
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="LargeImage" ' . checked( $value, 'LargeImage', false ) . ' /> Large ';				
			break;			

			case 'onecolumn':
				$value = isset( $options[ $args['label_for'] ] ) && ! empty( $options[ $args['label_for'] ])? $options[ $args['label_for'] ] : 'LargeImage';				
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="SmallImage" ' . checked( $value, 'SmallImage', false ) . ' /> Small ';
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="MediumImage" ' . checked( $value, 'MediumImage', false ) . ' /> Medium ';
				echo '<input type="radio" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="LargeImage" ' . checked( $value, 'LargeImage', false ) . ' /> Large ';				
			break;						
			
			case 'cachedays':
				$value = ( isset( $options[ $args['label_for'] ] ) &&  empty( $options[$args['label_for'] ]) )? 1 : $options[$args['label_for'] ];				
				echo '<input type="text" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="'.$value.'" size="25" autocomplete="off" placeholder="Products Cache Duration" maxlength="5" /> Day(s)';
				echo '<br/><small>'. __('Products API cache data always make faster your website to show products information from your server instead calling API data from remote server, but maximum 1 Day (24 Hours) default cache duration is fine and if you change this value to cache for longer days then better you should check to show last cahced time below so users can understand about display price since amazon products price may change frequently so if you cache longer days then amazon updated price will not be shown as product latest price until cache duarion expired. All cached data will be expired as your setting cached day(s) are over and new products information will be fetched automatically.', 'codeshop-amazon-affiliate') .'</small>';			
			break;
			
			case 'lastcachetime':
				$value = ( ! isset( $options[ $args['label_for'] ] ) )? 0 : 1;
				$checked = checked( $value, 1, false );
				echo '<input type="checkbox" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-displayoptions['.esc_attr($args['label_for']).']" value="1" ' . $checked . ' />';
				echo '<br/><small>'. __('If this box is checked then last cached time will display into front product pages with each product price so users can know the last time display price when it was cached. If last checked information not shown with products then those have been fetched and cached earlier so wait until cache duarion setting above expired then products will be fetched and cached again then last cached time will be shown.', 'codeshop-amazon-affiliate') .'</small>';			
			break;
						
			
		} // End switch
	}
			
	public static function settings_section_callback() {
		include_once( AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/settings-page-amazon-help.php');
	}
	
	public static function settings_section_fields_callback( $args = null ) {		
		$options = get_option('caaps_amazon-product-shop-settings');
		//print_r($options);
		switch ($args['fieldname']) {
			case 'accesskey':
			$value = isset( $options[$args['label_for']] )? $options[$args['label_for']] : '';
			echo '<input type="text" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-settings['.esc_attr($args['label_for']).']" value="'.$value.'" size="100" placeholder="Access Key ID" />';
			break;
			
			case 'secretaccesskey':
			$value = isset( $options[$args['label_for']] )? $options[$args['label_for']] : '';
			echo '<input type="password" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-settings['.esc_attr($args['label_for']).']" value="'.$value.'" size="100" autocomplete="off" placeholder="Secret Access Key" />';
			break;

			case 'associateid':
			$value = isset( $options[$args['label_for']] )? $options[$args['label_for']] : '';
			echo '<input type="text" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-settings['.esc_attr($args['label_for']).']" value="'.$value.'" size="100" placeholder="Associate ID" />';
			break;
			
			case 'selectcountry':
			$countries = self::supported_countries();			
			$value = isset( $options[$args['label_for']] )? $options[$args['label_for']] : '';
			echo '<select id="'.$args['label_for'].'" name="caaps_amazon-product-shop-settings['.esc_attr($args['label_for']).']">';
			foreach ( $countries as $cc => $country) {
				$selected = isset( $options[$args['label_for']] ) ? selected( $options[$args['label_for']], $cc, false) : '';
				echo '<option value="'.$cc.'" ' .$selected.'>'.$country.'</option>';
			}			
			echo '</select>';
			break;						
		}
	}
	
	
	public static function add_amazonshop_submenu_settings() {
		// Submenu - Display Options
		add_submenu_page(
		    'edit.php?post_type=amazonproductshop',
        __( 'CodeShop Amazon Affiliate', 'codeshop-amazon-affiliate' ),
        __( 'Display Options', 'codeshop-amazon-affiliate' ),
            'manage_options',
            'caaps_amazon-product-shop-display-options',
			array( __CLASS__, 'add_amazonshop_submenu_displayoptions_callback' )        
          );
		
		// Submenu - Settings  
		add_submenu_page(
		    'edit.php?post_type=amazonproductshop',
        __( 'CodeShop Amazon Affiliate', 'codeshop-amazon-affiliate' ),
        __( 'Settings', 'codeshop-amazon-affiliate' ),
            'manage_options',
            'caaps_amazon-product-shop-settings',
			array( __CLASS__, 'add_amazonshop_submenu_settings_callback' )        
          );
		  
	}	
						
	public static function add_amazonshop_submenu_displayoptions_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		include_once AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/add_amazonshop_submenu_displayoptions_callback.php';		
	}

		
	public static function add_amazonshop_submenu_settings_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		include_once AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/add_amazonshop_submenu_settings_callback.php';		
	}
	
	
	public static function supported_countries() {
			$countries = array( 'com.br' => 'Brazil',
								'ca'     => 'Canada',
								'cn'     => 'China',
								'fr'     => 'France',
								'de'     => 'Germany',
								'in'     => 'India',
								'it'     => 'Italy',
								'co.jp'  => 'Japan',
								'com.mx' => 'Mexico',
								'es'     => 'Spain',
								'co.uk'  => 'United Kingdom',
								'com'    => 'United States'
			                  );
		return $countries;					  		
	}
	
	public static function searchcategories_withsort_params( $locale = null ) {
		$categories_withsort_params = array();
		switch ( $locale ) {
			case 'com.br':
			$categories_withsort_params['categories'] = array( 'All'         => 'Todos os departmentos',
			                              					   'Books'       => 'Livros',  
										   					   'KindleStore' => 'Loja Kindle',  
										   					   'MobileApps'  => 'Apps e Jogos'
										 					);
															
		    $categories_withsort_params['sortParams']['All']         = array();
		    $categories_withsort_params['sortParams']['Books']       = array( 'relevancerank',
																		   'salesrank',
																		   'price',
																		   '-price',
																		   'reviewrank_authority',
																		   'daterank'
			                                                             );
		    $categories_withsort_params['sortParams']['KindleStore'] = array( 'relevancerank',
																			  'salesrank',
																			  'price',
																			  '-price',
																			  'reviewrank',
																			  'reviewrank_authority',
																			  'daterank'
			                                                               );

		    $categories_withsort_params['sortParams']['MobileApps']  = array( 'relevancerank',
																		     'popularityrank',
																		     'price',
																		     '-price',
																		     'reviewrank'
			                                                                );

			break; // End com.br
			
			case 'ca':
			
			$categories_withsort_params['categories'] = array( 'All'                  => 'All Departments',
			                              					   'Apparel'              => 'Clothing & Accessories',  
										   					   'Automotive'           => 'Automotive',															  			                              					   'Baby'                 => 'Baby',  
															   
										   					   'Beauty'               => 'Beauty',  
			                              					   'Blended'              => 'Blended',  
										   					   'Books'                => 'Books',  
			                              					   'DVD'                  => 'Movies & TV',  
															   
										   					   'Electronics'          => 'Electronics',  
			                              					   'GiftCards'            => 'Gift Cards',  
										   					   'Grocery'              => 'Grocery & Gourmet Food',
															   'HealthPersonalCare'   => 'Health & Personal Care',  
															   
										   					   'Industrial'           => 'Industrial & Scientific',  
															   'Jewelry'              => 'Jewelry',
			                              					   'KindleStore'          => 'Kindle Store',
															   'Kitchen'              => 'Home & Kitchen',
															   
										   					   'LawnAndGarden'        => 'Patio, Lawn & Garden',  
			                              					   'Luggage'              => 'Luggage & Bags',  
			                              					   'Marketplace'          => 'Marketplace',  
			                              					   'MobileApps'           => 'Apps & Games', 
															    
										   					   'Music'               => 'Music',  
			                              					   'MusicalInstruments'   => 'Musical Instruments, Stage & Studio',  
										   					   'OfficeProducts'       => 'Office Products',  
			                              					   'PetSupplies'          => 'Pet Supplies', 
															   
															   'Shoes'                => 'Shoes & Handbags', 
										   					   'Software'             => 'Software',  
			                              					   'SportingGoods'        => 'Sports & Outdoors',
															   'Tools'                => 'Tools & Home Improvement',  
															   
			                              					   'Toys'                 => 'Toys & Games',  
										   					   'VideoGames'           => 'Video Games', 
															   'Watches'              => 'Watches'															   															   
										 					);
															
		    $categories_withsort_params['sortParams']['All'] = array();
		    $categories_withsort_params['sortParams']['Apparel']       =   array(   'salesrank',
																		   		    'popularityrank',
																		   		    'price',
																		   			'-price',
																					'relevancerank',
																		        	'reviewrank',
																					'-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['Automotive']    =   array(   'salesrank',
			                                                                    	'date-desc-rank',
																		   			'price',
																		   			'-price',
																					'relevancerank',
																					'reviewrank',
																					'reviewrank_authority'
																				
			                                                             );

		    $categories_withsort_params['sortParams']['Baby']         =   array('salesrank',
			                                                                    'relevancerank',
																		   		'price',
																		   		'-price',
																				'reviewrank',
																				'reviewrank_authority'

			                                                             );
		    $categories_withsort_params['sortParams']['Beauty']       =   array('salesrank',
			                                                                    'relevancerank',
																		   		'price',
																		   		'-price',
																				'reviewrank',
																				'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Blended']      = array();
		    $categories_withsort_params['sortParams']['Books']        =   array( 'salesrank',
																				 'pricerank', 
																				 'inverse-pricerank',
																				 'daterank',
																				 'titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['DVD']          =   array( 'salesrank',
																				  'titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['Electronics']  =   array( 'salesrank',
			                                                                     'relevancerank',
																		   		 'price',
																		   		 '-price',
																				 'titlerank', 
																		         '-titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['GiftCards']    =   array( 'popularityrank',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Grocery']     =   array(   'salesrank',
																				  'price',
																				  '-price',			
																				  'date-desc-rank',
																				  'relevancerank',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['HealthPersonalCare']  =   array( 'salesrank',
																						'price',
																						'-price',			
																						'relevancerank',
																						'reviewrank',
																						'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Industrial']      =   array( 'featured',
																					'price',
																					'-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Jewelry']         =   array( 'popularityrank',
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank',
																					'-release-date'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['KindleStore']     =   array(  'salesrank',
																					 'daterank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																				   	 'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Kitchen']         =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																				   	 'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['LawnAndGarden']   =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																				   	 'reviewrank_authority'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Luggage']         =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																				   	 'reviewrank_authority',
																					 'date-desc-rank'
			                                                             );
		    $categories_withsort_params['sortParams']['Marketplace']     =   array(  'salesrank',
																					 'pmrank',
																					 'price',
																					 '-price',
																					 'titlerank',
																					 '-titlerank',																				
																					 '-launch-date',
																					 'relevancerank'
			                                                             );
		    $categories_withsort_params['sortParams']['MobileApps']     =   array(  'popularityrank',
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank'
			                                                             );


		    $categories_withsort_params['sortParams']['Music']           =   array(   'salesrank',
																					  'titlerank',
																					  'orig-rel-date',
																					  '-orig-rel-date',
																					  'releasedate',
																					  '-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['MusicalInstruments']  =   array( 'date-desc-rank',
			                                                                            'salesrank',
																						'relevancerank', 
																		   				'price',
																		   		        '-price',
																				        'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['OfficeProducts']  =   array( 'relevancerank',
			                                                                        'salesrank',
																		   		    'price',
																		   		    '-price',
																		            'reviewrank',
																				    'reviewrank_authority',
																					'date-desc-rank'																					
			                                                             );															

		    $categories_withsort_params['sortParams']['PetSupplies']  =   array( 'salesrank',
																		   		 'price',
																		   		 '-price',
																				 'relevancerank',
																		         'reviewrank',
																				 'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Shoes']        =   array(  'relevancerank',
																				  'salesrank',
																				  'popularityrank',
																				  'reviewrank',
																				  'price',
																				  '-price',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Software']     =   array(  'pricerank',
			                                                                      'inverse-pricerank', 
																				  'salesrank',
																				  '-daterank',
																				  'titlerank'
			                                                             );																		 

		    $categories_withsort_params['sortParams']['SportingGoods'] =   array(   'salesrank', 
																		   		    'price',
																		   		    '-price',
																				    'relevancerank',
																					'reviewrank',
																				    'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Tools']         =   array(   'salesrank', 
																		   		    'price',
																		   		    '-price',
																				    'relevancerank',
																					'reviewrank',
																				    'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Toys']          =   array(   'salesrank', 
																		   		    'price',
																		   		    '-price',
																				    'relevancerank',
																					'reviewrank',
																				    'reviewrank_authority',
																					'date-desc-rank'
			                                                             );

		    $categories_withsort_params['sortParams']['VideoGames']    =   array(   'salesrank',
																					'pricerank',
																					'inverse-pricerank',
																					'titlerank',
																					'-titlerank'
			                                                             );

		    $categories_withsort_params['sortParams']['Watches']      =  array(     'salesrank', 
																		   		    'price',
																		   		    '-price',
																				    'relevancerank',
																					'reviewrank',
																				    'reviewrank_authority'
			                                                             );

			break; // End ca

			case 'cn':
			$categories_withsort_params['categories'] = array( 'All'                  => '全部分类',
			                              					   'Apparel'              => '服饰箱包',  
															   'Appliances'           => '大家电',
										   					   'Automotive'           => '汽车用品',															  			                              					   'Baby'                 => '母婴用品',  
															   
										   					   'Beauty'               => '美容化妆',  
										   					   'Books'                => '图书',  
															   
										   					   'Electronics'          => '电子',  
			                              					   'GiftCards'            => '礼品卡',  
										   					   'Grocery'              => '食品',
															   'HealthPersonalCare'   => '个护健康',  
															   
															   'Home'                 => '家用',
															   'HomeImprovement'      => '家居装修',
															   
															   'Jewelry'              => '珠宝首饰',
			                              					   'KindleStore'          => 'Kindle商店',
															   'Kitchen'              => '厨具',
															   
			                              					   'MobileApps'           => '应用程序和游戏', 
															    
										   					   'Music'                => '音乐',  
			                              					   'MusicalInstruments'   => '乐器',  
										   					   'OfficeProducts'       => '办公用品',  
															   'PCHardware'           => '电脑/IT',
															   
			                              					   'PetSupplies'          => '宠物用品', 
															   'Photo'                => '摄影/摄像',
															   
															   'Shoes'                => '鞋靴', 
										   					   'Software'             => '软件',  
			                              					   'SportingGoods'        => '运动户外休闲',
															   
			                              					   'Toys'                 => '玩具',  
															   'Video'                => '音像',
										   					   'VideoGames'           => '游戏/娱乐', 
															   'Watches'              => '钟表'															   															   
										 					);
															
		    $categories_withsort_params['sortParams']['All'] = array();
		    $categories_withsort_params['sortParams']['Apparel']       =   array(   'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank_authority',
																					'-launch-date',
																					'-pct-off'
																				);
		    $categories_withsort_params['sortParams']['Appliances']    =   array(   'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'-launch-date',
																					'-pct-off'
																				);																				

		    $categories_withsort_params['sortParams']['Automotive']    =   array(   'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'-launch-date',
																					'-pct-off'
																				);																				

		    $categories_withsort_params['sortParams']['Baby']         =   array(    'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority'
																				);		
																																						
		    $categories_withsort_params['sortParams']['Beauty']       =   array(    'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank'
																				);	
																																							
		    $categories_withsort_params['sortParams']['Books']        =   array(  'salesrank',
																				  'pricerank',
																				  'inverse-pricerank',
																				  'daterank',
																				  'titlerank',
																				  '-titlerank',
																				  'price',
																				  '-price',
																				  '-publication_date',
																				  '-unit-sales'
			                                                             );
		    $categories_withsort_params['sortParams']['Electronics']  =   array(  'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'release-date',
																				  '-release-date',
																				  'releasedate',
																				  '-releasedate'			
						                                                        );
		    $categories_withsort_params['sortParams']['GiftCards']    =   array(  'relevancerank',
																				  'popularityrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Grocery']     =   array( 'relevancerank',
																				'salesrank',
																				'price',
																				'-price',
																				'reviewrank'
			                                                                  );
		    $categories_withsort_params['sortParams']['HealthPersonalCare']  =   array(   'salesrank',
																						  'price',
																						  '-price',
																						  'titlerank',
																						  '-titlerank',
																						  'release-date',
																						  '-release-date',
																						  'releasedate',
																						  '-releasedate'
			                                                                           );
		    $categories_withsort_params['sortParams']['Home']        =   array( 'relevancerank',
																				'salesrank',
																				'reviewrank_authority',
																				'reviewrank',
																				'price',
																				'-price'
			                                                                   );
		    $categories_withsort_params['sortParams']['HomeImprovement']  =   array(  'relevancerank',
																					  'salesrank',
																					  'reviewrank_authority',
																					  'reviewrank',
																					  'price',
																					  '-price'
			                                                                   );
																			   
		    $categories_withsort_params['sortParams']['Jewelry']         =   array( 'relevancerank',
																					'salesrank',
																					'reviewrank',
																					'price',
																					'-price'
			                                                                      );
																		 
		    $categories_withsort_params['sortParams']['KindleStore']     =   array(  'salesrank',
																					 'daterank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																				   	 'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Kitchen']         =   array(   'relevancerank',
																					  'popularityrank',
																					  'price',
																					  '-price',
																					  'reviewrank',
																					  '-release-date',
																					  'pct-off'
			                                                             );
		    $categories_withsort_params['sortParams']['MobileApps']      =   array(  'popularityrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Music']           =   array(   'salesrank',
																					  'pricerank',
																					  'price',
																					  '-price',
																					  '-pricerank',
																					  'titlerank',
																					  '-titlerank',
																					  'orig-rel-date',
																					  'releasedate',
																					  '-orig-rel-date',
																					  '-releasedate'
			                                                             );
		    $categories_withsort_params['sortParams']['MusicalInstruments']     =   array(  'relevancerank',
																							'salesrank',
																							'price',
																							'-price',
																							'reviewrank',
																							'reviewrank_authority',
																							'date-desc-rank'
			                                                                              );

		    $categories_withsort_params['sortParams']['OfficeProducts']  =   array( 'relevancerank',
			                                                                        'salesrank',
																		   		    'price',
																		   		    '-price',
																		            'reviewrank'
			                                                                      );															

		    $categories_withsort_params['sortParams']['PCHardware']      =   array( 'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date',
																					'pct-off'
			                                                                     );															

		    $categories_withsort_params['sortParams']['PetSupplies']  =   array(  'relevancerank',
																				  'salesrank',
																				  '-launch-date',
																				  '-pct-off',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Photo']       =   array(   'relevancerank',
																				  'salesrank',
																				  '-launch-date',
																				  '-pct-off',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Shoes']        =   array(  'relevancerank',
																				  'salesrank',
																				  '-launch-date',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Software']     =   array(  'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'release-date',
																				  '-release-date',
																				  'releasedate',
																				  '-releasedate'
			                                                             );																		 

		    $categories_withsort_params['sortParams']['SportingGoods'] =   array( 'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'release-date',
																				  '-release-date'
			                                                             );																		

		    $categories_withsort_params['sortParams']['Toys']          =   array(   'salesrank',
																					'price',
																					'-price',
																					'titlerank',
																					'-titlerank',
																					'release-date',
																					'-release-date',
																					'releasedate',
																					'-releasedate'
			                                                             );	
		    $categories_withsort_params['sortParams']['Video']         =   array( 'salesrank',
																				  'pricerank',
																				  'price',
																				  '-pricerank',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'orig-rel-date',
																				  'releasedate',
																				  '-orig-rel-date',
																				  '-releasedate'
			                                                             );																		
																		 																	
		    $categories_withsort_params['sortParams']['VideoGames']    =   array( 'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'release-date',
																				  '-release-date',
																				  'releasedate',
																				  '-releasedate'
			                                                             );																		

		    $categories_withsort_params['sortParams']['Watches']      =  array( 'salesrank', 
																				'price',
																				'-price',
																				'titlerank',
																				'-titlerank'
			                                                             );

			
			break; // End cn

			case 'fr':
			$categories_withsort_params['categories'] = array( 'All'                  => 'Toutes nos boutiques',
			                              					   'Apparel'              => 'Vêtements et accessoires',  
															   'Appliances'           => 'Gros électroménager',
										   					   'Baby'                 => 'Bébés & Puériculture',  
															   
										   					   'Beauty'               => 'Beauté et Parfum',  
			                              					   'Blended'              => 'Blended',  
										   					   'Books'                => 'Livres en français',  
			                              					   'Classical'            => 'Musique classique', 
															   
															   'DVD'                  => 'DVD & Blu-ray', 
															   
										   					   'Electronics'          => 'High-Tech',  
															   'ForeignBooks'         => 'Livres anglais et étrangers',
			                              					   'GiftCards'            => 'Boutique chèques-cadeaux',  
										   					   'Grocery'              => 'Epicerie',
															   'Handmade'             => 'Handmade',
															   'HealthPersonalCare'   => 'Hygiène et Santé',  
															   
															   'HomeImprovement'      => 'Bricolage',
															   
										   					   'Industrial'           => 'Secteur industriel & scientifique',  
															   'Jewelry'              => 'Bijoux',
			                              					   'KindleStore'          => 'Boutique Kindle',
															   'Kitchen'              => 'Cuisine & Maison',
															   
															   'LawnAndGarden'        => 'Jardin',
															   'Lighting'             => 'Luminaires et Eclairage',
															   
			                              					   'Luggage'              => 'Bagages',  
			                              					   'Marketplace'          => 'Marketplace',  
			                              					   'MobileApps'           => 'Applis & Jeux', 
															   
															   'MP3Downloads'         => 'Téléchargement de musique',
															    
										   					   'Music'                => 'Musique : CD & Vinyles',  
			                              					   'MusicalInstruments'   => 'Instruments de musique & Sono',  
										   					   'OfficeProducts'       => 'Fournitures de bureau',  
															   
															   'PCHardware'           => 'Informatique',
			                              					   'PetSupplies'          => 'Animalerie', 
															   
															   'Shoes'                => 'Chaussures et Sacs', 
										   					   'Software'             => 'Logiciels',  
			                              					   'SportingGoods'        => 'Sports et Loisirs',
															   
			                              					   'Toys'                 => 'Jeux et Jouets',  
										   					   'VideoGames'           => 'Jeux vidéo', 
															   'Watches'              => 'Montres'															   															   
										 					);
															
		    $categories_withsort_params['sortParams']['All'] = array();
		    $categories_withsort_params['sortParams']['Apparel']       =   array(   'salesrank',
																		   		    'price',
																		   			'-price',
																					'relevancerank',
																		        	'reviewrank',
																					'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Appliances']    =   array(   'salesrank',
																		   		    'price',
																		   			'-price',
																					'relevancerank',
																		        	'reviewrank',
																					'reviewrank_authority'
			                                                             );
																		 

		    $categories_withsort_params['sortParams']['Baby']         =   array( 'salesrank',
			                                                                     'relevancerank',
																		   		 'price',
																		   		 '-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Beauty']       =   array( 'salesrank',
			                                                                     'relevancerank',
																		   		 'price',
																		   		 '-price',
																				 'reviewrank'
			                                                             );
		    $categories_withsort_params['sortParams']['Blended']      = array();
		    $categories_withsort_params['sortParams']['Books']        =   array(  'salesrank',
																				  '-daterank',
																				  'pricerank',
																				  'inverse-pricerank',
																				  'titlerank',
																				  '-titlerank',
																				  'price',
																				  '-price',
																				  'publication_date',
																				  '-unit-sales'

			                                                             );
		    $categories_withsort_params['sortParams']['Classical']    =   array(  'salesrank',
																				  'pricerank',
																				  'inverse-pricerank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank'

			                                                             );
		    $categories_withsort_params['sortParams']['DVD']         =   array(  'salesrank',
			                                                                     'amzrank', 
																		   		 'availability',
																				 'titlerank', 
																		         '-titlerank'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Electronics']  =   array( 'salesrank',
																		   		 'price',
																		   		 '-price',
																				 'titlerank', 
																		         '-titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['ForeignBooks']  =   array( 'salesrank',
																				  'pricerank',
																				  'inverse-pricerank',
																				  '-daterank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'publication_date',
																				  '-unit-sales'		
	                                                                           );
																		 
		    $categories_withsort_params['sortParams']['GiftCards']    =   array(  'salesrank',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'date-desc-rank'
			                                                             );
		    $categories_withsort_params['sortParams']['Grocery']     =   array(   'popularityrank',
																				  'price',
																				  '-price',			
																				  'relevancerank',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Handmade']     =   array(  'popularityrank',
																				  'price',
																				  '-price',			
																				  'relevancerank',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['HealthPersonalCare']  =   array( 'salesrank',
																						'price',
																						'-price',			
																						'relevancerank',
																						'reviewrank'
			                                                             );
		    $categories_withsort_params['sortParams']['HomeImprovement']    =   array(  'salesrank',
																						'price',
																						'-price',			
																						'relevancerank',
																						'reviewrank',
																						'reviewrank_authority'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Industrial']      =   array( 'featured',
																					'price',
																					'-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Jewelry']         =   array( 'salesrank',
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['KindleStore']     =   array(  'salesrank',
																					 'daterank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'reviewrank_authority',
																				   	 '-edition-sales-velocity'
			                                                             );
		    $categories_withsort_params['sortParams']['Kitchen']         =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank'

			                                                             );
		    $categories_withsort_params['sortParams']['LawnAndGarden']   =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'reviewrank_authority'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Lighting']        =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'reviewrank_authority'

			                                                             );
		    $categories_withsort_params['sortParams']['Luggage']        =   array(   'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'reviewrank_authority',
																					 'date-desc-rank'

			                                                             );

		    $categories_withsort_params['sortParams']['Magazines']        =   array( 'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'titlerank',
																					 '-titlerank',
																					 '-unit-sales'

			                                                             );
		    $categories_withsort_params['sortParams']['Marketplace']     =   array(  'salesrank',
			                                                                         'relevancerank',  
																					 'pmrank',
																					 'price',
																					 '-price',
																					 'titlerank',
																					 '-titlerank',																				
																					 '-launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['MobileApps']     =   array(  'reviewrank',
																					'pmrank',			
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['MP3Downloads']   =   array(  'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'titlerank',
																					'-titlerank',
																					'artistalbumrank',
																					'-artistalbumrank',
																					'albumrank',
																					'-albumrank',
																					'runtime',
																					'-runtime',
																					'-releasedate'
			                                                             );


		    $categories_withsort_params['sortParams']['Music']           =   array( 'salesrank',
																					'titlerank',
																					'-titlerank',
																					'pricerank',
																					'-pricerank',
																					'price',
																					'-price',
																					'availability',
																					'releasedate',
																					'-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['MusicalInstruments']  =   array( 'reviewrank',
			                                                                            'salesrank',
																						'relevancerank', 
																		   				'price',
																		   		        '-price',
																				        'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['OfficeProducts']  =   array( 'relevancerank',
			                                                                        'salesrank',
																		   		    'price',
																		   		    '-price',
																		            'reviewrank'
			                                                             );	
																		 														
		    $categories_withsort_params['sortParams']['PCHardware']     =   array( 	'psrank',
																					'salesrank',
																					'price',
																					'-price',
																					'titlerank',
																					'reviewrank',
																					'reviewrank_authority',
																					'launch_date'

			                                                             );

		    $categories_withsort_params['sortParams']['PetSupplies']  =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );																		

		    $categories_withsort_params['sortParams']['Shoes']        =   array(  'relevancerank',
																				  'salesrank',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'price',
																				  '-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Software']     =   array(  '-date',
																				  'price',
																				  '-pricerank',			
																				  'salesrank',
																				  'titlerank',
																				  '-titlerank'
			                                                             );																		 

		    $categories_withsort_params['sortParams']['SportingGoods'] =   array(   'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'-launch-date',
																					'relevancerank'
			                                                             );


		    $categories_withsort_params['sortParams']['Toys']          =   array(   'salesrank', 
																		   		    'price',
																		   		    '-price',
																					'titlerank',
																					'-titlerank'
			                                                             );

		    $categories_withsort_params['sortParams']['VideoGames']    =   array(   'salesrank',
																		   		    'price',
																		   		    '-price',			
																					'titlerank',
																					'-titlerank',
																					'-date'
			                                                             );

		    $categories_withsort_params['sortParams']['Watches']      =  array(     'salesrank', 
																		   		    'price',
																		   		    '-price',
																				    'relevancerank',
																					'reviewrank',
																					'titlerank',
																					'-titlerank'
			                                                             );
			
			
			break; // End fr

			case 'de':
			$categories_withsort_params['categories'] = array( 'All'                  => 'Alle Kategorien',
			                              					   'Apparel'              => 'Bekleidung',  
															   'Appliances'           => 'Elektro-Großgeräte',
										   					   'Automotive'           => 'Auto & Motorrad',															  			                              					   'Baby'                 => 'Baby',  
															   
										   					   'Beauty'               => 'Beauty',  
			                              					   'Blended'              => 'Blended',  
										   					   'Books'                => 'Bücher',  
			                              					   'Classical'            => 'Klassik', 
															   
															   'DVD'                  => 'DVD & Blu-ray', 
															   
										   					   'Electronics'          => 'Elektronik & Foto',  
															   'ForeignBooks'         => 'Fremdsprachige Bücher',
			                              					   'GiftCards'            => 'Geschenkgutscheine',  
										   					   'Grocery'              => 'Lebensmittel & Getränke',
															   'Handmade'             => 'Handmade',
															   'HealthPersonalCare'   => 'Drogerie & Körperpflege',  
															   
															   'HomeGarden'           => 'Garten',
															   
										   					   'Industrial'           => 'Technik & Wissenschaft',  
															   'Jewelry'              => 'Schmuck',
			                              					   'KindleStore'          => 'Kindle-Shop',
															   'Kitchen'              => 'Küche & Haushalt',
															   
															   'Lighting'             => 'Beleuchtung',
															   
			                              					   'Luggage'              => 'Koffer, Rucksäcke & Taschen',  
															   'Magazines'            => 'Zeitschriften',
			                              					   'Marketplace'          => 'Marketplace',  
			                              					   'MobileApps'           => 'Apps & Spiele', 
															   
															   'MP3Downloads'         => 'Musik-Downloads',
															    
										   					   'Music'                => 'Musik-CDs & Vinyl',  
			                              					   'MusicalInstruments'   => 'Musikinstrumente & DJ-Equipment',  
										   					   'OfficeProducts'       => 'Bürobedarf & Schreibwaren',  
															   'Pantry'               => 'Amazon Pantry',
															   
															   'PCHardware'           => 'Computer & Zubehör',
			                              					   'PetSupplies'          => 'Haustier', 
															   'Photo'                => 'Kamera & Foto',
															   
															   'Shoes'                => 'Schuhe & Handtaschen', 
										   					   'Software'             => 'Software',  
			                              					   'SportingGoods'        => 'Sport & Freizeit',
															   'Tools'                => 'Baumarkt',  
															   
			                              					   'Toys'                 => 'Spielzeug',  
															   'UnboxVideo'           => 'Amazon Instant Video',
										   					   'VideoGames'           => 'Games', 
															   'Watches'              => 'Uhren'															   															   
										 					);
															
		    $categories_withsort_params['sortParams']['All'] = array();
		    $categories_withsort_params['sortParams']['Apparel']       =   array(   'salesrank',
																		   		    'price',
																		   			'-price',
																					'relevancerank',
																		        	'reviewrank'
			                                                             );
		    $categories_withsort_params['sortParams']['Appliances']    =   array(   'salesrank',
																		   		    'price',
																		   			'-price',
																					'relevancerank',
																		        	'reviewrank',
																					'reviewrank_authority'
			                                                             );
																		 

		    $categories_withsort_params['sortParams']['Automotive']    =   array(   'salesrank',
																		   			'price',
																		   			'-price',
																					'relevancerank',
																					'reviewrank'
																				
			                                                             );

		    $categories_withsort_params['sortParams']['Baby']         =   array( 'salesrank',
			                                                                     'psrank',
			                                                                     'relevancerank',
																		   		 'price',
																		   		 '-price',
																				 'reviewrank',
																				 'titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['Beauty']       =   array('salesrank',
			                                                                    'relevancerank',
																		   		'price',
																		   		'-price',
																				'reviewrank'
			                                                             );
		    $categories_withsort_params['sortParams']['Blended']      = array();
		    $categories_withsort_params['sortParams']['Books']        =   array(  'salesrank',
																				  'reviewrank',
																				  'pricerank',
																				  'inverse-pricerank',
																				  '-pubdate',
																				  'titlerank',
																				  '-titlerank',
																				  'price',
																				  '-price',
																				  '-publication_date',
																				  '-unit-sales'

			                                                             );
		    $categories_withsort_params['sortParams']['Classical']    =   array(  'salesrank',
																				  'reviewrank',
																				  'pubdate',
																				  'publication_date',
																				  '-pubdate',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  '-publication_date',
																				  'relevancerank',
																				  '-releasedate'

			                                                             );
		    $categories_withsort_params['sortParams']['DVD']         =   array(  'salesrank',
																		   		 'price',
																		   		 '-price',
																				 'titlerank', 
																		         '-titlerank'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Electronics']  =   array( 'salesrank',
																		   		 'price',
																		   		 '-price',
																				 'titlerank', 
																		         '-titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['ForeignBooks']  =   array( 'salesrank',
																				  'reviewrank',
																				  'pricerank',
																				  'inverse-pricerank',
																				  '-pubdate',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  '-publication_date',
																				  '-unit-sales'		
	                                                                           );
																		 
		    $categories_withsort_params['sortParams']['GiftCards']    =   array(  'salesrank',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'date-desc-rank'
			                                                             );
		    $categories_withsort_params['sortParams']['Grocery']     =   array(   'salesrank',
																				  'price',
																				  '-price',			
																				  'relevancerank',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Handmade']     =   array(  'popularityrank',
																				  'price',
																				  '-price',			
																				  'relevancerank',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['HealthPersonalCare']  =   array( 'salesrank',
																						'price',
																						'-price',			
																						'relevancerank',
																						'reviewrank',
																						'titlerank',
																						'-titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['HomeGarden']      =   array(     'salesrank',
																						'price',
																						'-price',			
																						'relevancerank',
																						'reviewrank',
																						'titlerank',
																						'-titlerank'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Industrial']      =   array( 'featured',
																					'price',
																					'-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Jewelry']         =   array( 'salesrank',
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['KindleStore']     =   array(  'salesrank',
																					 'daterank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																				   	 '-edition-sales-velocity'
			                                                             );
		    $categories_withsort_params['sortParams']['Kitchen']         =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'titlerank',
																					 '-titlerank'

			                                                             );
		    $categories_withsort_params['sortParams']['Lighting']        =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'reviewrank_authority'

			                                                             );
		    $categories_withsort_params['sortParams']['Luggage']        =   array(   'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'reviewrank_authority',
																					 'date-desc-rank'

			                                                             );

		    $categories_withsort_params['sortParams']['Magazines']        =   array( 'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'titlerank',
																					 '-titlerank',
																					 '-unit-sales'

			                                                             );
		    $categories_withsort_params['sortParams']['Marketplace']     =   array(  'salesrank',
			                                                                         'relevancerank',  
																					 'pmrank',
																					 'price',
																					 '-price',
																					 'titlerank',
																					 '-titlerank',																				
																					 '-launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['MobileApps']     =   array(  'reviewrank',
																					'pmrank',			
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['MP3Downloads']   =   array(  'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'titlerank',
																					'-titlerank',
																					'artistalbumrank',
																					'-artistalbumrank',
																					'albumrank',
																					'-albumrank',
																					'runtime',
																					'-runtime',
																					'-releasedate'
			                                                             );


		    $categories_withsort_params['sortParams']['Music']           =   array(   'salesrank',
																					  '-pubdate',
																					  'price',
																					  '-price',
																					  '-publication_date',
																					  'pubdate',
																					  'publication_date',
																					  'titlerank',
																					  '-titlerank',
																					  'releasedate',
																					  '-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['MusicalInstruments']  =   array( 'reviewrank',
			                                                                            'salesrank',
																						'relevancerank', 
																		   				'price',
																		   		        '-price',
																				        'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['OfficeProducts']  =   array( 'relevancerank',
			                                                                        'salesrank',
																		   		    'price',
																		   		    '-price',
																		            'reviewrank'
			                                                             );	
		    $categories_withsort_params['sortParams']['Pantry']          =   array( 'relevancerank',
																		   		    'price',
																		   		    '-price',
																		            'reviewrank'
			                                                             );															
																		 														
		    $categories_withsort_params['sortParams']['PCHardware']     =   array( 	'psrank',
																					'salesrank',
																					'price',
																					'-price',
																					'titlerank',
																					'reviewrank',
																					'reviewrank_authority',
																					'launch_date'

			                                                             );

		    $categories_withsort_params['sortParams']['PetSupplies']  =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'price-new-bin',
																				  '-price-new-bin',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Photo']        =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'reviewrank'
			                                                             );
																		 

		    $categories_withsort_params['sortParams']['Shoes']        =   array(  'relevancerank',
																				  'salesrank',
																				  'reviewrank',
																				  'price',
																				  '-price',
																				  '-launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Software']     =   array(  '-date',
																				  'price',
																				  '-price',			
																				  'salesrank',
																				  'titlerank',
																				  '-titlerank'
			                                                             );																		 

		    $categories_withsort_params['sortParams']['SportingGoods'] =   array(   'salesrank',
																					'price',
																					'-price',
																					'titlerank',
																					'-titlerank',
																					'reviewrank',
																					'release-date',
																					'-release-date',
																					'relevancerank'
			                                                             );

		    $categories_withsort_params['sortParams']['Tools']         =   array(   'featured', 
																		   		    'price',
																		   		    '-price',
																				    'relevancerank',
																					'reviewrank'
			                                                             );

		    $categories_withsort_params['sortParams']['Toys']          =   array(   'salesrank', 
																		   		    'price',
																		   		    '-price',
																				    'relevancerank',
																					'reviewrank',
																				    '-date',
																					'-titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['UnboxVideo']    =   array(   'relevancerank',
																					'popularity-rank',
																					'price-asc-rank',
																					'price-desc-rank',
																					'review-rank',
																					'date-desc-rank'

			                                                             );																		 

		    $categories_withsort_params['sortParams']['VideoGames']    =   array(   'salesrank',
																		   		    'price',
																		   		    '-price',			
																					'titlerank',
																					'-titlerank',
																					'-date'
			                                                             );

		    $categories_withsort_params['sortParams']['Watches']      =  array(     'salesrank', 
																		   		    'price',
																		   		    '-price',
																				    'relevancerank',
																					'reviewrank',
																					'titlerank',
																					'-titlerank'
			                                                             );
			
			break; // End de

			case 'in':
			$categories_withsort_params['categories'] = array( 'All'                  => 'All Departments',
			                                                   'Apparel'              => 'Clothing & Accessories', 
			                              					   'Appliances'           => 'Appliances',  
										   					   'Automotive'           => 'Car & Motorbike',
															   
			                              					   'Baby'                 => 'Baby',  
										   					   'Beauty'               => 'Beauty',  
										   					   'Books'                => 'Books',
															   'DVD'                  => 'Movies & TV Shows', 

										   					   'Electronics'          => 'Electronics',  
															   'Furniture'            => 'Furniture',
			                              					   'GiftCards'            => 'Gift Cards',  
										   					   'Grocery'              => 'Gourmet & Specialty Foods',  
															   
										   					   'HealthPersonalCare'   => 'Health & Personal Care',  
			                              					   'HomeGarden'           => 'Home & Kitchen',  
										   					   'Industrial'           => 'Industrial & Scientific',  
															   'Jewelry'              => 'Jewellery',
															   
			                              					   'KindleStore'          => 'Kindle Store',  
										   					   'LawnAndGarden'        => 'Lawn & Garden',  
			                              					   'Luggage'              => 'Luggage & Bags',  
															   'LuxuryBeauty'         => 'Luxury Beauty',
															   
			                              					   'Marketplace'          => 'Marketplace',  
										   					   'Music'                => 'Music',  
			                              					   'MusicalInstruments'   => 'Musical Instruments',  
										   					   'OfficeProducts'       => 'Office Products',  
															   
			                              					   'Pantry'               => 'Amazon Pantry',  
										   					   'PCHardware'           => 'Computers & Accessories',  
			                              					   'PetSupplies'          => 'Pet Supplies',
															   'Shoes'                => 'Shoes & Handbags',  
															   
										   					   'Software'             => 'Software',  
			                              					   'SportingGoods'        => 'Sports, Fitness & Outdoors',  
			                              					   'Toys'                 => 'Toys & Games',  
										   					   'VideoGames'           => 'Video Games',  

			                              					   'Watches'              => 'Watches',  										   					   
															   
										 					);
															
		    $categories_withsort_params['sortParams']['All']          =  array();
		    $categories_withsort_params['sortParams']['Apparel']      =  array(  'date-desc-rank',
																				 'price',
																				 '-price',
																				 'relevancerank',
																				 'reviewrank_authority'
			                                                             );
			
		    $categories_withsort_params['sortParams']['Appliances']   =   array( '-release-date',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  'popularityrank'
			                                                             );

		    $categories_withsort_params['sortParams']['Automotive']    =   array( 'salesrank',
																				  '-release-date',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  'popularityrank'
			                                                             );

		    $categories_withsort_params['sortParams']['Baby']          =   array(  'salesrank',
			                                                                       'relevancerank',
																				   'price',
																				   '-price',
																				   'date-desc-rank',
																				   'reviewrank',
																				   'reviewrank_authority'																				   
			                                                             );
		    $categories_withsort_params['sortParams']['Beauty']        =   array(  'salesrank',
			                                                                       'relevancerank',
																				   'price',
																				   '-price',
																				   'date-desc-rank',
																				   'reviewrank',
																				   'reviewrank_authority'
																				   
			                                                             );
		    $categories_withsort_params['sortParams']['Books']         =   array(  'salesrank',
																				   'price',
																				   '-price',
																				   'relevancerank',
																				   'reviewrank',
																				   'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['DVD']           =   array(  'salesrank',
																				   'price',
																				   '-price',
																				   'inverse-pricerank',
																				   'relevancerank',
																				   'reviewrank',
																				   'reviewrank_authority',
																				   'releasedate',
																				   'daterank'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Electronics']   =   array(  'salesrank',
			                                                                       'relevancerank',   
																				   'date-desc-rank', 
																				   'price',
																				   '-price',
																				   'reviewrank',
																				   'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Furniture']     =   array(  'popularity-rank',
																				   'price',
																				   '-price',
																				   'relevancerank',
																				   'reviewrank',
																				   '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['GiftCards']    =   array(  'salesrank',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'date-desc-rank'
			                                                             );
		    $categories_withsort_params['sortParams']['Grocery']     =   array(   '-release-date',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'relevancerank',
																				  'popularity-rank'
			                                                             );
		    $categories_withsort_params['sortParams']['HealthPersonalCare']  =   array( 'salesrank',
																						'price',
																						'-price',
																						'relevancerank',
																						'reviewrank_authority',
																						'date-desc-rank'
																						
			                                                             );
		    $categories_withsort_params['sortParams']['HomeGarden']      =   array(   'salesrank',
																					  'price',
																					  '-price',
																					  'relevancerank',
																					  'reviewrank',
																					  'reviewrank_authority',
																					  'date-desc-rank'
			                                                             );
		    $categories_withsort_params['sortParams']['Industrial']      =   array( 'featured',
																					'price',
																					'-price'																					
			                                                             );
		    $categories_withsort_params['sortParams']['Jewelry']         =   array(   'reviewrank_authority',
																					  'price',
																					  '-price',
																					  'reviewrank',
																					  'relevancerank',
																					  'popularity-rank'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['KindleStore']     =   array(  'salesrank',
			                                                                         'popularity-rank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['LawnAndGarden']   =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Luggage']         =   array(  'popularity-rank',
																					 'price',
																					 '-price',
																					 'date-desc-rank',
																					 'relevancerank',
																					 'reviewrank',
																					 'reviewrank_authority'
																			       );
		    $categories_withsort_params['sortParams']['LuxuryBeauty']    =   array(  'popularity-rank',
																					 'price',
																					 '-price',
																					 '-release-date',
																					 'relevancerank',
																					 'reviewrank'
																			       );
																				   
		    $categories_withsort_params['sortParams']['Marketplace']     =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank'
			                                                             );
		    $categories_withsort_params['sortParams']['Music']           =   array(   'salesrank',
																					  'price',
																					  '-price',
																					  '-releasedate',
																					  'relevancerank',
																					  'reviewrank',
																					  'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['MusicalInstruments']  =   array( 'relevancerank',
			                                                                            'popularityrank', 
																		   				'price',
																		   		        '-price',
																				        'reviewrank',
																				        '-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['OfficeProducts']  =   array( 'relevancerank',
																					'popularityrank', 
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );															

		    $categories_withsort_params['sortParams']['Pantry']       =   array(  'relevancerank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['PCHardware']   =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'date-desc-rank'
			                                                             );

		    $categories_withsort_params['sortParams']['PetSupplies']  =   array( 'salesrank',
																		   		 'price',
																		   		 '-price',
																				 'titlerank',
																				 '-titlerank',
																				 'relevance',
																				 'relevancerank',
																		         'reviewrank',
																				 'reviewrank_authority'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Shoes']        =   array( 'salesrank',
																		   		 'price',
																		   		 '-price',
																				 'date-desc-rank',
																				 'relevancerank',
																				 'reviewrank_authority'
			                                                             );																		 
 
		    $categories_withsort_params['sortParams']['Software']     =   array(  'relevancerank',
																				  'salesrank',
																				  'popularityrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['SportingGoods'] =   array(   'relevancerank',
																					'price',
																					'-price',
																					'popularity-rank',
																					'reviewrank_authority',
																					'date-desc-rank'
			                                                             );


		    $categories_withsort_params['sortParams']['Toys']          =   array( 'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'date-desc-rank'
			                                                             );

		    $categories_withsort_params['sortParams']['VideoGames']    =   array( 'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  '-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['Watches']      =   array(  'relevancerank',
																				  'price',
																				  '-price',
																				  'popularity-rank',
																				  'reviewrank',
																				  'reviewrank_authority'

			                                                             );


			
			break; // End in

			case 'it':
			$categories_withsort_params['categories'] = array( 'All'                  => 'Tutte le categorie',
			                                                   'Apparel'              => 'Abbigliamento', 
										   					   'Automotive'           => 'Auto e Moto',
															   
			                              					   'Baby'                 => 'Prima infanzia',  
										   					   'Beauty'               => 'Bellezza',  
										   					   'Books'                => 'Libri',
															   'DVD'                  => 'Film e TV',  

										   					   'Electronics'          => 'Elettronica',
															   'ForeignBooks'         => 'Libri in altre lingue', 
															   'Garden'               => 'Giardino e giardinaggio', 

			                              					   'GiftCards'            => 'Buoni Regalo',  
										   					   'Grocery'              => 'Alimentari e cura della casa',  
			                              					   'Handmade'             => 'Handmade',  
										   					   'HealthPersonalCare'   => 'Cura della Persona',  


										   					   'Industrial'           => 'Industria e Scienza',  
															   'Jewelry'              => 'Gioielli',
			                              					   'KindleStore'          => 'Kindle Store',  
															   'Kitchen'              => 'Casa e cucina',
															   
															   'Lighting'             => 'Illuminazione',
															   

			                              					   'Luggage'              => 'Valigeria',  

			                              					   'MobileApps'           => 'App e Giochi',  
			                              					   'MP3Downloads'         => 'Musica Digitale',  
										   					   'Music'                => 'CD e Vinili',  

			                              					   'MusicalInstruments'   => 'Strumenti musicali e DJ',  
										   					   'OfficeProducts'       => 'Cancelleria e prodotti per ufficio',  
										   					   'PCHardware'           => 'Informatica', 
															   'Shoes'                => 'Scarpe e borse', 

										   					   'Software'             => 'Software',  
			                              					   'SportingGoods'        => 'Sport e tempo libero',  
										   					   'Tools'                => 'Fai da te',  

			                              					   'Toys'                 => 'Giochi e giocattoli',  
										   					   'VideoGames'           => 'Videogiochi',  

			                              					   'Watches'              => 'Orologi',  
															   
										 					);
															
		    $categories_withsort_params['sortParams']['All']            =   array();
		    $categories_withsort_params['sortParams']['Apparel']        =   array(  'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'relevancerank'
			                                                             );			
		    $categories_withsort_params['sortParams']['Automotive']    =   array(   'salesrank',
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Baby']         =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank'
			                                                             );
		    $categories_withsort_params['sortParams']['Beauty']       =   array(  'relevancerank',
																				  'salesrank',
																				  'popularityrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Books']        =   array( 'salesrank',
																				 'price',
																				 '-price',
																				 'relevancerank',
																				 'reviewrank',
																				 'reviewrank_authority',
																				 '-pubdate',
																				 '-publication_date'
			                                                             );
		    $categories_withsort_params['sortParams']['DVD']          =   array(  'salesrank',
																				  '-releasedate',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Electronics']   =   array(  'salesrank',
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['ForeignBooks']  =   array(   'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'-pubdate',
																					'-publication_date'
			                                                             );
		    $categories_withsort_params['sortParams']['Garden']       =  array( 'relevancerank',
																				'salesrank',
																				'price',
																				'-price',
																				'reviewrank',
																				'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['GiftCards']    =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank_authority',
																				  'date-desc-rank',
																				  'reviewrank'
			                                                             );
		    $categories_withsort_params['sortParams']['Grocery']     =   array(   'relevancerank',
																				  'popularityrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Handmade']    =   array(   'relevancerank',
																				  'popularityrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['HealthPersonalCare']  =   array( 'relevancerank',
																						'salesrank',
																						'popularityrank',
																						'price',
																						'-price',
																						'reviewrank',
																						'-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Industrial']      =   array( 'featured',
																					'price',
																					'-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Jewelry']         =   array( 'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['KindleStore']     =   array(  'salesrank',
																					 'daterank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'reviewrank_authority',
																					 '-edition-sales-velocity'
			                                                             );
		    $categories_withsort_params['sortParams']['Kitchen']        =   array( 'salesrank',
																		   		   'price',
																		   		   '-price',
																				   'relevancerank',
																		           'reviewrank',
																				   'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Lighting']       =   array( 'salesrank',
																		   		   'price',
																		   		   '-price',
																				   'relevancerank',
																		           'reviewrank'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Luggage']         =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'reviewrank_authority',
																					 'date-desc-rank'
			                                                             );
		    $categories_withsort_params['sortParams']['MobileApps']     =   array(  'pmrank',
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['MP3Downloads']    =   array( 'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'titlerank',
																					'-titlerank',
																					'-releasedate',
																					'artistalbumrank',
																					'-artistalbumrank',
																					'albumrank',
																					'-albumrank',
																					'runtime',
																					'-runtime'
			                                                             );

		    $categories_withsort_params['sortParams']['Music']           =   array(   'relevancerank',
																					  'salesrank',
																					  'price',
																					  '-price',
																					  'reviewrank',
																					  'reviewrank_authority',
																					  '-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['MusicalInstruments']  =   array( 'relevancerank',
																						'popularityrank',
																						'price',
																						'-price',
																						'reviewrank',
																						'-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['OfficeProducts']  =   array( 'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank_authority',
																					'date-desc-rank'
			                                                             );															

		    $categories_withsort_params['sortParams']['PCHardware']   =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'date-desc-rank',
																				  'reviewrank_authority',
																				  'reviewrank'
			                                                             );

		    $categories_withsort_params['sortParams']['Shoes']        =   array( 'salesrank',
																		   		 'price',
																				 'pricerank',
																		   		 '-price',
																				 'inverse-pricerank',
																				 '-launch-date',
																				 'relevancerank',
																		         'reviewrank',
																				 'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Software']     =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  '-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['SportingGoods'] =   array(   'reviewrank',
																					'salesrank', 
																		   		    'price',
																		   		    '-price',
																				    'relevancerank',
																				    'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Tools']         =   array( 'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'date-desc-rank'
			                                                             );

		    $categories_withsort_params['sortParams']['Toys']          =   array( 'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['VideoGames']    =   array( 'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  '-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['Watches']        =  array( 'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );

			
			break; //End it

			case 'co.jp':
			$categories_withsort_params['categories'] = array( 'All'                  => 'すべてのカテゴリー',
			                                                   'Apparel'              => '服＆ファッション小物',
			                              					   'Appliances'           => '大型家電',  
										   					   'Automotive'           => 'カー・バイク用品',
															   
			                              					   'Baby'                 => 'ベビー&マタニティ',  
										   					   'Beauty'               => 'コスメ',  
			                              					   'Blended'              => 'Blended',  
										   					   'Books'                => '本',  
															   
															   'Classical'            => 'クラシック',
															   'CreditCards'          => 'クレジットカード', 
															   'DVD'                  => 'DVD',

										   					   'Electronics'          => '家電&カメラ',  
															   'ForeignBooks'         => '洋書',

			                              					   'GiftCards'            => 'ギフト券',  
										   					   'Grocery'              => '食品・飲料・お酒',  
										   					   'HealthPersonalCare'   => 'ヘルス&ビューティー',  
															   'Hobbies'              => 'Hobbies',


			                              					   'HomeImprovement'      => 'DIY・工具',  
										   					   'Industrial'           => '産業・研究開発用品', 
															   'Jewelry'              => 'ジュエリー', 
			                              					   'KindleStore'          => 'Kindleストア',  
															   'Kitchen'              => 'ホーム&キッチン',

			                              					   'Marketplace'          => 'Marketplace',  
			                              					   'MobileApps'           => 'Android アプリ',  
			                              					   'MP3Downloads'         => 'デジタルミュージック',  
										   					   'Music'                => 'ミュージック',  

			                              					   'MusicalInstruments'   => '楽器',  
										   					   'OfficeProducts'       => '文房具・オフィス用品',  
										   					   'PCHardware'           => 'パソコン・周辺機器',  

			                              					   'PetSupplies'          => 'ペット用品',  
															   'Shoes'                => 'シューズ＆バッグ',
										   					   'Software'             => 'PCソフト',  
			                              					   'SportingGoods'        => 'スポーツ&アウトドア',  

			                              					   'Toys'                 => 'Toys',  
															   'Video'                => 'DVD',
										   					   'VideoDownload'        => 'Amazon インスタント・ビデオ',  
										   					   'VideoGames'           => 'TVゲーム',  

			                              					   'Watches'              => '腕時計',  
															   
										 					);
															
		    $categories_withsort_params['sortParams']['All']           =   array();
		    $categories_withsort_params['sortParams']['Apparel']       =   array( 'salesrank',
																				  'price',
																				  '-price',
																				  'relevancerank'
			                                                                );
			
		    $categories_withsort_params['sortParams']['Appliances']    =   array( 'salesrank',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Automotive']    =   array( 'salesrank',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  'reviewrank_authority'																				
			                                                             );

		    $categories_withsort_params['sortParams']['Baby']         =   array(  'salesrank',
																				  'psrank',
																				  'price',
																				  '-price',
																				  'titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['Beauty']       =   array( 'salesrank',
			                                                                     'relevancerank',
																				 'reviewrank',
																				 'price',
																				 '-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Blended']      =   array();
		    $categories_withsort_params['sortParams']['Books']        =   array( 'salesrank',
																				 'pricerank', 
																				 'inverse-pricerank',
																				 'daterank',
																				 'titlerank',
																				 '-titlerank',  
																				 '-unit-sales',
																				 'price',
																				 '-price',
																				 '-publication_date'
			                                                             );
		    $categories_withsort_params['sortParams']['Classical']   =   array( 'salesrank',
																				'pricerank',
																				'price',
																				'-pricerank',
																				'-price',
																				'titlerank',
																				'-titlerank',
																				'orig-rel-date',
																				'releasedate',
																				'-orig-rel-date',
																				'-releasedate'
			                                                             );
		    $categories_withsort_params['sortParams']['CreditCards']  =   array(  'reviewrank',
																				  'relevancerank',
																				  'price',
																				  '-price',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['DVD']          =   array(  'salesrank',
																				  'pricerank',
																				  'price',
																				  '-pricerank',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'orig-rel-date',
																				  'releasedate',
																				  '-orig-rel-date',
																				  '-releasedate'
			                                                             );
																		 
																		 
		    $categories_withsort_params['sortParams']['Electronics']  =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'release-date',
																				  '-release-date',
																				  '-releasedate',
																				  'releasedate',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['ForeignBooks'] =   array(  'salesrank',
																				  'pricerank',
																				  'inverse-pricerank',
																				  'daterank',
																				  'titlerank',
																				  '-titlerank',
																				  'price',
																				  '-price',
																				  '-publication_date',
																				  '-unit-sales'
			                                                             );
		    $categories_withsort_params['sortParams']['GiftCards']    =   array(  'salesrank',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'date-desc-rank'
			                                                             );
		    $categories_withsort_params['sortParams']['Grocery']     =   array(   'salesrank',
																				  'reviewrank',
																				  'price',
																				  '-price',
																				  'relevancerank'
			                                                             );
		    $categories_withsort_params['sortParams']['HealthPersonalCare']  =   array( 'salesrank',
																						'price',
																						'-price',
																						'titlerank',
																						'-titlerank',
																						'release-date',
																						'-release-date',
																						'releasedate',
																						'-releasedate'
			                                                             );
		    $categories_withsort_params['sortParams']['Hobbies']         =   array( 'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'titlerank',
																					'-titlerank',
																					'release-date',
																					'-release-date',
																					'mfg-age-min',
																					'-mfg-age-min',
																					'releasedate',
																					'-releasedate',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['HomeImprovement'] =   array( 'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Industrial']      =   array( 'relevancerank',
																					'featured',
																					'price',
																					'-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Jewelry']         =   array( 'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['KindleStore']     =   array(  'salesrank',
																					 'daterank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Kitchen']         =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'date-desc-rank',
																					 'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Marketplace']     =   array(  'salesrank',
																					 'pmrank',
																					 'price',
																					 '-price',
																					 'titlerank',
																					 '-titlerank',																				
																					 '-launch-date',
																					 'relevancerank'
			                                                             );
		    $categories_withsort_params['sortParams']['MobileApps']     =   array(  'pmrank',
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );


		    $categories_withsort_params['sortParams']['MP3Downloads']    =   array(   'relevancerank',
																					  'salesrank',
																					  'titlerank',
																					  '-titlerank',
																					  'artistalbumrank',
																					  '-artistalbumrank',
																					  'albumrank',
																					  '-albumrank',
																					  'runtime',
																					  '-runtime',
																					  'price',
																					  '-price',
																					  'price-new-bin',
																					  '-price-new-bin',
																					  'reviewrank_authority',
																					  'releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['Music']           =   array(  'salesrank',
																					 'pricerank',
																					 '-pricerank',
																					 'price',
																					  '-price',
																					  'titlerank',
																					  '-titlerank',
																					  'orig-rel-date',
																					  '-orig-rel-date',
																					  'releasedate',
																					  '-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['MusicalInstruments']  =   array( 'reviewrank',
			                                                                            'salesrank', 
																		   				'price',
																		   		        '-price',
																				        'relevancerank'
			                                                             );

		    $categories_withsort_params['sortParams']['OfficeProducts']     =   array( 'reviewrank',
			                                                                            'salesrank', 
																		   				'price',
																		   		        '-price',
																				        'relevancerank'
			                                                             );

		    $categories_withsort_params['sortParams']['PCHardware']   =   array(  'relevancerank',
																				  'salesrank',
																				  'reviewrank',
																				  'price',
																				  '-price',
																				  'price-new-bin',
																				  '-price-new-bin',
																				  'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['PetSupplies']  =   array( 'salesrank',
																		   		 'price',
																		   		 '-price',
																				 'price-new-bin',
																				 '-price-new-bin',
																				 'relevancerank',
																		         'reviewrank',
																				 'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Shoes']       =   array(  'salesrank',
																		   		 'price',
																		   		 '-price',
																				 '-launch-date',
																				 'relevancerank',
																		         'reviewrank',
																				 'reviewrank_authority'
			                                                             );
																		 

		    $categories_withsort_params['sortParams']['Software']     =   array(  'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'release-date',
																				  '-release-date',
																				  'releasedate',
																				  '-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['SportingGoods'] =   array(   'titlerank',
			                                                                        '-titlerank',
																					'salesrank', 
																					'release-date',
																					'-release-date',
																		   		    'price',
																		   		    '-price'
			                                                             );

		    $categories_withsort_params['sortParams']['Toys']          =   array( 'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'release-date',
																				  '-release-date',
																				  'releasedate',
																				  '-releasedate',
																				  'relevancerank',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'price',
																				  '-price'
			                                                             );

		    $categories_withsort_params['sortParams']['Video']      =   array(  'salesrank',
																				'pricerank',
																				'price',
																				'-pricerank',
																				'-price',
																				'titlerank',
																				'-titlerank',
																				'-orig-rel-date',
																				'-releasedate',
																				'releasedate',
																				'orig-rel-date'
			                                                             );

		    $categories_withsort_params['sortParams']['VideoDownload'] =   array( 'relevancerank',
																				  'popularity-rank',
																				  'price-desc-rank',
																				  'price-asc-rank',
																				  'review-rank',
																				  'date-desc-rank'
			                                                             );

		    $categories_withsort_params['sortParams']['VideoGames']    =   array( 'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'release-date',
																				  '-releasedate',
																				  'releasedate',
																				  '-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['Watches']       =   array( 'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank'
			                                                             );

			
			break; //End co.jp

			case 'com.mx':
			$categories_withsort_params['categories'] = array( 'All'                  => 'Todos los departamentos',
			                              					   'Baby'                 => 'Bebé',  
										   					   'Books'                => 'Libros',  
															   'DVD'                  => 'Películas y Series de TV',

										   					   'Electronics'          => 'Electrónicos',  
										   					   'HealthPersonalCare'   => 'Salud, Belleza y Cuidado Personal',  


			                              					   'HomeImprovement'      => 'Herramientas y Mejoras del Hogar',  
			                              					   'KindleStore'          => 'Tienda Kindle',  
										   					   'Kitchen'              => 'Hogar y Cocina',  

										   					   'Music'                => 'Música',  

										   					   'OfficeProducts'       => 'Oficina y Papelería',  
										   					   'Software'             => 'Software',  
			                              					   'SportingGoods'        => 'Deportes y Aire Libre',
															   'VideoGames'           => 'Videojuegos',  


			                              					   'Watches'              => 'Relojes', 
															   
										 					);
															
		    $categories_withsort_params['sortParams']['All']          =   array();
		    $categories_withsort_params['sortParams']['Baby']         =   array(  'relevancerank',
																				  'popularityrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Books']        =   array(  'relevancerank',
																				  'popularityrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['DVD']         =   array( 'relevancerank',
																				'popularityrank',
																				'price',
																				'-price',
																				'reviewrank',
																				'-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Electronics']  =   array(  'relevancerank',
																				  'popularityrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['HealthPersonalCare']  =   array( 'relevancerank',
																						'popularityrank',
																						'price',
																						'-price',
																						'reviewrank',
																						'-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['HomeImprovement'] =   array( 'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                              );
		    $categories_withsort_params['sortParams']['KindleStore']     =   array( 'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Kitchen']        =   array(  'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Music']           =  array(  'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['OfficeProducts']  =  array(  'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );


		    $categories_withsort_params['sortParams']['Software']     =   array( 'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['SportingGoods'] =   array(   'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['VideoGames']    =   array(   'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['Watches']       =   array(   'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );

			
			break; //End com.mx

			case 'es':
			$categories_withsort_params['categories'] = array( 'All'                  => 'Todos los departamentos',
			                                                   'Apparel'              => 'Ropa y accesorios',  
										   					   'Automotive'           => 'Coche y moto',
															   
			                              					   'Baby'                 => 'Bebé',  
										   					   'Beauty'               => 'Belleza',  
										   					   'Books'                => 'Libros',  
															   'DVD'                  => 'Películas y TV', 

										   					   'Electronics'          => 'Electrónica',  
															   'ForeignBooks'         => 'Libros en idiomas extranjeros',

			                              					   'GiftCards'            => 'Cheques regalo',  
										   					   'Grocery'              => 'Supermercado',  
			                              					   'Handmade'             => 'Handmade',  
										   					   'HealthPersonalCare'   => 'Salud y cuidado personal',  


										   					   'Industrial'           => 'Industria y ciencia',  
															   'Jewelry'              => 'Joyería',
			                              					   'KindleStore'          => 'Tienda Kindle',  
															   'Kitchen'              => 'Hogar',
										   					   'LawnAndGarden'        => 'Jardín',  
															   
															   'Lighting'             => 'Iluminación',
			                              					   'Luggage'              => 'Equipaje',  

			                              					   'MobileApps'           => 'Apps y Juegos',  
			                              					   'MP3Downloads'         => 'Música Digital',  
										   					   'Music'                => 'Música: CDs y vinilos',  

			                              					   'MusicalInstruments'   => 'Instrumentos musicales',  
										   					   'OfficeProducts'       => 'Oficina y papelería',  
										   					   'PCHardware'           => 'Informática',  
															   'Shoes'                => 'Zapatos y complementos',

										   					   'Software'             => 'Software',  
			                              					   'SportingGoods'        => 'Deportes y aire libre',  
										   					   'Tools'                => 'Bricolaje y herramientas',  

			                              					   'Toys'                 => 'Juguetes y juegos',  
										   					   'VideoGames'           => 'Videojuegos',  

			                              					   'Watches'              => 'Relojes',  
															   
										 					);
															
		    $categories_withsort_params['sortParams']['All']          =   array();
		    $categories_withsort_params['sortParams']['Apparel']      =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank'
			                                                             );
		    $categories_withsort_params['sortParams']['Automotive']   =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );
			
		    $categories_withsort_params['sortParams']['Baby']         =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Beauty']       =   array(  'relevancerank',
																				  'popularityrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Books']        =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  '-pubdate',
																				  '-publication_date'
			                                                             );
		    $categories_withsort_params['sortParams']['DVD']         =   array( 'relevancerank',
																				'salesrank',
																				'reviewrank',
																				'reviewrank_authority',
																				'price',
																				'-price',
																				'-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Electronics']  =   array(  'relevancerank',
																				  'salesrank',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'price',
																				  '-price'
			                                                             );
		    $categories_withsort_params['sortParams']['ForeignBooks']  =   array(   'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'-pubdate',
																					'-publication_date'
			                                                             );

		    $categories_withsort_params['sortParams']['GiftCards']  =   array(    'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'date-desc-rank'
			                                                             );
		    $categories_withsort_params['sortParams']['Grocery']    =   array(    'relevancerank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['Handmade']   =   array(    'relevancerank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  '-release-date',
																				  'popularityrank'
			                                                             );
																		 
																		 
																		 
		    $categories_withsort_params['sortParams']['HealthPersonalCare']  =   array( 'relevancerank',
																						'salesrank',
																						'reviewrank_authority',
																						'price',
																						'-price',
																						'date-desc-rank'
			                                                                );
		    $categories_withsort_params['sortParams']['Industrial']        =   array(   'featured',
																						'price',
																						'-price'
			                                                               );
																		 
		    $categories_withsort_params['sortParams']['Jewelry']           =   array( 'relevancerank',
			                                                                          'salesrank',
																					  'price',
																					  '-price',
																					  'reviewrank',
																					  'reviewrank_authority',
																					  'date-desc-rank'
			                                                              );
		    $categories_withsort_params['sortParams']['KindleStore']     =   array( 'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'daterank',
																					'-edition-sales-velocity'
			                                                             );
		    $categories_withsort_params['sortParams']['Kitchen']        =   array(  'relevancerank',
			                                                                        'salesrank', 
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['LawnAndGarden']  =   array(  'relevancerank',
			                                                                        'salesrank',
																					'popularityrank', 
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['Lighting']       =   array(  'relevancerank',
																					'popularityrank', 
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['Luggage']        =   array(  'relevancerank',
			                                                                        'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'date-desc-rank'
			                                                             );
		    $categories_withsort_params['sortParams']['MobileApps']     =   array(  'relevancerank',
			                                                                        'pmrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['MP3Downloads']   =   array(  'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'titlerank',
																					'-titlerank',
																					'-releasedate',
																					'artistalbumrank',
																					'-artistalbumrank',
																					'albumrank',
																					'-albumrank',
																					'runtime',
																					'-runtime'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Music']           =  array(  'relevancerank',
			                                                                        'salesrank',  
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['MusicalInstruments'] =  array(   'relevancerank',
			                                                                            'popularityrank',
																						'price',
																						'-price',
																						'reviewrank',
																						'-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['OfficeProducts']  =  array(  'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['PCHardware']     =  array(   'relevancerank',
																					'popularityrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Shoes']          =  array(   'relevancerank',
																					'popularity-rank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'-launch-date'
			                                                             );


		    $categories_withsort_params['sortParams']['Software']     =   array(    'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['SportingGoods'] =   array(   'relevancerank',
			                                                                        'salesrank',  
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'date-desc-rank'
			                                                             );

		    $categories_withsort_params['sortParams']['Tools']        =   array(    'relevancerank',
			                                                                        'salesrank',  
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'date-desc-rank'
			                                                             );

		    $categories_withsort_params['sortParams']['Toys']         =   array(    'relevancerank',
			                                                                        'salesrank',  
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['VideoGames']    =   array(   'relevancerank',
			                                                                        'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['Watches']       =   array(   'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );
						
			break; // End es

			case 'co.uk':
			$categories_withsort_params['categories'] = array( 'All'                  => 'All Departments',
			                                                   'Apparel'              => 'Clothing',  
															   'Appliances'           => 'Large Appliances',
										   					   'Automotive'           => 'Car & Motorbike',
															   
			                              					   'Baby'                 => 'Baby',  
										   					   'Beauty'               => 'Beauty',  
															   'Blended'              => 'Blended',
										   					   'Books'                => 'Books',  
															   
															   'Classical'            => 'Classical',
															   'DVD'                  => 'DVD & Blu-ray',
										   					   'Electronics'          => 'Electronics & Photo',  
			                              					   'GiftCards'            => 'Gift Cards',  
															   
										   					   'Grocery'              => 'Grocery',  
			                              					   'Handmade'             => 'Handmade',  
										   					   'HealthPersonalCare'   => 'Health & Personal Care',  
															   'HomeGarden'           => 'Garden & Outdoors', 


										   					   'Industrial'           => 'Industrial & Scientific',  
															   'Jewelry'              => 'Jewellery',
			                              					   'KindleStore'          => 'Kindle Store',  
															   'Kitchen'              => 'Kitchen & Home',
															   
															   'Lighting'             => 'Lighting',
			                              					   'Luggage'              => 'Luggage',  
															   'Marketplace'          => 'Marketplace',
			                              					   'MobileApps'           => 'Apps & Games',  
															   
			                              					   'MP3Downloads'         => 'Digital Music',  
										   					   'Music'                => 'CDs & Vinyl',  
			                              					   'MusicalInstruments'   => 'Musical Instruments & DJ',  
										   					   'OfficeProducts'       => 'Stationery & Office Supplies',  
															   
															   'Pantry'               => 'Amazon Pantry',
										   					   'PCHardware'           => 'Computers',  
															   'PetSupplies'          => 'Pet Supplies',
															   'Shoes'                => 'Shoes & Bags',

										   					   'Software'             => 'Software',  
			                              					   'SportingGoods'        => 'Sports & Outdoors',  
										   					   'Tools'                => 'DIY & Tools',  
			                              					   'Toys'                 => 'Toys & Games', 
															    
															   'UnboxVideo'           => 'Amazon Instant Video',
															   'VHS'                  => 'VHS',
										   					   'VideoGames'           => 'PC & Video Games',  
			                              					   'Watches'              => 'Watches',  
															   
										 					);
															
		    $categories_withsort_params['sortParams']['All']             =   array();
		    $categories_withsort_params['sortParams']['Apparel']         =   array(   'salesrank',
																					  'price',
																					  '-price',
																					  'relevancerank',
																					  'reviewrank',
																					  '-launch-date'
			                                                             );
			
		    $categories_withsort_params['sortParams']['Appliances']      =   array(   'salesrank',
																					  'price',
																					  '-price',
																					  'relevancerank',
																					  'reviewrank',
																					  'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Automotive']      =   array(   'salesrank',
																					  'price',
																					  '-price',
																					  'relevancerank',
																					  'reviewrank',
																					  'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Baby']            =   array(  'salesrank',
																					 'relevancerank',
																					 'price',
																					 '-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Beauty']       =   array(  'salesrank',
																				  'relevancerank',
																				  'reviewrank',
																				  'price',
																				  '-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Blended']      =   array();
		    $categories_withsort_params['sortParams']['Books']        =   array( 'salesrank',
																				 'pricerank', 
																				 'inverse-pricerank',
																				 'daterank',
																				 'pubdate',
																				 'titlerank',
																				 '-titlerank',  
																				 '-unit-sales',
																				 'price',
																				 '-price',
																				 'reviewrank',
																				 '-publication_date',
																				 'publication_date'
			                                                             );
		    $categories_withsort_params['sortParams']['Classical']  =   array(  'salesrank',
																		   		'price',
																		   		'-price',
																				'inverse-pricerank', 
																		        'reviewrank',
																				'titlerank',
																				'-titlerank'  

			                                                             );
		    $categories_withsort_params['sortParams']['DVD']       =   array(   'salesrank',
																		   		'price',
																		   		'-price',
																				'inverse-pricerank', 
																		        'reviewrank',
																				'daterank',
																				'releasedate',
																				'titlerank',
																				'-titlerank'  

			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Electronics']  =   array(  'salesrank',
																				  'price',
																				  'inverse-pricerank', 
																				  'reviewrank',
																				  'daterank',
																				  'titlerank',
																				  '-titlerank'  

			                                                             );
		    $categories_withsort_params['sortParams']['GiftCards']    =   array(  'popularityrank',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Grocery']     =   array(   'salesrank',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Handmade']    =   array(   'popularityrank',
																				  'price',
																				  '-price',
																				  'relevancerank',
																				  'reviewrank',
																				  '-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['HealthPersonalCare']  =   array( '-price',
																						'salesrank',
																						'price',
																						'daterank',
																						'reviewrank',
																						'titlerank',
																						'-titlerank',
																						'releasedate'
			                                                             );
		    $categories_withsort_params['sortParams']['HomeGarden']      =   array( '-price',
																					'salesrank',
																					'price',
																					'daterank',
																					'reviewrank',
																					'titlerank',
																					'-titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['Industrial']      =   array( 'featured',
																					'price',
																					'-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Jewelry']         =   array(  'salesrank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 '-launch-date'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['KindleStore']     =   array(  'salesrank',
																					 'daterank',
																					 'price',
																					 '-price',
																					 'relevancerank',
																					 'reviewrank',
																					 '-edition-sales-velocity'
			                                                             );
		    $categories_withsort_params['sortParams']['Kitchen']         =   array( 'daterank',
																					'-price',
																					'salesrank',
																					'price',
																					'reviewrank',
																					'titlerank',
																					'-titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['Lighting']        =   array( 'relevancerank',
																					'-price',
																					'salesrank',
																					'price',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );
																		 
		    $categories_withsort_params['sortParams']['Luggage']         =   array( 'relevancerank',
																					'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'reviewrank_authority',
																					'date-desc-rank'
			                                                             );
		    $categories_withsort_params['sortParams']['Marketplace']     =   array( 'relevancerank',
																					'pmrank',
																					'salesrank',
																					'price',
																					'-price',
																					'titlerank',
																					'-titlerank',
																					'-launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['MobileApps']     =   array(  'pmrank',
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank',
																					'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['MP3Downloads']    =   array( 'salesrank',
																					'price',
																					'-price',
																					'relevancerank',
																					'reviewrank',
																					'-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['Music']           =   array( 'salesrank',
																					'reviewrank',
																					'price',
																					'inverse-pricerank',
																					'-price',
																					'releasedate',
																					'-releasedate',
																					'titlerank',
																					'-titlerank'
			                                                             );

		    $categories_withsort_params['sortParams']['MusicalInstruments']  =   array( 'relevancerank',
																						'-price',
																						'salesrank',
																						'price',
																						'reviewrank',
																						'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['OfficeProducts']  =   array( 'relevancerank',
			                                                                        'salesrank',
																		   		    'price',
																		   		    '-price',
																		            'reviewrank'
			                                                             );															

		    $categories_withsort_params['sortParams']['Pantry']       =   array( 'relevancerank',
																				  'price',
																				  '-price',
																				  'reviewrank'
			                                                             );

		    $categories_withsort_params['sortParams']['PCHardware']   =   array(  'psrank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  'reviewrank',
																				  'reviewrank_authority',
																				  'launch_date'
			                                                             );

		    $categories_withsort_params['sortParams']['PetSupplies']  =   array( 'salesrank',
																		   		 'price',
																		   		 '-price',
																				 'price-new-bin',
																				 '-price-new-bin',
																				 'relevancerank',
																		         'reviewrank',
																				 'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Shoes']        =   array(  'pmrank',
																				  'reviewrank',
																				  'price',
																				  '-price',
																				  '-launch-date'
			                                                             );
																		 

		    $categories_withsort_params['sortParams']['Software']     =   array(  'salesrank',
																				  'price',
																				  'inverse-pricerank',
																				  'reviewrank',
																				  'titlerank',
																				  '-titlerank',
																				  'daterank'
			                                                             );

		    $categories_withsort_params['sortParams']['SportingGoods'] =   array(   'salesrank',
																					'price',
																					'-price',
																					'reviewrank',
																					'titlerank',
																					'-titlerank'
			                                                             );

		    $categories_withsort_params['sortParams']['Tools']         =   array( 'salesrank',
																				  'price',
																				  '-price',
																				  'reviewrank',
																				  'daterank',
																				  'titlerank',
																				  '-titlerank'
			                                                             );

		    $categories_withsort_params['sortParams']['Toys']          =   array( 'mfg-age-min',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  '-mfg-age-min'
			                                                             );

		    $categories_withsort_params['sortParams']['UnboxVideo']    =   array( 'relevancerank',
																				  'popularity-rank',
																				  'price-asc-rank',
																				  'price-desc-rank',
																				  'review-rank',
																				  'date-desc-rank'
			                                                             );

		    $categories_withsort_params['sortParams']['VHS']           =   array( 'daterank',
																				  'salesrank',
																				  'price',
																				  'inverse-pricerank',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  'reviewrank',
																				  'releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['VideoGames']    =   array( 'daterank',
																				  'salesrank',
																				  'price',
																				  'inverse-pricerank',
																				  'titlerank',
																				  '-titlerank',
																				  'reviewrank'
			                                                             );

		    $categories_withsort_params['sortParams']['Watches']      =   array(  'relevancerank',
																				  'salesrank',
																				  'price',
																				  '-price',
																				  'titlerank',
																				  '-titlerank',
																				  '-launch-date',
																				  'reviewrank'
			                                                             );

			
			break; //End co.uk

			case 'com':
			$categories_withsort_params['categories'] = array( 'All'                  => 'All Departments',
			                              					   'Appliances'           => 'Appliances',  
										   					   'ArtsAndCrafts'        => 'Arts, Crafts & Sewing',  
										   					   'Automotive'           => 'Automotive',
															   
			                              					   'Baby'                 => 'Baby',  
										   					   'Beauty'               => 'Beauty',  
			                              					   'Blended'              => 'Blended',  
										   					   'Books'                => 'Books',  

			                              					   'Collectibles'         => 'Collectibles & Fine Arts',  
										   					   'Electronics'          => 'Electronics',  
			                              					   'Fashion'              => 'Clothing, Shoes & Jewelry',  
										   					   'FashionBaby'          => 'Clothing, Shoes & Jewelry - Baby',  

			                              					   'FashionBoys'          => 'Clothing, Shoes & Jewelry - Boys',  
										   					   'FashionGirls'         => 'Clothing, Shoes & Jewelry - Girls',  
			                              					   'FashionMen'           => 'Clothing, Shoes & Jewelry - Men',  
										   					   'FashionWomen'         => 'Clothing, Shoes & Jewelry - Women',  

			                              					   'GiftCards'            => 'Gift Cards',  
										   					   'Grocery'              => 'Grocery & Gourmet Food',  
			                              					   'Handmade'             => 'Handmade',  
										   					   'HealthPersonalCare'   => 'Health & Personal Care',  


			                              					   'HomeGarden'           => 'Home & Kitchen',  
										   					   'Industrial'           => 'Industrial & Scientific',  
			                              					   'KindleStore'          => 'Kindle Store',  
										   					   'LawnAndGarden'        => 'Patio, Lawn & Garden',  

			                              					   'Luggage'              => 'Luggage & Travel Gear',  
										   					   'Magazines'            => 'Magazine Subscriptions',  
			                              					   'Marketplace'          => 'Marketplace',  
										   					   'Merchants'            => 'Merchants',  


			                              					   'MobileApps'           => 'Apps & Games',  
										   					   'Movies'               => 'Movies & TV',  
			                              					   'MP3Downloads'         => 'Digital Music',  
										   					   'Music'                => 'CDs & Vinyl',  

			                              					   'MusicalInstruments'   => 'Musical Instruments',  
										   					   'OfficeProducts'       => 'Office Products',  
			                              					   'Pantry'               => 'Prime Pantry',  
										   					   'PCHardware'           => 'Computers',  

			                              					   'PetSupplies'          => 'Pet Supplies',  
										   					   'Software'             => 'Software',  
			                              					   'SportingGoods'        => 'Sports & Outdoors',  
										   					   'Tools'                => 'Tools & Home Improvement',  

			                              					   'Toys'                 => 'Toys & Games',  
										   					   'UnboxVideo'           => 'Amazon Instant Video',  
			                              					   'Vehicles'             => 'Vehicles',  
										   					   'VideoGames'           => 'Video Games',  

			                              					   'Wine'                 => 'Wine',  
										   					   'Wireless'             => 'Cell Phones & Accessories',  
															   
										 					);
															
		    $categories_withsort_params['sortParams']['All'] = array();
		    $categories_withsort_params['sortParams']['Appliances']      =   array( 'salesrank',
																		   		'pmrank',
																		   		'price',
																		   		'-price',
																				'relevancerank',
																		        'reviewrank',
																				'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['ArtsAndCrafts']  =   array(  'salesrank',
																		   		    'pmrank',
																				    'reviewrank',
																		   		    'price',
																		   			'-price',
																					'relevancerank',																		        																			'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Automotive']    =   array( 'salesrank',
			                                                                    'titlerank',
																				'-titlerank',  
																		   		'price',
																		   		'-price',
																				'relevancerank'
			                                                             );

		    $categories_withsort_params['sortParams']['Baby']         =   array(  'salesrank',
																		   'psrank',
																		   'price',
																		   '-price',
																		   'titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['Beauty']       =   array( 'salesrank',
			                                                                '-launch-date',
																			'sale-flag', 
																		   	'pmrank',
																		   	'price',
																		   	'-price'
			                                                             );
		    $categories_withsort_params['sortParams']['Blended']      = array();
		    $categories_withsort_params['sortParams']['Books']        =   array( 'salesrank',
			                                                               'pricerank', 
																		   'inverse-pricerank',
																		   'daterank',
			                                                               'titlerank',
																		   '-titlerank',  
																		   '-unit-sales',
																		   'price',
																		   '-price',
																		   'relevancerank',
																		   'reviewrank',
																		   '-publication_date'
			                                                             );
		    $categories_withsort_params['sortParams']['Collectibles']  =   array( 'salesrank',
																		   		'pmrank',
																		   		'price',
																		   		'-price',
																				'relevancerank',
																		        'reviewrank',
																				'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Electronics']  =   array( 'salesrank',
			                                                                     'titlerank', 
																		   		 'price',
																		   		 '-price',
																				 'pmrank', 
																		         'reviewrank'
			                                                             );
		    $categories_withsort_params['sortParams']['Fashion']      =   array( 'popularity-rank',
																		   	 'price',
																		   	 '-price',
																			 'relevancerank',
																		     'reviewrank',
																			 'launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['FashionBaby']  =  array( 'popularity-rank',
																		   	    'price',
																		   	    '-price',
																			    'relevancerank',
																		        'reviewrank',
																			    'launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['FashionBoys']  =  array( 'popularity-rank',
																		   	    'price',
																		   	    '-price',
																			    'relevancerank',
																		        'reviewrank',
																			    'launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['FashionGirls'] =  array( 'popularity-rank',
																		   	    'price',
																		   	    '-price',
																			    'relevancerank',
																		        'reviewrank',
																			    'launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['FashionMen']   =  array( 'popularity-rank',
																		   	    'price',
																		   	    '-price',
																			    'relevancerank',
																		        'reviewrank',
																			    'launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['FashionWomen'] =  array( 'popularity-rank',
																		   	    'price',
																		   	    '-price',
																			    'relevancerank',
																		        'reviewrank',
																			    'launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['GiftCards']    =   array( 'salesrank',
																		   		'price',
																		   		'-price',
																				'relevancerank',
																		        'reviewrank',
																				'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Grocery']     =   array(   'salesrank',
																		   		'pricerank',
																		   		'inverseprice',
																		   		'launch-date',
																				'sale-flag',
																				'relevancerank'
			                                                             );
		    $categories_withsort_params['sortParams']['Handmade']    =   array(  'featured',
																		   		'price',
																		   		'-price',
																				'relevancerank',
																		        'reviewrank',
																				'-release-date'
			                                                             );
		    $categories_withsort_params['sortParams']['HealthPersonalCare']  =   array( 'salesrank',
																		   		   		'pmrank',
																						'inverseprice',
																		   				'pricerank',
																		        		'launch-date',
																						'sale-flag'
			                                                             );
		    $categories_withsort_params['sortParams']['HomeGarden']      =   array( 'salesrank',
																		   		'price',
																		   		'-price',
																				'titlerank',
																		        '-titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['Industrial']      =   array( 'salesrank',
			                                                                    'pmrank',
																		   		'price',
																		   		'-price',
																				'titlerank',
																		        '-titlerank'
			                                                             );
		    $categories_withsort_params['sortParams']['KindleStore']     =   array( 'salesrank',
																		   		 'daterank',
																		   		 'price',
																		   		 '-price',
																				 'relevancerank',
																		         'reviewrank',
																				 '-edition-sales-velocity'
			                                                             );
		    $categories_withsort_params['sortParams']['LawnAndGarden']   =   array( 'salesrank',
																		   		   'price',
																		   		   '-price',
																				   'relevancerank',
																		           'reviewrank',
																				   'reviewrank_authority'
			                                                             );
		    $categories_withsort_params['sortParams']['Luggage']         =   array( 'popularity-rank',
																		   	 'price',
																		   	 '-price',
																			 'relevancerank',
																		     'reviewrank',
																			 'launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['Magazines']       =   array(  'subslot-salesrank',
																		   		'daterank',
																		   		'price',
																		   		'-price',
																				'titlerank',
																				'-titlerank',
																				'-unit-sales',
																		        'reviewrank',
																				'-publication_date'
			                                                             );
		    $categories_withsort_params['sortParams']['Marketplace']     =   array( 'salesrank',
																		   		 'pmrank',
																		   		 'price',
																		   		 '-price',
																				 'titlerank',
																				 '-titlerank',																				
																				 '-launch-date',
																				 'relevancerank'
			                                                             );
		    $categories_withsort_params['sortParams']['Merchants']      =   array(  'salesrank',
																		   		'pmrank',
																		   		'price',
																		   		'-price',
																				'relevance',
																				'relevancerank',
																		        'pricerank',
																				'inverseprice',
																				'launch-date',
																				'-launch-date'
			                                                             );
		    $categories_withsort_params['sortParams']['MobileApps']     =   array( 'pmrank',
																		   		'price',
																		   		'-price',
																				'relevancerank',
																		        'reviewrank',
																				'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Movies']         =   array( 'featured',
																		   	'price',
																		   	'-price',
																			'relevancerank',
																		    'reviewrank',
																			'-release-date'
			                                                             );

		    $categories_withsort_params['sortParams']['MP3Downloads']    =   array(  'salesrank',
																		   		   'price',
																		   		   '-price',
																				   'relevancerank',
																				   '-releasedate'
			                                                             );

		    $categories_withsort_params['sortParams']['Music']           =   array( 'salesrank',
			                                                               'psrank',
																		   'price',
																		    '-price',
																			'titlerank',
																			'-titlerank',
																			'artistrank',
																			'orig-rel-date',
																			'-orig-rel-date',
																			'release-date',
																			'releasedate',
																			'-releasedate',
																		    'relevancerank'
			                                                             );

		    $categories_withsort_params['sortParams']['MusicalInstruments']  =   array( 'pmrank',
			                                                                            'salesrank', 
																		   				'price',
																		   		        '-price',
																				        '-launch-date',
																				        'sale-flag'
			                                                             );

		    $categories_withsort_params['sortParams']['OfficeProducts']  =   array( 'pmrank',
			                                                                        'salesrank',
																		   		    'price',
																		   		    '-price',
																					'titlerank',
																		            'reviewrank'
			                                                             );															

		    $categories_withsort_params['sortParams']['Pantry']       =   array( 'relevancerank',
																		   	'price',
																		   	'-price',
																		    'reviewrank'
			                                                             );

		    $categories_withsort_params['sortParams']['PCHardware']   =   array( 'psrank',
			                                                                    'salesrank',
																		   		'price',
																		   		'-price',
																				'titlerank'
			                                                             );

		    $categories_withsort_params['sortParams']['PetSupplies']  =   array( 'salesrank',
																		   		 'price',
																		   		 '-price',
																				 'titlerank',
																				 '-titlerank',
																				 'relevance',
																				 'relevancerank',
																		         'reviewrank',
																				 'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Software']     =   array( 'pmrank',
			                                                                  'salesrank',
																		   	  'price',
																		   	  '-price',
																			  'titlerank'
			                                                             );

		    $categories_withsort_params['sortParams']['SportingGoods'] =   array(  'relevance-fs-rank',
			                                                                        'pricerank',
																					'salesrank', 
																					'inverseprice',
																					'launch-date',
																					'sale-flag',
																		   		    'price',
																		   		    '-price',
																				    'relevancerank',
																				    'reviewrank_authority'
			                                                             );

		    $categories_withsort_params['sortParams']['Tools']         =   array( 'pmrank',
			                                                               'salesrank', 
																		   'price',
																		   '-price',
																		   'titlerank',
																		   '-titlerank'
			                                                             );

		    $categories_withsort_params['sortParams']['Toys']          =   array( 'pmrank',
			                                                              'salesrank',
																		  'price',
																		  '-price',
																		  'titlerank',
																		  '-age-min'
			                                                             );

		    $categories_withsort_params['sortParams']['UnboxVideo']    =   array( 'salesrank',
																		   		'price',
																		   		'-price',
																				'titlerank',
																				'relevancerank',
																		        '-video-release-date',
																				'-launch-date'
			                                                             );

		    $categories_withsort_params['sortParams']['Vehicles']      =   array( 'featured',
																			  '-release-date',
																			  'relevancerank',
																		      'reviewrank'
			                                                             );

		    $categories_withsort_params['sortParams']['VideoGames']    =   array( 'pmrank',
			                                                                    'salesrank',
																		   		'price',
																		   		'-price',
																				'titlerank'
			                                                             );

		    $categories_withsort_params['sortParams']['Wine']          =   array( 'featured',
																		  'price',
																		  '-price',
																		  'relevancerank',
																		  'reviewrank',
																		  'reviewscore'
			                                                             );

		    $categories_withsort_params['sortParams']['Wireless']      =   array( 'salesrank',
			                                                                  'pricerank',
																		   	  'inverse-pricerank',
																			  'titlerank',
																		      '-titlerank',
																		      'reviewrank',
																			  'daterank'
			                                                             );


			break; // End com
			
		} // End Switch
		return $categories_withsort_params;
	}
	
	public static function create_shop_homepage() {		
		$page_name = 'amazon-product-shop';	
		$page = get_page_by_path( $page_name );		
		if ( ! isset( $page->ID ) ) {
			$current_user = wp_get_current_user();
			$postarr = array(  
			                'post_type'     => 'page',
							'post_name'     => $page_name,
							'post_title'    => 'Amazon Shop', 
							'post_author'   =>  1,
							'post_status'   => 'publish'
						  );			
		  $amazonshop_frontpageid = wp_insert_post( $postarr );				  
		  update_option( 'caaps_amazonshop_frontpageid', $amazonshop_frontpageid );
		}
	}
	
	public static function caaps_admin_notices() {
		$admin_notice = false;
		$options = get_option('caaps_amazon-product-shop-settings');
		if ( ! isset( $options['caaps_settings_field_accesskeyid']) || 
			empty( $options['caaps_settings_field_accesskeyid'] ) ) {
			$admin_notice = true;
		}		
		if ( ! isset( $options['caaps_settings_field_secretaccesskey']) || 
			empty( $options['caaps_settings_field_secretaccesskey'] ) ) {
			$admin_notice = true;
		}

		if ( ! isset( $options['caaps_settings_field_country'] ) || 
			empty( $options['caaps_settings_field_country'] ) ) {
			$admin_notice = true;
		}
		
		if ( $admin_notice ) {
			$url = admin_url( 'edit.php?post_type=amazonproductshop&page=caaps_amazon-product-shop-settings' );
			$alink = '<a href="'.$url.'">Click to add.</a>';
			printf('<div class="notice notice-warning is-dismissible">');
		    printf('<div class="caaps-amazonshop-notice-wrapper"><h3><span class="dashicons dashicons-products"></span> CodeShop Amazon Affiliate:</h3> <h4>Amazon Access Key , Serect Access Key, Associate ID and Country settings required! Please add them through admin menu CodeShop -> Settings page. ' .$alink.'</h4></div>');
	        printf('</div>');
		}
		
	}
	
	public static function amazonshop_row_link( $actions, $plugin_file ) {
		$codeshop_plugin = plugin_basename( AMZONPRODUCTSHOP_PLUGIN_DIR );
		$plugin_name = basename($plugin_file, '.php');
		if ( $codeshop_plugin == $plugin_name ) {
			$doclink[] = '<a href="https://codeapple.net/codeshop-amazon-affiliate/documentation/" title="CodeShop Amazon Affiliate Plugin Documentation" target="_blank">Documentation</a>';	
			$doclink[] = '<a href="https://codeapple.net/codeshop-amazon-affiliate/forum/" title="CodeShop Amazon Affiliate Plugin Forum Help" target="_blank">Forum</a>';	
			return array_merge( $actions, $doclink );
		}
		return $actions;
	}	
	
} // End class