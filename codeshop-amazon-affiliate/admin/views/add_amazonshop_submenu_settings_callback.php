<?php 
// check if the user have submitted the settings
if ( isset($_GET['settings-updated'] ) ) {
   add_settings_error('caaps_settings_messages', 'caaps_setting_message', __('Settings Saved', 'codeshop-amazon-affiliate'), 'updated');
}
// show error / update messages
settings_errors('caaps_settings_messages');
?>
<div class="wrap">
  <form action="options.php" method="post">
      <?php
      settings_fields('caaps_amazon-product-shop-settings');
      do_settings_sections('caaps_amazon-product-shop-settings');
      submit_button('Save Settings');
      ?>
  </form>
   
   <?php
        $test_settings = true;
   		$options = get_option('caaps_amazon-product-shop-settings');		
		$accesskeyid = $options['caaps_settings_field_accesskeyid'];
		$secretaccesskey = $options['caaps_settings_field_secretaccesskey'];
		$associateid = $options['caaps_settings_field_associateid'];		
		if ( ! isset( $accesskeyid ) || empty( $accesskeyid ) ) {
			$test_settings = false;
		}
		if ( ! isset( $secretaccesskey ) || empty( $secretaccesskey ) ) {
			$test_settings = false;
		}
		if ( ! isset( $associateid ) || empty( $associateid ) ) {
			$test_settings = false;
		}		   
		 if ( $test_settings ) {
   ?>
           <hr />
           <table style="width:100%">
                <thead></thead>
                <tbody>
                    <tr>
                        <td style="padding-left:0; width:20%;">
                            <a href="Javascript:void(0);" class="caaps_testapi_settings button">Test Settings</a>
                        </td>
                        <td class="caaps_testapi_message"></td>
                    </tr>
                </tbody>
           </table>
           <hr />
   <?php } ?>
   <p>
     See instructions for retrieving your Access Key ID and Secret Access Key. 
     <a href="https://affiliate-program.amazon.com/gp/advertising/api/detail/your-account.html" target="_blank">Get Security Credentials</a><br /><br />
     
     
     Associates earn commissions by using their own websites to refer sales to Amazon.com. To get a commission, an     Associate     must have an Associate ID, also known as an Associate tag. The Associate ID is an automatically generated     unique identifier that you will need to make requests through the Product Advertising API. To know more about becoming an Associate <a href="http://docs.aws.amazon.com/AWSECommerceService/latest/DG/becomingAssociate.html" target="_blank">please click here.</a><br />    
    Your Associate ID works only in the locale in which you register. If you want to be an Associate in more than one locale, you must register separately for each locale. Use the locale you want to register -
   </p>
      
  <table class="widefat" cellspacing="0" border="1">
      <thead>
          <tr><th>Locale</th><th>URL</th></tr>
      </thead>
      
      <tbody>            
          <tr><td>Brazil</td><td><p><a  href="https://associados.amazon.com.br" target="_blank">https://associados.amazon.com.br</a></p></td></tr>
          
          <tr><td>Canada</td><td><p><a  href="https://associates.amazon.ca" target="_blank">https://associates.amazon.ca</a></p></td></tr>
                          
          <tr><td>China</td><td><p><a  href="https://associates.amazon.cn" target="_blank">https://associates.amazon.cn/gp/advertising/api/detail/main.html</a></p></td></tr>

          <tr><td>France</td><td><p><a  href="https://partenaires.amazon.fr" target="_blank">https://partenaires.amazon.fr</a></p>
          
          </td></tr><tr><td>Germany</td><td><p><a  href="https://partnernet.amazon.de" target="_blank">https://partnernet.amazon.de</a></p></td></tr>
                               
          <tr><td>India</td><td><p><a  href="https://affiliate-program.amazon.in" target="_blank">https://affiliate-program.amazon.in</a></p></td></tr>
          
          <tr><td>Italy</td><td><p><a  href="https://programma-affiliazione.amazon.it" target="_blank">https://programma-affiliazione.amazon.it/gp/advertising/api/detail/main.html</a></p></td></tr>
          
          <tr><td>Japan</td><td><p><a  href="https://affiliate.amazon.co.jp" target="_blank">https://affiliate.amazon.co.jp</a></p></td></tr>
          
          <tr><td>Mexico</td><td><p><a  href="https://afiliados.amazon.com.mx/gp/associates/join/landing/main.html" target="_blank">https://afiliados.amazon.com.mx/gp/associates/join/landing/main.html</a></p></td></tr>
          
          <tr><td>Spain</td><td><p><a  href="https://afiliados.amazon.es" target="_blank">https://afiliados.amazon.es</a></p></td></tr>
          
          <tr><td>United Kingdom</td><td><p><a  href="https://affiliate-program.amazon.co.uk" target="_blank">https://affiliate-program.amazon.co.uk</a></p></td></tr>
          
          <tr><td>United States</td><td><p><a  href="https://affiliate-program.amazon.com" target="_blank">https://affiliate-program.amazon.com</a></p></td></tr>
      </tbody>
  </table>     
  <p>Since products advertising API used to get all amazon products related data, images, audio, video, logos from amazon site so      you need to agree Amazon.com Product Advertising API <a href="https://affiliate-program.amazon.com/gp/advertising/api/detail/agreement.html" target="_blank">License Agreement</a> </p>
    
</div><!-- /.wrap -->  
