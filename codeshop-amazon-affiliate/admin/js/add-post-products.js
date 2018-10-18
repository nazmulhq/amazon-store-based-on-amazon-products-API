jQuery(document).ready(function($) {
	
	// Click on AmazonShop media button
	$('#caaps_insert_shortcode').click(function() {	
		 tb_show('Add Amazon Products', '#TB_inline?inlineId=caaps-addproducts-withpost-container',false); 
		 resize_thickbox();
	 });
	 
	// Get Keyword Input
	$('body').on("click", ".caaps_thickbox_searchby_kword_btn", function() {			
		var searchCountry  = $('#caaps_country_field').val(); 
		var searchKword    = $('.caaps_thickbox_search_kword').val();		
		var searchCategory = $('#caaps_search_category').val();
		var sortBy         = $('#caaps_search_sorting').val();		
		// Initial default search result page number
		var resultPage     = 1;				
		$('.caaps-display-thickbox-results').html('<p style="text-align:center; font-size:18px; font-weight:bold;">'+caaps_addproducts_script_obj.product_searching_msg+'<br/>'+caaps_addproducts_script_obj.product_searching_loadimage+'</p>');						
		$.post( caaps_addproducts_script_obj.adminajax_url, 
		        {	
				   action: "caaps_searchby_kword_display",
				   search_country: searchCountry, 
				   search_kword: searchKword,
				   search_category: searchCategory,
				   sort_by: sortBy,
				   result_page: resultPage,				   				   
				   thickbox_search: true,
				   security: caaps_addproducts_script_obj.nonce	
				}, function(data) {
					$('.caaps-display-thickbox-results').html(data);
		           }
	    );		
		return false;		
	});	 
	 
	// Check search category dropdwon presence then show sort parameter as options value for default selected search Category
	if ( $('select').hasClass( 'caaps_search_category' ) ) {
		var searchCategory = $('#caaps_search_category').val().toLowerCase();
		var sortParams = $('.caaps_sortparams_'+searchCategory).val();
		//console.log(sortParams);
	}
	
	// On change Search category update sort paramaters
	$('#caaps_search_category').change(function() {
		var searchCategory = $(this).val().toLowerCase();
		console.log(searchCategory);
		var sortParams = $('.caaps_sortparams_'+searchCategory).val().split(",");
		//console.log(sortParams);		
		if ( sortParams.length > 0 ) {
			var selectOpts = '<option value="">'+caaps_addproducts_script_obj.sort_by+'</option>';
			for ( var i = 0; i < sortParams.length; i++ ) {
				selectOpts += '<option value="'+sortParams[i]+'">'+sortParams[i]+'</option>';
			}
			//console.log( selectOpts );
			$('#caaps_search_sorting').html(selectOpts);
		}
	});	 
	 
	// Add Shortcode button click on thickbox results window
	$('body').on("click", ".caaps_addshortcode_products_btn", function() {		
				
		$('.caaps-display-message-trow').html(caaps_addproducts_script_obj.adding_products_msg);
		var selectedProducts = [];		
		$('.caaps-add-amazonproducts-chkbox').each(function() {					    
			if ( $(this).is(":checked") ) {
				selectedProducts.push($(this).val());
			}
		});		
		if ( selectedProducts.length == 0 ) {
			$('.caaps-display-message-trow').html(caaps_addproducts_script_obj.no_products_selected_msg);	
		}
		else {
			var PostID = '';
			var templateName = $('#caaps_shortcode_template').val(); 			
			PostID = $('.caaps-products-toadd-postid').val();		
			var createdShortcode = caaps_create_shortcode( PostID, templateName, selectedProducts );
			if ( createdShortcode ) {
				tb_remove();
			}			
		}
		return false;						
	});				 
	
	// Select / DeSelect All Products
	$('body').on("click", ".caaps_select_deselect_thickbox_allproducts", function() {
		if ( $(this).is(":checked") ) {
			var checkStatus = true;
		}
		else {
			var checkStatus = false;
		}
		$('.caaps-add-amazonproducts-chkbox').each( function() {			
			$(this).prop("checked", checkStatus );
		});
	});	 
	 
	 function caaps_create_shortcode( PostID, templateName, selectedProducts ) {
			var shortCode = '[caaps postid = "'+ PostID +'" template = "'+ templateName +'" product_perpage = "6" asins = "'+ selectedProducts.toString() +'"]';
			$('.caaps-display-message-trow').html(caaps_addproducts_script_obj.added_products_msg);		 
			send_to_editor( shortCode );
			return true;
	 }
	 
	/**
	 * Resizing thickbox on change in window dimensions
	 * Setting a max width and height of 1280x800 px for readability and to lessen distortion
	 */
	function resize_thickbox(){ 
	  TB_WIDTH = Math.min(1280,0.8 * $(window).width());
	  TB_HEIGHT = Math.min(800,0.9 * $(window).height());
	  $(document).find('#TB_ajaxContent').width(TB_WIDTH-35).height(TB_HEIGHT-90);
	  $(document).find('#TB_window').width(TB_WIDTH).height(TB_HEIGHT);
	  $(document).find('#TB_window').css({marginLeft: '-' + TB_WIDTH / 2 + 'px',top: TB_HEIGHT/12});
	  $(document).find('#TB_window').removeClass();
	}
	
	// Resize thickbox on window resize
	$(window).on('resize',resize_thickbox);
	

	 
	
}); // End jQuery(document).ready(function($)
