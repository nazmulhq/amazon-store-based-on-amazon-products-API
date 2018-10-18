<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Caaps_Amazon_Helper {
	
	public static function is_post_edit_page() {
		global $pagenow;
		global $typenow;		
		if( 'amazonproductshop' == $typenow && 'post.php' == $pagenow ) { 
			return true;
		}
		else {
			return false;
		}		
	}
	
	public static function get_editpage_postid() {
		global $pagenow;
		global $typenow;		
		if( 'amazonproductshop' == $typenow && 'post.php' == $pagenow ) { 
			global $post;
			return $post->ID;
		}
		else {
			return false;
		}				
	}
	
} // End Class
?>