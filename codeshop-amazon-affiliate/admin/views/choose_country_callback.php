<div class="wrap">    	
	<input type="hidden" class="caaps-products-toadd-postid" value="<?php echo $post->ID;?>" />    	    
	<table class="widefat" style="border:none;">
    	<thead>
        </thead>        
        <tbody>
        	<tr>
            	<td style="width:30%;">
                	<label for="caaps_locale_field"><?php esc_html_e('Selected Country', 'codeshop-amazon-affiliate');?></label>
                </td>
                <td>
                <?php $options = get_option('caaps_amazon-product-shop-settings'); ?>
                   <select name="caaps_locale_field" id="caaps_locale_field" style="width:60%;" disabled="disabled">
					   <?php 
                       foreach ( $countries as $cc => $country) {
						    $selected = isset( $options['caaps_settings_field_country'] ) ? selected( $options['caaps_settings_field_country'], $cc, false) : '';
                           echo '<option value="'.$cc.'" ' .$selected.'>'.$country.'</option>';
                       }
                       ?>
                   </select>                        
                </td>
            </tr>                                       
        </tbody>        
    </table>    
</div><!-- /.wrap -->