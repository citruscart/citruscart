<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

class CitruscartHelperTax extends CitruscartHelperBase
{
	/**
	 * Calculate taxes on list of products based on provided geozones
	 *
	 * @param $products					Array of products
	 * @param $source					Source of tax calculation (final_price '1', product_price '2', orderitem_price '3', orderitem_price + orderitem_attribute_price '4')
	 * @param $geozones					Array with IDs of geozones
	 *
	 * @return Associative array with indexes product_id of products with arrays with list of their tax rates (names and rates)
	 */
	static public function calculateGeozonesTax( $items, $source, $geozones )
	{
		$result = new stdClass();
		$result->tax_total = 0.00;
		$result->tax_rate_rates = array();
		$result->tax_class_rates = array();
		$result->product_taxes = array();

		if ( !is_array( $items ) )
			return $result;

		$db = JFactory::getDBO( );
		foreach ( $items as $key => $item )
		{
			$orderitem_tax = 0;

			// for each geozone for billing address calculate and update the item's tax value
			foreach ( $geozones  as $geozone )
			{
				switch( $source )
				{
					case 1: // final_price
						$product_price = $item->orderitem_final_price;
						break;
					case 2: // product_price
						$product_price = $item->product_price;
						break;
					case 3: // orderitem_price
						$product_price = $item->orderitem_price;
						break;
					case 4: // orderitem_price + orderitem_attributes_price
						$product_price = $item->orderitem_price + floatval($item->orderitem_attributes_price);
						break;
				}
				$taxrate = CitruscartHelperTax::calculateTaxSingleProductGeozone( $item->product_id, $product_price, $geozone );
				$orderitem_tax += $taxrate->amount;

				for( $i = 0, $c = count( $taxrate->rates ); $i < $c; $i++ ) // count in all tax rates for this product
				{
					if( !isset( $result->tax_rate_rates[ $taxrate->rates[$i]->tax_rate_id] ) )
						$result->tax_rate_rates[ $taxrate->rates[$i]->tax_rate_id ] = $taxrate->rates[$i];
					else
						$result->tax_rate_rates[ $taxrate->rates[$i]->tax_rate_id ]->applied_tax += $taxrate->rates[$i]->applied_tax;

					if( !isset( $result->tax_class_rates[ $taxrate->rates[$i]->tax_class_id] ) )
					{
						$q = new CitruscartQuery( );
						$q->select( '*' );
						$q->from( '#__citruscart_taxclasses' );
						$q->where( 'tax_class_id = '.( int )$taxrate->rates[$i]->tax_class_id );
						$db->setQuery( $q );
						
						$result->tax_class_rates[ $taxrate->rates[$i]->tax_class_id ] = $db->loadObject();
						$result->tax_class_rates[ $taxrate->rates[$i]->tax_class_id ]->applied_tax = $taxrate->rates[$i]->applied_tax;
					}
					else
						$result->tax_class_rates[ $taxrate->rates[$i]->tax_class_id ]->applied_tax += $taxrate->rates[$i]->applied_tax;
				}
			}
			
			$result->product_taxes[ $item->product_id ] = $orderitem_tax;
			$result->tax_total += $orderitem_tax;
		}
		return $result;
		
	}
	
