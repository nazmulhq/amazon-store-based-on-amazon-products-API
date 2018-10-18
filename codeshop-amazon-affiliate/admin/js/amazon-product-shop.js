jQuery(document).ready(function($) {		
	// Get Keyword Input
	$('.caaps_searchby_kword_btn').click(function() {		
		var searchCountry  = $('#caaps_locale_field').val();
		var searchKword    = $('.caaps_search_kword').val();
		var searchCategory = $('#caaps_search_category').val();
		var sortBy         = $('#caaps_search_sorting').val();		
		// Initial default search result page number
		var resultPage     = 1;		
		$('.caaps-display-results-metabox').html('<p style="text-align:center; font-size:18px; font-weight:bold;">'+caaps_metabox_script_obj.product_searching_msg+'<br/>'+caaps_metabox_script_obj.product_searching_loadimage+'</p>');
		$.post( caaps_metabox_script_obj.adminajax_url, 
		        {	
				   action: "caaps_searchby_kword_display",
				   search_country: searchCountry, 
				   search_kword: searchKword,				   
				   search_category: searchCategory,
				   sort_by: sortBy,
				   result_page: resultPage,
				   security: caaps_metabox_script_obj.nonce	
				}, function(data) {
					$('.caaps-display-results-metabox').html(data);
		           }
	    );		
		return false;		
	});
	
	// When click on results page numbers to search
	$('body').on("click", ".caaps_search_page_number", function() {		
		// Search result page number
		var resultPage     = $(this).text();				
		console.log( resultPage );
		var searchCountry  = $('#caaps_locale_field').val();
		var searchKword    = $('.caaps_search_kword').val();
		var searchCategory = $('#caaps_search_category').val();
		var sortBy         = $('#caaps_search_sorting').val();		
		$('.caaps-display-results-metabox').html('<p style="text-align:center; font-size:18px; font-weight:bold;">'+caaps_metabox_script_obj.product_searching_msg+'<br/>'+caaps_metabox_script_obj.product_searching_loadimage+'</p>');
		$.post( caaps_metabox_script_obj.adminajax_url, 
		        {	
				   action: "caaps_searchby_kword_display",
				   search_country: searchCountry, 
				   search_kword: searchKword,	
				   search_category: searchCategory,
				   sort_by: sortBy,				   			   
				   result_page: resultPage,
				   security: caaps_metabox_script_obj.nonce	
				}, function(data) {
					$('.caaps-display-results-metabox').html(data);
		           }
	    );				
	});
	
	// Get ASINs Input
	$('.caaps_searchby_asin_btn').click(function() {
		var searchCountry = $('#caaps_locale_field').val();
		var searchASIN = $('.caaps_search_asin').val();
		$('.caaps-display-results-metabox').html('<p style="text-align:center; font-size:18px; font-weight:bold;">'+caaps_metabox_script_obj.product_searching_msg+'<br/>'+caaps_metabox_script_obj.product_searching_loadimage+'</p>');
		$.post( caaps_metabox_script_obj.adminajax_url, 
		        {	
				   action: "caaps_searchby_asin_display",
				   search_country: searchCountry, 
				   search_asin: searchASIN,
				   security: caaps_metabox_script_obj.nonce		
				}, function(data) {
					$('.caaps-display-results-metabox').html(data);
		           }
	    );
		return false;
	});
	
	// Select / DeSelect All Products - Search Results
	$('#caaps_displayresults_metabox').on("click", ".caaps_select_deselect_allproducts", function() {
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
	
	// Select / DeSelect All Products - Added products
	$('#caaps_remove_select_deselect_allproducts').click( function() {
		if ( $(this).is(":checked") ) {
			var checkStatus = true;
		}
		else {
			var checkStatus = false;
		}
		$('.caaps-remove-amazonproducts-chkbox').each( function() {			
			$(this).prop("checked", checkStatus );
		});
	});
	
	
	// Add selected products button	click - Search Results Meta box
	$('#caaps_displayresults_metabox').on("click", ".caaps_addselected_products_btn", function() {		
				
		$('.caaps-display-message-trow').html(caaps_metabox_script_obj.adding_products_msg);
		var selectedProducts = [];		
		$('.caaps-add-amazonproducts-chkbox').each(function() {					    
			if ( $(this).is(":checked") ) {
				selectedProducts.push($(this).val());
			}
		});		
		if ( selectedProducts.length == 0 ) {
			$('.caaps-display-message-trow').html(caaps_metabox_script_obj.no_products_selected_msg);	
		}
		else {
			var PostID = '';
			PostID = $('.caaps-products-toadd-postid').val();		
			$.post( caaps_metabox_script_obj.adminajax_url, 
					{	
					   action: "caaps_add_selected_products",				   
					   selected_products: selectedProducts,
					   post_id: PostID,
					   security: caaps_metabox_script_obj.nonce	
					}, function(data) {
						if ( data )					    
						$('.caaps-display-message-trow').html(caaps_metabox_script_obj.added_products_msg);						
					   }
			);		
		}
		return false;						
	});
	
	// Remove selected products button click - Added Products Metabox
	$('#caaps_removeselected_products_btn').click(function() {						
		$('.caaps-remove-products-display-message-trow').html(caaps_metabox_script_obj.removing_products_msg);
		var selectedProducts = [];		
		$('.caaps-remove-amazonproducts-chkbox').each(function() {					    
			if ( $(this).is(":checked") ) {
				selectedProducts.push($(this).val());
			}
		});		
		if ( selectedProducts.length == 0 ) {
			$('.caaps-remove-products-display-message-trow').html(caaps_metabox_script_obj.no_products_removeselected_msg);	
		}
		else {
			var PostID = '';
			PostID = $('.caaps-products-toadd-postid').val();		
			$.post( caaps_metabox_script_obj.adminajax_url, 
					{	
					   action: "caaps_remove_selected_products",				   
					   selected_products: selectedProducts,
					   post_id: PostID,
					   security: caaps_metabox_script_obj.nonce	
					}, function(data) {
						if ( data )
						$('.caaps-remove-products-display-message-trow').html(caaps_metabox_script_obj.removed_products_msg);
						$('.caaps-remove-products-display-message-trow').append('<a href="Javascript:document.location.reload(true)"> Refresh Page</a>');
					   }
			);		
		}
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
			var selectOpts = '<option value="">'+caaps_metabox_script_obj.sort_by+'</option>';
			for ( var i = 0; i < sortParams.length; i++ ) {
				selectOpts += '<option value="'+sortParams[i]+'">'+sortParams[i]+'</option>';
			}
			//console.log( selectOpts );
			$('#caaps_search_sorting').html(selectOpts);
		}
	});
	
	// Test Settings Click
	$('.caaps_testapi_settings').click(function() {
		$('.caaps_testapi_message').html('<h4>Please wait...Testing API Settings.</h4>');
		$.ajax({
		  type:"POST",
		  cache: false,
		  url: caaps_metabox_script_obj.adminajax_url,
		  data : {
                action : 'caaps_test_api_settings',
                security : caaps_metabox_script_obj.nonce
                },		  
		  success: function(data) { 
		  	// console.log(data); 
			$('.caaps_testapi_message').html('<div class="notice notice-success">Settings test success!</div>');
			},
		  error: function( xhr, status, error ) { 
		  	// console.log(xhr); 
			// console.log(status); 
			// console.log(error); 
			$('.caaps_testapi_message').html('<div class="notice notice-error">Settings test failed!</div>');
			}
		})		  
		  
		  
	});
					
}); // End jQuery(document).ready(function($)