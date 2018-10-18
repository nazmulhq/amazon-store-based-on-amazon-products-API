<div class="wrap">    		    
	<?php 
    $post_id = $post->ID;
    $meta_key = '_caaps_added_products_'.$post_id;
    $addedproducts = get_post_meta( $post_id, $meta_key );		
	// If added products asins found then show
	if ( isset( $addedproducts[0] ) && count( $addedproducts[0] ) > 0 ) {		        
		Caaps_Amazon_Ajax_Handler::$search_type = 'itemlookup';
		Caaps_Amazon_Ajax_Handler::$search_asin = $addedproducts[0];
		// Use transient mode to load faster added products
		$processed_responses = Caaps_Amazon_Ajax_Handler::amazonsearch_products_transientmode();
	}	
    ?>                
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
                        	<?php submit_button( __( 'Remove Selected Products', 'amazon-product-shop' ), 'primary caaps_removeselected_products_btn', 'caaps_removeselected_products_btn', false, $other_attributes = array( 'id' => 'caaps_removeselected_products_btn' ) );?>                           
                        </td>
                        <td style="text-align:center;" colspan="2">
                        	<input type="checkbox" id="caaps_remove_select_deselect_allproducts" class="caaps_remove_select_deselect_allproducts" value="1" checked="checked"  /> 
                            <label for="caaps_remove_select_deselect_allproducts"><strong><?php _e('Select / De-Select All Products', 'amazon-product-shop');?></strong></label>
                        </td>
                    </tr>		
                    <tr>
                    	<td class="caaps-remove-products-display-message-trow" colspan="3" style="text-align:center; font-size:18px; font-weight:bold;">
                        </td>
                    </tr>		
                    <tr>
                    	<td colspan="3"><hr /></td>
                    </tr>        
        	<?php 
				while ( $i < $total_products ) { ?>
                <tr>
                	<td>
						<?php 
						if ( isset( $processed_responses[$i]['ASIN'] ) && ! empty( $processed_responses[$i]['ASIN'] ) ) {
							echo ( isset( $processed_responses[$i]['SmallImage'] ) && ( ! empty( $processed_responses[$i]['SmallImage'] )) )? '<img src="'.$processed_responses[$i]['SmallImage'].'" alt="" /><br />' : '';
							echo '<input type="checkbox" id="caaps_removechkboxid_'.$processed_responses[$i]['ASIN'].'" class="caaps-remove-amazonproducts-chkbox" name="caaps-remove-amazonproducts-chkbox[]" value="'.$processed_responses[$i]['ASIN'].'" checked="checked" />';
							echo ( isset( $processed_responses[$i]['Title'] ) && ! empty( $processed_responses[$i]['Title'] ) )? '<label for="caaps_removechkboxid_'.$processed_responses[$i]['ASIN'].'">'.$processed_responses[$i]['Title'] . '</label><br />' : '';
							echo ( isset( $processed_responses[$i]['LowestNewPriceFormattedPrice'] ) && ! empty( $processed_responses[$i]['LowestNewPriceFormattedPrice'] ) )? $processed_responses[$i]['LowestNewPriceFormattedPrice'] . '<br />' : '';													
							$i++;
						}
                        ?>                    
                    </td>
                    
                    <td>
						<?php 
						if ( isset( $processed_responses[$i]['ASIN'] ) && ! empty( $processed_responses[$i]['ASIN'] ) ) {
							echo ( isset( $processed_responses[$i]['SmallImage'] ) && ( ! empty( $processed_responses[$i]['SmallImage'] )) )? '<img src="'.$processed_responses[$i]['SmallImage'].'" alt="" /><br />' : '';
							echo '<input type="checkbox" id="caaps_removechkboxid_'.$processed_responses[$i]['ASIN'].'" class="caaps-remove-amazonproducts-chkbox" name="caaps-remove-amazonproducts-chkbox[]" value="'.$processed_responses[$i]['ASIN'].'" checked="checked" />';
							echo ( isset( $processed_responses[$i]['Title'] ) && ! empty( $processed_responses[$i]['Title'] ) )? '<label for="caaps_removechkboxid_'.$processed_responses[$i]['ASIN'].'">'.$processed_responses[$i]['Title'] . '</label><br />' : '';
							echo ( isset( $processed_responses[$i]['LowestNewPriceFormattedPrice'] ) && ! empty( $processed_responses[$i]['LowestNewPriceFormattedPrice'] ) )? $processed_responses[$i]['LowestNewPriceFormattedPrice'] . '<br />' : '';													
							$i++;
						}
                        ?>                                        
                    </td>
                    
                    <td>
						<?php 
						if ( isset( $processed_responses[$i]['ASIN'] ) && ! empty( $processed_responses[$i]['ASIN'] ) ) {
							echo ( isset( $processed_responses[$i]['SmallImage'] ) && ( ! empty( $processed_responses[$i]['SmallImage'] )) )? '<img src="'.$processed_responses[$i]['SmallImage'].'" alt="" /><br />' : '';
							echo '<input type="checkbox" id="caaps_removechkboxid_'.$processed_responses[$i]['ASIN'].'" class="caaps-remove-amazonproducts-chkbox" name="caaps-remove-amazonproducts-chkbox[]" value="'.$processed_responses[$i]['ASIN'].'" checked="checked" />';
							echo ( isset( $processed_responses[$i]['Title'] ) && ! empty( $processed_responses[$i]['Title'] ) )? '<label for="caaps_removechkboxid_'.$processed_responses[$i]['ASIN'].'">'.$processed_responses[$i]['Title'] . '</label><br />' : '';
							echo ( isset( $processed_responses[$i]['LowestNewPriceFormattedPrice'] ) && ! empty( $processed_responses[$i]['LowestNewPriceFormattedPrice'] ) )? $processed_responses[$i]['LowestNewPriceFormattedPrice'] . '<br />' : '';													
							$i++;
						}
                        ?>                                        
                    </td>

                </tr>	
           <?php     
				} // End while			
			}
			?>       	                
        </tbody>        
    </table>    
</div><!-- /.wrap -->