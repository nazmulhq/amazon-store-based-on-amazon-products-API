<?php
/**
 * Plugin Name: CodeShop Amazon Affiliate
 * Plugin URI:  https://codeapple.net/codeshop-amazon-affiliate/
 * Description: CodeShop Amazon Affiliate plugin will help you to create a complete amazon store from all available amazon product categories of different supported countries, add thousands product by         creating different categories and add products accordingly. Also support adding products with Wordpress regular posts and pages. Make ready your site to look same as your active theme with easy             customizable templates to advertise amazon products to sell and earn commissions. 
 * Version:     2.0.0
 * Author:      CodeApple
 * Author URI:  https://codeapple.net/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: codeshop-amazon-affiliate
 * Domain Path: /languages
 */
 
 /*
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
  
  Copyright 2017 codeapple.net
 */
 
  if ( ! defined( 'ABSPATH' ) ) {
	  exit; // Exit if accessed directly.
  }
  
  define( 'AMZONPRODUCTSHOP_VERSION', '2.0.0' );
  define( 'AMZONPRODUCTSHOP_MINIMUM_PHP_VERSION', '5.3.0' );
  define( 'AMZONPRODUCTSHOP_MINIMUM_WP_VERSION', '4.4' );
  define( 'AMZONPRODUCTSHOP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );    
   
  // Amazon products global variable 
  global $amazon_products;
      
  require_once( AMZONPRODUCTSHOP_PLUGIN_DIR . 'vendor/autoload.php' );       
  function caaps_autoloader( $caaps_class ) {	  	  
	  $classfile = get_include_path().strtolower( str_replace( '_', '-' , $caaps_class ) ).'.class.php';
	  if ( file_exists( $classfile ) ) {
		  require_once( $classfile );
	  }
  }  
  set_include_path( dirname(__FILE__) . '/includes/');    
  spl_autoload_register('caaps_autoloader');  
  new Caaps_Autoloader();
  register_activation_hook( __FILE__, array( 'Caaps_Amazon_Shop','activate_amazonshop') );      