	/**
	 * Calculate taxes on list of products
	 *
	 * @param $products						Array of products
	 * @param $source						Source of tax calculation (final_price '1', product_price '2', orderitem_price '3')
	 * @param $billing_address		Actual customer's billing address
	 * @param $shipping_address		Actual customer's shipping address
	 * @param $tax_type						for the future use
	 *
	 * @return Associative array with indexes product_id of products with arrays with list of their tax rates (names and rates)
	 */
	static public function calculateTax( $products, $source = 1, $billing_address = null, $shipping_address = null, $tax_type = null )
	{
		$result = new stdClass();
		$result->tax_total = 0.00;
		$result->tax_rate_rates = array();
		$result->tax_class_rates = array();
		$result->product_taxes = array();

		if ( !is_array( $products ) ) {
			return $result;
		}

		Citruscart::load( 'CitruscartHelperShipping', 'helpers.shipping' );
		Citruscart::load( 'CitruscartQuery', 'library.query' );
		Citruscart::load( 'CitruscartTools', 'library.tools' );
		if ( $billing_address ) {
			$billing_zones = CitruscartHelperShipping::getGeoZones( $billing_address->zone_id, '1', $billing_address->postal_code );
		} else { 
			$billing_zones = array();
		}
		
		if ( !empty( $billing_zones ) )
		{
			foreach( $billing_zones as $key => $value ) {
				$billing_zones[$key] = $value->geozone_id;
			}
		}

		//load the default geozones when user is logged out and the config is to show tax
		if (empty( $billing_zones ) )
		{
			$geozones = CitruscartHelperUser::getGeoZones( JFactory::getUser()->id );
			if( empty( $geozones ) )
			{
			    // use the default
			    $billing_zones = array( Citruscart::getInstance()->get('default_tax_geozone') );
			}
			else
			{
			    foreach( $geozones as $key => $value ) {
			        $billing_zones[$key] = $value->geozone_id;
			    }
			}
		}

		return CitruscartHelperTax::calculateGeozonesTax( $products , $source, $billing_zones );
	}

	/*
	 * Calculate taxes on for a single product and a single geozone
	 *
	 * @param $product_id				ID of a product
	 * @param $product_price		Product price
	 * @param $geozone_id				Geozone ID
	 * @param $tax_class_id			Tax Class ID
	 * @param $tax_type					for the future use
	 * @param $update_rates			Force method to update tax rates for this geozone
	 *
	 * @return An object with calculated taxes on a product
	 */
	static public function calculateTaxSingleProductGeozone( $product_id, $product_price, $geozone_id = null, $tax_class_id = null, $tax_type = null, $update_rates = false )
	{
		static $taxes = null;
		static $taxes_rates = null;
		
		if( $taxes === null )
			$taxes = array();
		if( $taxes_rates === null )
			$taxes_rates = array();

		$result = new stdClass();
		$result->rates = array();
		$result->amount = 0;
		
		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$db = JFactory::getDBO( );
		if( $tax_class_id === null )
		{
			$q = new CitruscartQuery( );
			$q->select( 'tax_class_id' );
			$q->from( '#__citruscart_products' );
			$q->where( 'product_id = '.( int )$product_id );
			$db->setQuery( $q );
			$tax_class_id = $db->loadResult();
		}
		
		if( isset( $tax_rates[$geozone_id][$tax_class_id] ) && !$update_rates )
			$data = $tax_rates[$geozone_id][$tax_class_id];
		else 
		{
			$q = new CitruscartQuery();
			$q->select( 'tax_class_id, tax_rate_id, tax_rate, tax_rate_description, level ' );
			$q->from( '#__citruscart_taxrates' );
			if( $geozone_id !== null )
				$q->where( "geozone_id = ".( int )$geozone_id );
			$q->where( 'tax_class_id = '. ( int )$tax_class_id );
			$q->order( 'level' );
			$db->setQuery( ( string ) $q );
			$data = $db->loadObjectList( );
			$tax_rates[$geozone_id][$tax_class_id] = $data;
		}
		
		$taxes_list = array();
		if ( $c = count( $data ) )
		{
			$prev_level = 0;
			$subtotal = $product_price;
			$tax_amount = 0;
			for( $i = 0; $i < $c; $i++ )
			{
				$tax = $data[$i];
				if( $tax->level != $prev_level )
				{
					$subtotal += $tax_amount;
					$result->amount += $tax_amount;
					$tax_amount = 0;
					$prev_level = $tax->level;
				}
				$tax->applied_tax = ( $tax->tax_rate / 100) * $subtotal;
				$tax_amount += $tax->applied_tax;
				$taxes_list []= $tax;
			}
			$result->amount += $tax_amount;
			$result->rates = $taxes_list;
		}
		return $result;
	}
}
