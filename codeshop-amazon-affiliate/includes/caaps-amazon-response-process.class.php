<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Caaps_Amazon_Response_Process {
		
	public static $total_resultpages    = 0;
	
	public static function process_response( $response = null ) {
	//echo '<pre>';	
	//print_r($response);	
	//echo '</pre>';
	  if ( isset($response) && $response['Items']['Request']['IsValid'] === 'True' ) {				  
		  
		  // Get total result pages number
		  self::$total_resultpages = isset( $response['Items']['TotalPages'] )? $response['Items']['TotalPages'] : 0;
		  
		  // Process response
		  if ( isset( $response['Items']['Item'][0]) ) {
			  return self::process_multidata_response( $response['Items']['Item'] );
		  }
		  else if ( isset( $response['Items']['Item']) ) {
			  return self::process_singledata_response( $response['Items']['Item'] );
		  }
		  else if ( isset( $response['Items']['Request']['Errors']['Error']['Code'] ) ) {
			  return __( 'AMAZON ERROR CODE: ', 'codeshop-amazon-affiliate' ) . $response['Items']['Request']['Errors']['Error']['Code'];
		  }		  
	  }
	  // When $response['Items']['Request']['IsValid'] == False
	  else {
			  wp_die( '<div class="notice notice-warning"><h4>' . $response['Items']['Request']['Errors']['Error']['Message'] . '</h4></div>');
	  }
	}
	
	public static function process_multidata_response( $items = null ) {
		$item_index = 0;
		$products  = array();
		foreach ( $items as $item) {
			$products[ $item_index ]['ASIN'] = $item['ASIN'];
			$products[ $item_index ]['DetailPageURL'] = $item['DetailPageURL'];
			$products[ $item_index ]['SmallImage'] = isset( $item['SmallImage']['URL'] )? $item['SmallImage']['URL'] : '';
			$products[ $item_index ]['MediumImage'] = isset( $item['MediumImage']['URL'] )? $item['MediumImage']['URL'] : '';
			$products[ $item_index ]['LargeImage'] = isset( $item['LargeImage']['URL'] )? $item['LargeImage']['URL'] : '';			
					
			$products[ $item_index ]['Brand'] = isset( $item['ItemAttributes']['Brand'] )? $item['ItemAttributes']['Brand'] : '';
			$products[ $item_index ]['EAN'] = isset( $item['ItemAttributes']['EAN'] )? $item['ItemAttributes']['EAN'] : '';
			$products[ $item_index ]['EAN'] = isset( $item['ItemAttributes']['EAN'] )? $item['ItemAttributes']['EAN'] : '';
			$products[ $item_index ]['ListPriceCurrencyCode'] = isset( $item['ItemAttributes']['ListPrice']['CurrencyCode'] )? $item['ItemAttributes']['ListPrice']['CurrencyCode'] : '';
			$products[ $item_index ]['ListPriceFormattedPrice'] = isset( $item['ItemAttributes']['ListPrice']['FormattedPrice'] )? $item['ItemAttributes']['ListPrice']['FormattedPrice'] : '';
			
			$products[ $item_index ]['Manufacturer'] = isset( $item['ItemAttributes']['Manufacturer'] )? $item['ItemAttributes']['Manufacturer'] : '';
			$products[ $item_index ]['Publisher'] = isset( $item['ItemAttributes']['Publisher'] )? $item['ItemAttributes']['Publisher'] : '';
			$products[ $item_index ]['ProductGroup'] = isset( $item['ItemAttributes']['ProductGroup'] )? $item['ItemAttributes']['ProductGroup'] : '';								
			$products[ $item_index ]['Title'] = isset( $item['ItemAttributes']['Title'] )? $item['ItemAttributes']['Title'] : '';												
			$products[ $item_index ]['LowestNewPriceCurrencyCode'] = isset( $item['OfferSummary']['LowestNewPrice']['CurrencyCode'] )? $item['OfferSummary']['LowestNewPrice']['CurrencyCode'] : '';
			$products[ $item_index ]['LowestNewPriceFormattedPrice'] = isset( $item['OfferSummary']['LowestNewPrice']['FormattedPrice'] )? $item['OfferSummary']['LowestNewPrice']['FormattedPrice'] : '';
			$products[ $item_index ]['TotalNew'] = isset( $item['OfferSummary']['TotalNew'] )? $item['OfferSummary']['TotalNew'] : '';
			$products[ $item_index ]['TotalUsed'] = isset( $item['OfferSummary']['TotalUsed'] )? $item['OfferSummary']['TotalUsed'] : '';
			$products[ $item_index ]['TotalCollectible'] = isset( $item['OfferSummary']['TotalCollectible'] )? $item['OfferSummary']['TotalCollectible'] : '';
			$products[ $item_index ]['TotalRefurbished'] = isset( $item['OfferSummary']['TotalRefurbished'] )? $item['OfferSummary']['TotalRefurbished'] : '';
			
			
			$products[ $item_index ]['TotalOffers'] = isset( $item['Offers']['TotalOffers'] )? $item['Offers']['TotalOffers'] : '';
			$products[ $item_index ]['Merchant'] = isset( $item['Offers']['Offer']['Merchant']['Name'] )? $item['Offers']['Offer']['Merchant']['Name'] : '';
			$products[ $item_index ]['Condition'] = isset( $item['Offers']['Offer']['OfferAttributes']['Condition'] )? $item['Offers']['Offer']['OfferAttributes']['Condition'] : '';
			$products[ $item_index ]['OfferListingId'] = isset( $item['Offers']['Offer']['OfferListing']['OfferListingId'] )? $item['Offers']['Offer']['OfferListing']['OfferListingId'] : '';
			$products[ $item_index ]['PriceCurrencyCode'] = isset( $item['Offers']['Offer']['OfferListing']['Price']['CurrencyCode'] )? $item['Offers']['Offer']['OfferListing']['Price']['CurrencyCode'] : '';
			$products[ $item_index ]['PriceFormattedPrice'] = isset( $item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'] )? $item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'] : '';
			$products[ $item_index ]['Availability'] = isset( $item['Offers']['Offer']['OfferListing']['Availability'] )? $item['Offers']['Offer']['OfferListing']['Availability'] : '';
			$products[ $item_index ]['CachedTime'] = date("Y-m-d H:i:s", time() );
						
			$item_index++;
		}
		return $products;					
	}

	public static function process_singledata_response( $item = null ) {
		$item_index = 0;
		$products  = array();
		$products[ $item_index ]['ASIN'] = $item['ASIN'];
		$products[ $item_index ]['DetailPageURL'] = $item['DetailPageURL'];
		$products[ $item_index ]['SmallImage'] = isset( $item['SmallImage']['URL'] )? $item['SmallImage']['URL'] : '';
		$products[ $item_index ]['MediumImage'] = isset( $item['MediumImage']['URL'] )? $item['MediumImage']['URL'] : '';
		$products[ $item_index ]['LargeImage'] = isset( $item['LargeImage']['URL'] )? $item['LargeImage']['URL'] : '';
				
		$products[ $item_index ]['Brand'] = isset( $item['ItemAttributes']['Brand'] )? $item['ItemAttributes']['Brand'] : '';
		$products[ $item_index ]['EAN'] = isset( $item['ItemAttributes']['EAN'] )? $item['ItemAttributes']['EAN'] : '';
		$products[ $item_index ]['EAN'] = isset( $item['ItemAttributes']['EAN'] )? $item['ItemAttributes']['EAN'] : '';
		$products[ $item_index ]['ListPriceCurrencyCode'] = isset( $item['ItemAttributes']['ListPrice']['CurrencyCode'] )? $item['ItemAttributes']['ListPrice']['CurrencyCode'] : '';
		$products[ $item_index ]['ListPriceFormattedPrice'] = isset( $item['ItemAttributes']['ListPrice']['FormattedPrice'] )? $item['ItemAttributes']['ListPrice']['FormattedPrice'] : '';
		
		$products[ $item_index ]['Manufacturer'] = isset( $item['ItemAttributes']['Manufacturer'] )? $item['ItemAttributes']['Manufacturer'] : '';
		$products[ $item_index ]['Publisher'] = isset( $item['ItemAttributes']['Publisher'] )? $item['ItemAttributes']['Publisher'] : '';
		$products[ $item_index ]['ProductGroup'] = isset( $item['ItemAttributes']['ProductGroup'] )? $item['ItemAttributes']['ProductGroup'] : '';				
		$products[ $item_index ]['Title'] = isset( $item['ItemAttributes']['Title'] )? $item['ItemAttributes']['Title'] : '';						
		$products[ $item_index ]['LowestNewPriceCurrencyCode'] = isset( $item['OfferSummary']['LowestNewPrice']['CurrencyCode'] )? $item['OfferSummary']['LowestNewPrice']['CurrencyCode'] : '';
		$products[ $item_index ]['LowestNewPriceFormattedPrice'] = isset( $item['OfferSummary']['LowestNewPrice']['FormattedPrice'] )? $item['OfferSummary']['LowestNewPrice']['FormattedPrice'] : '';
		$products[ $item_index ]['TotalNew'] = isset( $item['OfferSummary']['TotalNew'] )? $item['OfferSummary']['TotalNew'] : '';
		$products[ $item_index ]['TotalUsed'] = isset( $item['OfferSummary']['TotalUsed'] )? $item['OfferSummary']['TotalUsed'] : '';
		$products[ $item_index ]['TotalCollectible'] = isset( $item['OfferSummary']['TotalCollectible'] )? $item['OfferSummary']['TotalCollectible'] : '';
		$products[ $item_index ]['TotalRefurbished'] = isset( $item['OfferSummary']['TotalRefurbished'] )? $item['OfferSummary']['TotalRefurbished'] : '';
		
		
		$products[ $item_index ]['TotalOffers'] = isset( $item['Offers']['TotalOffers'] )? $item['Offers']['TotalOffers'] : '';
		$products[ $item_index ]['Merchant'] = isset( $item['Offers']['Offer']['Merchant']['Name'] )? $item['Offers']['Offer']['Merchant']['Name'] : '';
		$products[ $item_index ]['Condition'] = isset( $item['Offers']['Offer']['OfferAttributes']['Condition'] )? $item['Offers']['Offer']['OfferAttributes']['Condition'] : '';
		$products[ $item_index ]['OfferListingId'] = isset( $item['Offers']['Offer']['OfferListing']['OfferListingId'] )? $item['Offers']['Offer']['OfferListing']['OfferListingId'] : '';
		$products[ $item_index ]['PriceCurrencyCode'] = isset( $item['Offers']['Offer']['OfferListing']['Price']['CurrencyCode'] )? $item['Offers']['Offer']['OfferListing']['Price']['CurrencyCode'] : '';
		$products[ $item_index ]['PriceFormattedPrice'] = isset( $item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'] )? $item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'] : '';
		$products[ $item_index ]['Availability'] = isset( $item['Offers']['Offer']['OfferListing']['Availability'] )? $item['Offers']['Offer']['OfferListing']['Availability'] : '';
		$products[ $item_index ]['CachedTime'] = date("Y-m-d H:i:s", time() );	
		
		return $products;			
	}
		
} // End Class
?>