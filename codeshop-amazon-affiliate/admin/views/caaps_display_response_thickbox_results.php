<div class="wrap">    		    	
	<table class="widefat" style="border:none;">
    	<thead>
        </thead>        
        <tbody>        	
        	<?php 
			$total_products = ( isset( $processed_responses ) && count( $processed_responses ) > 0 )? count( $processed_responses ) : 0;
			$i = 0;
			if ( $total_products > 0 ) { ?>
            		<tr style="background-color:#e9f6f6;">
                    	<td style="text-align:center;">
                        	<?php submit_button( __( 'Add Shortcode', 'codeshop-amazon-affiliate' ), 'primary caaps_addshortcode_products_btn', 'caaps_addshortcode_products_btn', false, $other_attributes = array( 'id' => 'caaps_addshortcode_products_btn' ) );?>                           
                        </td>
                        <td style="text-align:center;">
                        	<input type="checkbox" id="caaps_select_deselect_thickbox_allproducts" class="caaps_select_deselect_thickbox_allproducts" value="1" checked="checked"  /> 
                            <label for="caaps_select_deselect_thickbox_allproducts"><strong><?php _e('Check / Uncheck Products', 'codeshop-amazon-affiliate');?></strong></label>
                        </td>
                    </tr>		
                    <tr>
                    	<td class="caaps-display-message-trow" colspan="2" style="text-align:center; font-size:18px; font-weight:bold;">
                        </td>
                    </tr>		
                    <tr>
                    	<td colspan="2"><hr /></td>
                    </tr>
            <?php    
				while ( $i < $total_products ) {
					?>
                    <tr>
                    	<td style="text-align:center;">
							<?php 
							if ( isset( $processed_responses[$i]['ASIN'] ) && ! empty( $processed_responses[$i]['ASIN'] )  ) {	
								
								echo ( isset( $processed_responses[$i]['MediumImage'] ) && ( ! empty( $processed_responses[$i]['MediumImage'] )) )? '<div style="width:auto;height:160px;margin-bottom:15px;"><img src="'.$processed_responses[$i]['MediumImage'].'" alt="" /></div>' : '';
								
								echo '<input type="checkbox" id="caaps_chkboxid_'.$processed_responses[$i]['ASIN'].'" class="caaps-add-amazonproducts-chkbox" name="caaps-add-amazonproducts-chkbox[]" value="'.$processed_responses[$i]['ASIN'].'" checked="checked" />';
								echo ( isset( $processed_responses[$i]['Title'] ) && ! empty( $processed_responses[$i]['Title'] ) )? '<label for="caaps_chkboxid_'.$processed_responses[$i]['ASIN'].'">'.$processed_responses[$i]['Title'] . '</label>' : '';
								
								echo ( isset( $processed_responses[$i]['LowestNewPriceFormattedPrice'] ) && ! empty( $processed_responses[$i]['LowestNewPriceFormattedPrice'] ) )? '<p>'.$processed_responses[$i]['LowestNewPriceFormattedPrice'].'</p>' : '';																				
								$i++;
							}
                            ?>                        
                        </td>
                        <td style="text-align:center;">
							<?php 
							if ( isset( $processed_responses[$i]['ASIN'] ) && ! empty( $processed_responses[$i]['ASIN'] )  ) {
								echo ( isset( $processed_responses[$i]['MediumImage'] ) && ( ! empty( $processed_responses[$i]['MediumImage'] )) )? '<div style="width:auto;height:160px;margin-bottom:15px;"><img src="'.$processed_responses[$i]['MediumImage'].'" alt="" /></div>' : '';
								
								echo '<input type="checkbox" id="caaps_chkboxid_'.$processed_responses[$i]['ASIN'].'" class="caaps-add-amazonproducts-chkbox" name="caaps-add-amazonproducts-chkbox[]" value="'.$processed_responses[$i]['ASIN'].'" checked="checked" />';
								echo ( isset( $processed_responses[$i]['Title'] ) && ! empty( $processed_responses[$i]['Title'] ) )? '<label for="caaps_chkboxid_'.$processed_responses[$i]['ASIN'].'">'.$processed_responses[$i]['Title'] . '</label>' : '';
								
								echo ( isset( $processed_responses[$i]['LowestNewPriceFormattedPrice'] ) && ! empty( $processed_responses[$i]['LowestNewPriceFormattedPrice'] ) )? '<p>'.$processed_responses[$i]['LowestNewPriceFormattedPrice'].'</p>' : '';													
								$i++;
							}
                            ?>                                                
                        </td>
                    </tr>
			<?php	                
				}
			}
			else {
				_e( 'No Products Found', 'codeshop-amazon-affiliate' );
			}
			?>
            
        </tbody>        
    </table>    
</div><!-- /.wrap -->