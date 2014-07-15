<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );

class modCitruscartProductsHelper extends JObject
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
		Citruscart::load('CitruscartHelperProduct', 'helpers.product');

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_citruscart/models' );

		// get the model
		$model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );

		// setting the model's state tells it what items to return
		$model->setState('filter_published', '1');
		$date = JFactory::getDate();
		$model->setState('filter_published_date', $date->toSql() );
		$model->setState('filter_enabled', '1');

		// Set category state
		if ($this->params->get('category', '1') != '1')
		$model->setState('filter_category', $this->params->get('category', '1'));

		// Set manufacturer state
		if ($this->params->get('manufacturer', '') != '')
		$model->setState('filter_manufacturer', $this->params->get('manufacturer', ''));

		// Set id set state
		if ($this->params->get('id_set', '') != '')
		{
			$params_id_set = $this->params->get('id_set');
			$id_array = explode(',', $params_id_set);
			$id_set = "'".implode("', '", $id_array)."'";
			$model->setState('filter_id_set', $id_set);
		}

		// set the states based on the parameters
		$model->setState('limit', $this->params->get( 'max_number', '10' ));
		if($this->params->get( 'price_from', '-1' ) != '-1')
		$model->setState('filter_price_from', $this->params->get( 'price_from', '-1' ));
		if($this->params->get( 'price_to', '-1' ) != '-1')
		$model->setState('filter_price_to', $this->params->get( 'price_to', '-1' ));
		$order = $this->params->get('order');
		$direction = $this->params->get('direction', 'ASC');
		switch ($order)
		{
			case "2":
			case "name":
				$model->setState('order', 'tbl.product_name');
				break;
			case "1":
			case "created":
				$model->setState('order', 'tbl.created_date');
				break;
			case "0":
			case "ordering":
			default:
				$model->setState('order', 'tbl.ordering');
				break;
		}

		if ($this->params->get('random', '0') == '1')
				$model->setState('order', 'RAND()');

		$model->setState('direction', $direction);

		$config = Citruscart::getInstance();
		$show_tax = $config->get('display_prices_with_tax');

		$default_user_group = Citruscart::getInstance()->get('default_user_group');
		$user_groups_array = $this->getUserGroups();

		$overide_price = false;
		if(count($user_groups_array) > 1 && $user_groups_array[0] != $default_user_group)
		{
			$overide_price = true;

		}
		// using the set filters, get a list of products
		if ($products = $model->getList(true, false ))
		{
			if( $show_tax )
			{
				Citruscart::load('CitruscartHelperUser', 'helpers.user');
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
				if($overide_price)
				{
					$filter_group = CitruscartHelperUser::getUserGroup(JFactory::getUser()->id, $product->product_id);
					$price = CitruscartHelperProduct::getPrice( $product->product_id, '1', $filter_group );
					$product->price =	$price->product_price;
				}

				$product->taxtotal = 0;
				$product->tax = 0;
				if ($show_tax )
				{
					$taxtotal = CitruscartHelperProduct::getTaxTotal($product->product_id, $geozones);
					$product->taxtotal = $taxtotal;
					$product->tax = $taxtotal->tax_total;
				}

				$product->filter_category = '';
				$categories = CitruscartHelperProduct::getCategories( $product->product_id );
				if (!empty($categories))
				{
					$product->link .= "&filter_category=".$categories[0];
					$product->filter_category = $categories[0];
				}
				$itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->category( $product->filter_category, true );
				if( empty( $itemid ) )
				{
					$product->itemid = $this->params->get( 'itemid' );
				}
				else
				{
					$product->itemid = $itemid;
				}
			}
		}

		return $products;
	}

	/**
	 * Method to get if user has multiple user group
	 * @return array
	 */
	private function getUserGroups()
	{
		$user = JFactory::getUser();
		$database = JFactory::getDBO();
		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$query = new CitruscartQuery();
		$query->select( 'tbl.group_id' );
		$query->from('#__citruscart_usergroupxref AS tbl');
		$query->join('INNER', '#__citruscart_groups AS g ON g.group_id = tbl.group_id');
		$query->where("tbl.user_id = ".(int) $user->id);
		$query->order('g.ordering ASC');

		$database->setQuery( (string) $query );
		return $database->loadColumn();
	}
}
