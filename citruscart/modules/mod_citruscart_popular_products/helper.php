<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );

class modCitruscartPopularProductsHelper extends JObject
{
	/**
	 * Sets the modules params as a property of the object
	 * @param unknown_type $params
	 * @return unknown_type
	 */
	function __construct( $params )
	{
		$this->params = $params;
	}

	/**
	 * Sample use of the products model for getting products with certain properties
	 * See admin/models/products.php for all the filters currently built into the model
	 *
	 * @param $parameters
	 * @return unknown_type
	 */
	function getProducts()
	{
		// Check the registry to see if our Citruscart class has been overridden
		if ( !class_exists('Citruscart') )
		JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

		// load the config class
		Citruscart::load( 'Citruscart', 'defines' );
		Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
		Citruscart::load('CitruscartHelperUser', 'helpers.user');
		$helper = new CitruscartHelperProduct();

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_citruscart/models' );

		// get the model
		$model = JModelLegacy::getInstance( 'OrderItems', 'CitruscartModel' );
		$model->setState( 'limit', $this->params->get( 'max_number', '5') );

		$query = $model->getQuery();

		// group results by product ID
		$query->group('tbl.product_id');

		// select the total number of sales for each product
		$field = array();
		$field[] = " SUM(tbl.orderitem_quantity) AS total_sales ";
		$field[] = " p.product_description_short AS product_description_short ";
		$query->select( $field );

		// order results by the total sales
		$query->order('total_sales DESC');

		$model->setQuery( $query );

		$show_tax = Citruscart::getInstance()->get('display_prices_with_tax');

		// using the set filters, get a list of products
		if ($products = $model->getList( false, false ))
		{
			if ($show_tax)
			{
				$geozones = CitruscartHelperUser::getGeoZones( JFactory::getUser()->id );
				if (empty($geozones))
				{
					// use the default
					$table = JTable::getInstance('Geozones', 'CitruscartTable');
					$table->load(array('geozone_id'=>Citruscart::getInstance()->get('default_tax_geozone')));
					$geozones = array( $table );
				}
			}
			foreach ($products as $product)
			{
				$product->link = 'index.php?option=com_citruscart&view=products&task=view&id='.$product->product_id;
				$filter_group = CitruscartHelperUser::getUserGroup(JFactory::getUser()->id, $product->product_id);
				$price = $helper->getPrice( $product->product_id, '1', $filter_group );
				$product->price = (isset($price->product_price)) ? $price->product_price :0 ;
				//product total
				$product->taxtotal = 0;
				$product->tax = 0;
				if ($show_tax)
				{
					$taxtotal = CitruscartHelperProduct::getTaxTotal($product->product_id, $geozones);
					$product->taxtotal = $taxtotal;
					$product->tax = $taxtotal->tax_total;
				}

				$product->filter_category = '';
				$categories = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getCategories( $product->product_id );
				if (!empty($categories))
				{
					$product->link .= "&filter_category=".$categories[0];
					$product->filter_category = $categories[0];
				}

				$itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->category( $product->filter_category, true );
				if (empty($itemid))
				{
					$itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->findItemid( array( 'view'=>'products' ) );
				}
				$product->itemid = $itemid;
			}
		}


		return $products;
	}
}

