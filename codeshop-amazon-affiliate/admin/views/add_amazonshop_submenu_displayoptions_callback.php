<?php 
// check if the user have submitted the settings
if ( isset($_GET['settings-updated'] ) ) {  	
   add_settings_error('caaps_displayoptions_messages', 'caaps_displayoptions_message', __('Settings Saved', 'codeshop-amazon-affiliate'), 'updated');
}
// show error / update messages
settings_errors('caaps_displayoptions_messages');
?>
<div class="wrap">
  <form action="options.php" method="post">
      <?php
      settings_fields('caaps_amazon-product-shop-displayoptions');
      do_settings_sections('caaps_amazon-product-shop-displayoptions');
      submit_button('Save Settings');
      ?>
  </form>   
</div><!-- /.wrap -->  
