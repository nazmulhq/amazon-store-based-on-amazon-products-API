<div class="wrap">    		    	
	<table class="widefat" style="border:none;">
    	<thead>
        </thead>        
        <tbody>        	
        	<?php 
			$total_products = ( isset( $processed_responses ) && count( $processed_responses ) > 0 )? count( $processed_responses ) : 0;
			$i = 0;
			if ( $total_products > 0 ) { ?>
            		
                    <tr>                        
                        <td colspan="2">                        
                          <?php
						  $display_resultpage = Caaps_Amazon_Ajax_Handler::$result_page;
						  // If search category 'All' then Amazon allows to send requests 1-5 pages
						  // If search category is not 'All' then Amazon allows to send requests 1-10 pages
						  if ( Caaps_Amazon_Ajax_Handler::$search_category == 'All' ) { 
						  		$max_resultpages   = Caaps_Amazon_Response_Process::$total_resultpages > 5? 5 : Caaps_Amazon_Response_Process::$total_resultpages;
						  }
						  else {
							  $max_resultpages     = Caaps_Amazon_Response_Process::$total_resultpages > 10? 10 : Caaps_Amazon_Response_Process::$total_resultpages;
						  }
						  ?>                          
                          <ul class="caaps-search-pages">
                              <li title="Click page number to get results">Search Result Page(s):</li>
							  <?php 
                              for ( $page = 1; $page <= $max_resultpages; $page++ ) {
                                    if ( $display_resultpage == $page ) {
                                        echo '<li class="active">'.$page.'</li>';
                                    }
                                    else {
                                        echo '<li><a href="javascript:void(0)" title="Click to get results" class="caaps_search_page_number">'.$page.'</a></li>';
                                    }
                              }?>
                          </ul>                          
                        </td>
                    </tr>
                    
                    <tr style="background-color:#e9f6f6;">
                    	<td style="text-align:center;">
                        	<?php submit_button( __( 'Add Selected Products', 'codeshop-amazon-affiliate' ), 'primary caaps_addselected_products_btn', 'caaps_addselected_products_btn', false, $other_attributes = array( 'id' => 'caaps_addselected_products_btn' ) );?>                           
                        </td>
                        <td style="text-align:center;">
                        	<input type="checkbox" id="caaps_select_deselect_allproducts" class="caaps_select_deselect_allproducts" value="1" checked="checked"  /> 
                            <label for="caaps_select_deselect_allproducts"><strong><?php _e('Select / De-Select All Products', 'codeshop-amazon-affiliate');?></strong></label>
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
								echo ( isset( $processed_responses[$i]['MediumImage'] ) && ( ! empty( $processed_responses[$i]['MediumImage'] )) )? '<div style="width:auto;height:160px;margin-bottom:15px;"><img src="'.$processed_responses[$i]['MediumImage'].'" alt="" style="height:160px;" /></div>' : '';
								
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