<div class="wrap">    		
	<table class="widefat" style="border:none;">
    	<thead>
        </thead>        
        <tbody>
        	<tr>
            	<td style="width:30%;">
                	<label for="caaps_search_asin"><?php esc_html_e('Search Product By ASIN', 'amazon-product-shop');?></label>
                </td>
                <td>
                	<textarea id="caaps_search_asin" name="caaps_search_asin" class="caaps_search_asin" style="width:90%;" placeholder="Each ASIN per line / Comma separated" rows="6"></textarea>
                </td>
                <td style="text-align:right;">
                    <?php submit_button( __( 'Search', 'amazon-product-shop' ), 'primary caaps_searchby_asin_btn', 'caaps_searchby_asin_btn', false, $other_attributes = array( 'id' => 'caaps_searchby_asin_btn' ) );?>
                </td>
            </tr>               
        </tbody>        
    </table>    
</div><!-- /.wrap -->