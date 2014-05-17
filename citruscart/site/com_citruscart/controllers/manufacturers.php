<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class CitruscartControllerManufacturers extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'manufacturers');
	}

	/**
	 * Sets the model's state
	 *
	 * @return array()
	 */
	function _setModelState( $model_name='' )
	{
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		if (empty($model_name)) {
			$model_name = $this->get('suffix');
		}
		$model = $this->getModel( $model_name );
		$ns = $this->getNamespace();

		$date = JFactory::getDate();

		$state['order'] = 'tbl.ordering';
		$state['direction'] = 'ASC';
		$state['filter_enabled']  = 1;
		$state['filter_published'] = 1;
		$state['filter_published_date'] = $date->toSql();
		$state['filter_manufacturer'] = $app->input->getInt('filter_manufacturer');

		//NOTE: check if filter_price_from and filter_price_to are empty since product will not show even if there is a product if its empty
		//check the filter price from
		$priceFrm = $app->input->getInt('filter_price_from');
		if( !empty( $priceFrm ) )
		{
			$state['filter_price_from'] = $priceFrm;
		}

		//check the filter price from
		$priceTo = $app->input->getInt('filter_price_to');
		if( !empty( $priceTo ) )
		{
			$state['filter_price_to'] = $priceTo;
		}

		if (!Citruscart::getInstance()->get('display_out_of_stock'))
		{
			$state['filter_quantity_from'] = '1';
		}

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}

		return $state;
	}

	/**
	 * Displays products by manufacturer
	 *
	 * (non-PHPdoc)
	 * @see Citruscart/admin/CitruscartController#display($cachable)
	 */
	function products()
	{
		$input =JFactory::getApplication()->input;
		$input->set( 'view', $this->get('suffix') );
		$input->set( 'search', false );
		$view   = $this->getView( $this->get('suffix'), JFactory::getDocument()->getType() );
		$model  = $this->getModel( 'products' );
		$state = $this->_setModelState( 'products' );

		$filter_manufacturer = $model->getState( 'filter_manufacturer' );

		// get the manufacturer we're looking at
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$cmodel = JModelLegacy::getInstance( 'Manufacturers', 'CitruscartModel' );
		$cat = $cmodel->getTable();
		$cat->load( $filter_manufacturer );

		// set the title based on the selected manufacturer
		$title = (empty($cat->manufacturer_name)) ? JText::_('COM_CITRUSCART_ALL_MANUFACTURERS') : JText::_($cat->manufacturer_name);

		// breadcrumb support
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$manufacturer_itemid = $input->getInt('Itemid', Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->manufacturer( $filter_manufacturer, true ) );

		// get the products to be displayed in this category
		if ($items = $model->getList())
		{
			foreach ($items as $item)
			{
				$itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->product( $item->product_id, null, true );
				$item->itemid = $input->getInt('Itemid', $itemid);

				$item->product_buy = $this->getAddToCart($item->product_id);
			}
		}

		$view->assign( 'title', $title );
		$view->assign( 'cat', $cat );
		$view->assign( 'items', $items );
		$view->set('_doTask', true);
		$view->setModel( $model, true );

		$view->setLayout('products');

		$view->display();
		$this->footer();
		return;
	}

	/**
	 * Displays a single product
	 * (non-PHPdoc)
	 * @see Citruscart/site/CitruscartController#view()
	 */
	function view()
	{
		$input =JFactory::getApplication()->input;
		$input->set( 'view', $this->get('suffix') );
		$model  = $this->getModel( $this->get('suffix') );
		$model->getId();

		Citruscart::load('CitruscartHelperUser', 'helpers.user');
		$user_id = JFactory::getUser()->id;
		$filter_group = CitruscartHelperUser::getUserGroup($user_id);
		$model->setState('filter_group', $filter_group);
		$row = $model->getItem( false ); // use the state

		$filter_category = $model->getState('filter_category', $input->get('filter_category'));
		if (empty($filter_category))
		{
			$categories = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getCategories( $row->product_id );
			if (!empty($categories))
			{
				$filter_category = $categories[0];
			}
		}

		if (empty($row->product_enabled))
		{
			$redirect = "index.php?option=com_citruscart&view=products&task=display&filter_category=".$filter_category;
			$redirect = JRoute::_( $redirect, false );
			$this->message = JText::_('COM_CITRUSCART_CANNOT_VIEW_DISABLED_PRODUCT');
			$this->messagetype = 'notice';
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}

		Citruscart::load( 'CitruscartArticle', 'library.article' );
		$product_description = CitruscartArticle::fromString( $row->product_description );


		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$cmodel = JModelLegacy::getInstance( 'Categories', 'CitruscartModel' );
		$cat = $cmodel->getTable();
		$cat->load( $filter_category );

		$view   = $this->getView( $this->get('suffix'), JFactory::getDocument()->getType() );
		$view->set('_doTask', true);
		$view->assign( 'row', $row );
		$view->assign( 'cat', $cat );

		// breadcrumb support
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$category_itemid = $input->getInt('Itemid', Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->category( $filter_category, true ) );
		$items = Citruscart::getClass( "CitruscartHelperCategory", 'helpers.category' )->getPathName( $filter_category, 'array' );
		if (!empty($items))
		{
			// add the categories to the pathway
			Citruscart::getClass( "CitruscartHelperPathway", 'helpers.pathway' )->insertCategories( $items, $category_itemid );
		}
		// add the item being viewed to the pathway
		$pathway->addItem( $row->product_name );

		// Check If the inventroy is set then it will go for the inventory product quantities
		if ($row->product_check_inventory)
		{
			$inventoryList = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getProductQuantities( $row->product_id );

			if (!Citruscart::getInstance()->get('display_out_of_stock') && empty($inventoryList))
			{
				// redirect
				$redirect = "index.php?option=com_citruscart&view=products&task=display&filter_category=".$filter_category;
				$redirect = JRoute::_( $redirect, false );
				$this->message = JText::_('COM_CITRUSCART_CANNOT_VIEW_PRODUCT');
				$this->messagetype = 'notice';
				$this->setRedirect( $redirect, $this->message, $this->messagetype );
				return;
			}

			// if there is no entry of product in the productquantities
			if (count($inventoryList) == 0)
			{
				$inventoryList[''] = '0';
			}
			$view->assign( 'inventoryList', $inventoryList );
		}
		$view->assign( 'product_comments', $this->getComments($row->product_id) );
		$view->assign('product_description', $product_description );
		$view->assign( 'files', $this->getFiles( $row->product_id ) );
		$view->assign( 'product_buy', $this->getAddToCart( $row->product_id ) );
		$view->assign( 'product_relations', $this->getRelationshipsHtml( $row->product_id, 'relates' ) );
		$view->assign( 'product_children', $this->getRelationshipsHtml( $row->product_id, 'parent' ) );
		$view->assign( 'product_requirements', $this->getRelationshipsHtml( $row->product_id, 'requires' ) );
		$view->setModel( $model, true );

		// using a helper file, we determine the product's layout
		$layout = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getLayout( $row->product_id, array( 'category_id'=>$cat->category_id ) );
		$view->setLayout($layout);



		ob_start();
		JFactory::getApplication()->triggerEvent( 'onDisplayProductAttributeOptions', array( $row->product_id ) );
		$view->assign( 'onDisplayProductAttributeOptions', ob_get_contents() );
		ob_end_clean();

		ob_start();
		JFactory::getApplication()->triggerEvent( 'onBeforeDisplayProduct', array( $row->product_id ) );
		$view->assign( 'onBeforeDisplayProduct', ob_get_contents() );
		ob_end_clean();

		ob_start();
		JFactory::getApplication()->triggerEvent( 'onAfterDisplayProduct', array( $row->product_id ) );
		$view->assign( 'onAfterDisplayProduct', ob_get_contents() );
		ob_end_clean();

		$view->display();
		$this->footer();
		return;
	}

	/**
	 * Gets a product's add to cart section
	 * formatted for display
	 *
	 * @param int $address_id
	 * @return string html
	 */
	function getAddToCart( $product_id, $values=array() )
	{
		$input =JFactory::getApplication()->input;
		$html = '';

		$view   = $this->getView( 'products', 'html' );
		//$model  = $this->getModel( $this->get('suffix') );
		$model = JModelLegacy::getInstance('Products', 'CitruscartModel');
		$model->setId( $product_id );

		Citruscart::load('CitruscartHelperUser', 'helpers.user');
		$user_id = JFactory::getUser()->id;
		$filter_group = CitruscartHelperUser::getUserGroup($user_id);
		$model->setState('filter_group', $filter_group);

		//$model->_item = '';
		$row = $model->getItem( false );
		if ($row->product_notforsale || Citruscart::getInstance()->get('shop_enabled') == '0')
		{
			return $html;
		}

		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', true);
		$view->setModel( $model, true );
		$view->setLayout( 'product_buy' );
		$view->assign( 'item', $row );
		$view->assign('product_id', $product_id);
		$view->assign('values', $values);
		$filter_category = $model->getState('filter_category', $input->getInt('filter_category', (int) @$values['filter_category'] ));
		$view->assign('filter_category', $filter_category);
		$view->assign('validation', "index.php?option=com_citruscart&view=products&task=validate&format=raw" );

		$config = Citruscart::getInstance();
		$show_tax = $config->get('display_prices_with_tax');
		$view->assign( 'show_tax', $show_tax );
		$view->assign( 'tax', 0 );
		$view->assign( 'taxtotal', '' );
		$view->assign( 'shipping_cost_link', '' );

		$row->tax = '0';
		if ($show_tax)
		{
			// finish CitruscartHelperUser::getGeoZone -- that's why this isn't working
			Citruscart::load('CitruscartHelperUser', 'helpers.user');
			$geozones = CitruscartHelperUser::getGeoZones( JFactory::getUser()->id );
			if (empty($geozones))
			{
				// use the default
				$table = JTable::getInstance('Geozones', 'CitruscartTable');
				$table->load(array('geozone_id'=>Citruscart::getInstance()->get('default_tax_geozone')));
				$geozones = array( $table );
			}

			$taxtotal = CitruscartHelperProduct::getTaxTotal($product_id, $geozones);
			$tax = $taxtotal->tax_total;
			$row->taxtotal = $taxtotal;
			$row->tax = $tax;
			$view->assign( 'taxtotal', $taxtotal );
			$view->assign( 'tax', $tax );
		}

		// TODO What about this??
		$show_shipping = $config->get('display_prices_with_shipping');
		if ($show_shipping)
		{
			$article_link = $config->get('article_shipping', '');
			$shipping_cost_link = JRoute::_('index.php?option=com_content&view=article&id='.$article_link);
			$view->assign( 'shipping_cost_link', $shipping_cost_link );
		}

		$invalidQuantity = '0';
		if (empty($values))
		{
			$product_qty = '1';

			// get the default set of attribute_csv
			$default_attributes = CitruscartHelperProduct::getDefaultAttributes( $product_id );
			sort($default_attributes);
			$attributes_csv = implode( ',', $default_attributes );
			$availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getAvailableQuantity ( $product_id, $attributes_csv );
			if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
			{
				$invalidQuantity = '1';
			}
		}

		if (!empty($values))
		{
			$product_id = !empty( $values['product_id'] ) ? (int) $values['product_id'] : $input->getInt( 'product_id' );
			$product_qty = !empty( $values['product_qty'] ) ? (int) $values['product_qty'] : '1';

			// TODO only display attributes available based on the first selected attribute?
			$attributes = array();
			foreach ($values as $key=>$value)
			{
				if (substr($key, 0, 10) == 'attribute_')
				{
					$attributes[] = $value;
				}
			}
			sort($attributes);
			$attributes_csv = implode( ',', $attributes );

			// Integrity checks on quantity being added
			if ($product_qty < 0) {
				$product_qty = '1';
			}

			// using a helper file to determine the product's information related to inventory
			$availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getAvailableQuantity ( $product_id, $attributes_csv );
			if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
			{
				$invalidQuantity = '1';
			}

			// adjust the displayed price based on the selected attributes
			$table = JTable::getInstance('ProductAttributeOptions', 'CitruscartTable');
			$attrs = array();
			foreach ($attributes as $attrib_id)
			{
				// load the attrib's object
				$table->load( $attrib_id );
				// update the price
				//$row->price = $row->price + floatval( "$table->productattributeoption_prefix"."$table->productattributeoption_price");

				// is not + or -
				if($table->productattributeoption_prefix == '=')
				{
					$row->price = floatval( $table->productattributeoption_price );
				}
				else
				{
					$row->price = $row->price + floatval( "$table->productattributeoption_prefix"."$table->productattributeoption_price");
				}
				$attrs[] = $table->productattributeoption_id;
			}
			$row->sku =  CitruscartHelperProduct::getProductSKU($row, $attrs);
			$view->assign( 'item', $row );
		}

		$view->assign( 'availableQuantity', $availableQuantity );
		$view->assign( 'invalidQuantity', $invalidQuantity );



		ob_start();
		JFactory::getApplication()->triggerEvent( 'onDisplayProductAttributeOptions', array( $row->product_id ) );
		$view->assign( 'onDisplayProductAttributeOptions', ob_get_contents() );
		ob_end_clean();

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 *
	 */
	function updateAddToCart()
	{
		$input =JFactory::getApplication()->input;
		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		// get elements from post
		$elements = json_decode( preg_replace('/[\n\r]+/', '\n', $input->get( 'elements', '', 'post', 'string' ) ) );

		// convert elements to array that can be binded
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$values = CitruscartHelperBase::elementsToArray( $elements );

		// now get the summary
		$html = $this->getAddToCart( $values['product_id'], $values );

		$response['msg'] = $html;
		// encode and echo (need to echo to send back to browser)
		echo json_encode($response);
		return;
	}

	/**
	 * Gets a product's files list
	 * formatted for display
	 *
	 * @param int $address_id
	 * @return string html
	 */
	function getFiles( $product_id )
	{
		$html = '';

		// get the product's files
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance( 'ProductFiles', 'CitruscartModel' );
		$model->setState( 'filter_product', $product_id );
		$model->setState( 'filter_enabled', 1 );
		//$model->setState( 'filter_purchaserequired', 1 );
		$items = $model->getList();

		// get the user's active subscriptions to this product, if possible
		$submodel = JModelLegacy::getInstance( 'Subscriptions', 'CitruscartModel' );
		$submodel->setState('filter_userid', JFactory::getUser()->id);
		$submodel->setState('filter_productid', $product_id);
		$subs = $submodel->getList();

		if (!empty($items))
		{
			// reconcile the list of files to the date the sub's files were last checked
			Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' );
			$subhelper = new CitruscartHelperSubscription();
			$subhelper->reconcileFiles($subs);

			Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
			$helper = CitruscartHelperBase::getInstance( 'ProductDownload', 'CitruscartHelper' );
			$filtered_items = $helper->filterRestricted( $items, JFactory::getUser()->id );

			$view   = $this->getView( 'products', 'html' );
			$view->set( '_controller', 'products' );
			$view->set( '_view', 'products' );
			$view->set( '_doTask', true);
			$view->set( 'hidemenu', true);
			$view->setModel( $model, true );
			$view->setLayout( 'product_files' );
			$view->set('downloadItems', $filtered_items[0]);
			$view->set('nondownloadItems', $filtered_items[1]);
			$view->set('product_id', $product_id);

			ob_start();
			$view->display();
			$html = ob_get_contents();
			ob_end_clean();
		}

		return $html;
	}

	/**
	 * Gets a product's related items
	 * formatted for display
	 *
	 * @param int $address_id
	 * @return string html
	 */
	function getRelationshipsHtml( $product_id, $relation_type='relates' )
	{
		$input =JFactory::getApplication()->input;
		$html = '';
		$validation = "";

		// get the list
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance( 'ProductRelations', 'CitruscartModel' );
		$model->setState( 'filter_relation', $relation_type );

		switch ($relation_type)
		{
			case "requires":
				$model->setState( 'filter_product_from', $product_id );
				$check_quantity = false;
				$layout = 'product_requirements';
				break;
			case "parent":
			case "child":
			case "children":
				$model->setState( 'filter_product_from', $product_id );
				$check_quantity = true;
				$validation = "index.php?option=com_citruscart&view=products&task=validateChildren&format=raw";
				$layout = 'product_children';
				break;
			case "relates":
				$model->setState( 'filter_product', $product_id );
				$check_quantity = false;
				$layout = 'product_relations';
				break;
			default:
				return $html;
				break;
		}

		if ($items = $model->getList())
		{
			$filter_category = $model->getState('filter_category', $input->get('filter_category'));
			if (empty($filter_category))
			{
				$categories = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getCategories( $product_id );
				if (!empty($categories))
				{
					$filter_category = $categories[0];
				}
			}

			foreach ($items as $key=>$item)
			{
				if ($check_quantity)
				{
					// TODO Unset $items[$key] if
					// this is out of stock &&
					// check_inventory &&
					// item for sale
				}

				if ($item->product_id_from == $product_id)
				{
					// display the _product_to
					$item->product_id = $item->product_id_to;
					$item->product_name = $item->product_name_to;
					$item->product_model = $item->product_model_to;
					$item->product_sku = $item->product_sku_to;
					$item->product_price = $item->product_price_to;
				}
				else
				{
					// display the _product_from
					$item->product_id = $item->product_id_from;
					$item->product_name = $item->product_name_from;
					$item->product_model = $item->product_model_from;
					$item->product_sku = $item->product_sku_from;
					$item->product_price = $item->product_price_from;
				}

				$itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->product( $item->product_id, $filter_category, true );
				$item->itemid = $input->getInt('Itemid', $itemid);
			}
		}

		if (!empty($items))
		{
			$view   = $this->getView( 'products', 'html' );
			$view->set( '_controller', 'products' );
			$view->set( '_view', 'products' );
			$view->set( '_doTask', true);
			$view->set( 'hidemenu', true);
			$view->setModel( $model, true );
			$view->setLayout( $layout );
			$view->set('items', $items);
			$view->set('product_id', $product_id);
			$view->assign('filter_category', $filter_category);
			$view->assign('validation', $validation );

			ob_start();
			$view->display();
			$html = ob_get_contents();
			ob_end_clean();
		}

		return $html;
	}

	/**
	 * downloads a file
	 *
	 * @return void
	 */
	function downloadFile()
	{
		$input =JFactory::getApplication()->input;
		$user = JFactory::getUser();
		$productfile_id = intval( $input->get( 'id', '', 'request', 'int' ) );
		$product_id = intval( $input->get( 'product_id', '', 'request', 'int' ) );
		$link = 'index.php?option=com_citruscart&controller=products&view=products&task=view&id='.$product_id;

		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance( 'ProductDownload', 'CitruscartHelper' );

		if ( !$canView = $helper->canDownload( $productfile_id, JFactory::getUser()->id ) )
		{
			$this->messagetype = 'notice';
			$this->message = JText::_('COM_CITRUSCART_NOT_AUTHORIZED_TO_DOWNLOAD_FILE');
			$this->setRedirect( $link, $this->message, $this->messagetype );
			return false;
		}
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$productfile = JTable::getInstance( 'ProductFiles', 'CitruscartTable' );
		$productfile->load( $productfile_id );
		if (empty($productfile->productfile_id))
		{
			$this->messagetype = 'notice';
			$this->message = JText::_('COM_CITRUSCART_INVALID FILE');
			$this->setRedirect( $link, $this->message, $this->messagetype );
			return false;
		}

		// log and download
		Citruscart::load( 'CitruscartFile', 'library.file' );

		// Log the download
		$productfile->logDownload( $user->id );

		// After download complete it will update the productdownloads on the basis of the user

		// geting the ProductDownloadId to updated for which productdownload_max  is greater then 0
		$productToDownload = $helper->getProductDownloadInfo($productfile->productfile_id, $user->id);;

		if (!empty($productToDownload))
		{
			$productDownload = JTable::getInstance('ProductDownloads', 'CitruscartTable');
			$productDownload->load($productToDownload->productdownload_id);
			$productDownload->productdownload_max = $productDownload->productdownload_max-1;
			if (!$productDownload->save())
			{
				// TODO in case product Download is not updating properly .
			}
		}

		if ($downloadFile = CitruscartFile::download( $productfile ))
		{
			$link = JRoute::_( $link, false );
			$this->setRedirect( $link );
		}
	}

	/**
	 *
	 * @return void
	 */
	function search()
	{
		$input =JFactory::getApplication()->input;
		$input->set( 'view', $this->get('suffix') );
		$input->set( 'layout', 'search' );
		$input->set( 'search', true );
		parent::display();
	}

	/**
	 * Verifies the fields in a submitted form.  Uses the table's check() method.
	 * Will often be overridden. Is expected to be called via Ajax
	 *
	 * @return unknown_type
	 */
	function validate()
	{
		$input =JFactory::getApplication()->input;
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = new CitruscartHelperBase();

		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		// get elements from post
		$elements = json_decode( preg_replace('/[\n\r]+/', '\n', $input->get( 'elements', '', 'post', 'string' ) ) );

		// validate it using table's ->check() method
		if (empty($elements))
		{
			// if it fails check, return message
			$response['error'] = '1';
			$response['msg'] = $helper->generateMessage(JText::_('COM_CITRUSCART_COULD_NOT_PROCESS_FORM'));
			echo ( json_encode( $response ) );
			return;
		}

		if (!Citruscart::getInstance()->get('shop_enabled', '1'))
		{
			$response['msg'] = $helper->generateMessage( "Shop Disabled" );
			$response['error'] = '1';
			echo ( json_encode( $response ) );
			return false;
		}

		// convert elements to array that can be binded
		$values = CitruscartHelperBase::elementsToArray( $elements );
		$product_id = !empty( $values['product_id'] ) ? (int) $values['product_id'] : $input->getInt( 'product_id' );
		$product_qty = !empty( $values['product_qty'] ) ? (int) $values['product_qty'] : '1';

		$attributes = array();
		foreach ($values as $key=>$value)
		{
			if (substr($key, 0, 10) == 'attribute_')
			{
				$attributes[] = $value;
			}
		}
		sort($attributes);
		$attributes_csv = implode( ',', $attributes );

		// Integrity checks on quantity being added
		if ($product_qty < 0) {
			$product_qty = '1';
		}

		// using a helper file to determine the product's information related to inventory
		$availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getAvailableQuantity ( $product_id, $attributes_csv );
		if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
		{
			$response['msg'] = $helper->generateMessage( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $availableQuantity->product_name, $product_qty ) );
			$response['error'] = '1';
			echo ( json_encode( $response ) );
			return false;
		}

		$product = JTable::getInstance('Products', 'CitruscartTable');
		$product->load( array( 'product_id'=>$product_id ) );

		// if product notforsale, fail
		if ($product->product_notforsale)
		{
			$response['msg'] = $helper->generateMessage( "Product Not For Sale" );
			$response['error'] = '1';
			echo ( json_encode( $response ) );
			return false;
		}

		$user = JFactory::getUser();
		$keynames = array();
		$keynames['user_id'] = $user->id;
		if (empty($user->id))
		{
			$session = JFactory::getSession();
			$keynames['session_id'] = $session->getId();
		}
		$keynames['product_id'] = $product_id;

		$cartitem = JTable::getInstance( 'Carts', 'CitruscartTable' );
		$cartitem->load($keynames);
		if ($product->quantity_restriction)
		{
			if( $product->quantity_restriction )
			{
				$error = false;
				$min = $product->quantity_min;
				$max = $product->quantity_max;

				if( $max )
				{
					$remaining = $max - $cartitem->product_qty;
					if ($product_qty > $remaining )
					{
						$error = true;
						$msg = $helper->generateMessage( "You have reached the maximum quantity for this item. You can order another ".$remaining );
					}
				}
				if( $min )
				{
					if ($product_qty < $min )
					{
						$error = true;
						$msg = $helper->generateMessage( "You have not reached the miminum quantity for this item. You have to order at least ".$min );
					}
				}
			}
			if($error)
			{
				$response['msg'] = $msg;
				$response['error'] = '1';
				echo ( json_encode( $response ) );
				return false;
			}
		}


		// create cart object out of item properties
		$item = new JObject;
		$item->user_id     = JFactory::getUser()->id;
		$item->product_id  = (int) $product_id;
		$item->product_qty = (int) $product_qty;
		$item->product_attributes = $attributes_csv;
		$item->vendor_id   = '0'; // vendors only in enterprise version

		// no matter what, fire this validation plugin event for plugins that extend the checkout workflow
		$results = array();

		$results = JFactory::getApplication()->triggerEvent( "onValidateAddToCart", array( $item, $values ) );

		for ($i=0; $i<count($results); $i++)
		{
			$result = $results[$i];
			if (!empty($result->error))
			{
				Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
				$helper = CitruscartHelperBase::getInstance();
				$response['msg'] = $helper->generateMessage( $result->message );
				$response['error'] = '1';
				echo ( json_encode( $response ) );
				return;
			}
			else
			{
				// if here, all is OK
				$response['error'] = '0';
			}
		}
		echo ( json_encode( $response ) );
		return;
	}

	/**
	 * Verifies the fields in a submitted form.
	 * Then adds the item to the users cart
	 *
	 * @return unknown_type
	 */
	function addToCart()
	{
		$input =JFactory::getApplication()->input;
		JSession::checkToken() or jexit( 'Invalid Token' );
		$product_id = $input->getInt( 'product_id' );
		$product_qty = $input->getInt( 'product_qty' );
		$filter_category = $input->getInt( 'filter_category' );

		Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
		$router = new CitruscartHelperRoute();
		if (!$itemid = $router->product( $product_id, $filter_category, true ))
		{
			$itemid = $router->category( 1, true );
		}

		// set the default redirect URL
		$redirect = "index.php?option=com_citruscart&view=products&task=view&id=$product_id&filter_category=$filter_category&Itemid=".$itemid;
		$redirect = JRoute::_( $redirect, false );

		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance();
		if (!Citruscart::getInstance()->get('shop_enabled', '1'))
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_SHOP_DISABLED');
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}

		// convert elements to array that can be binded
		$values =$input->getArray($_POST);

		$attributes = array();
		foreach ($values as $key=>$value)
		{
			if (substr($key, 0, 10) == 'attribute_')
			{
				$attributes[] = $value;
			}
		}
		sort($attributes);
		$attributes_csv = implode( ',', $attributes );

		// Integrity checks on quantity being added
		if ($product_qty < 0) {
			$product_qty = '1';
		}

		// using a helper file to determine the product's information related to inventory
		$availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getAvailableQuantity ( $product_id, $attributes_csv );
		if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $availableQuantity->product_name, $product_qty ) );
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}

		// do the item's charges recur? does the cart already have a subscription in it?  if so, fail with notice
		$product = JTable::getInstance('Products', 'CitruscartTable');
		$product->load( array( 'product_id'=>$product_id ) );

		// if product notforsale, fail
		if ($product->product_notforsale)
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_PRODUCT_NOT_FOR_SALE');
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}

		$user = JFactory::getUser();
		$cart_id = $user->id;
		$id_type = "user_id";
		if (empty($user->id))
		{
			$session = JFactory::getSession();
			$cart_id = $session->getId();
			$id_type = "session";
		}

		Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
		$carthelper = new CitruscartHelperCarts();

		$cart_recurs = $carthelper->hasRecurringItem( $cart_id, $id_type );
		if ($product->product_recurs && $cart_recurs)
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_CART_ALREADY_RECURS');
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}

		if ($product->product_recurs)
		{
			$product_qty = '1';
		}

		// create cart object out of item properties
		$item = new JObject;
		$item->user_id     = JFactory::getUser()->id;
		$item->product_id  = (int) $product_id;
		$item->product_qty = (int) $product_qty;
		$item->product_attributes = $attributes_csv;
		$item->vendor_id   = '0'; // vendors only in enterprise version

		// onAfterCreateItemForAddToCart: plugin can add values to the item before it is being validated /added
		// once the extra field(s) have been set, they will get automatically saved

		$results = JFactory::getApplication()->triggerEvent( "onAfterCreateItemForAddToCart", array( $item, $values ) );
		foreach ($results as $result)
		{
			foreach($result as $key=>$value)
			{
				$item->set($key,$value);
			}
		}

		// does the user/cart match all dependencies?
		$canAddToCart = $carthelper->canAddItem( $item, $cart_id, $id_type );
		if (!$canAddToCart)
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_CANNOT_ADD_ITEM_TO_CART') . " - " . $carthelper->getError();
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}

		// no matter what, fire this validation plugin event for plugins that extend the checkout workflow
		$results = array();

		$results = JFactory::getApplication()->triggerEvent( "onBeforeAddToCart", array( $item, $values ) );

		for ($i=0; $i<count($results); $i++)
		{
			$result = $results[$i];
			if (!empty($result->error))
			{
				$this->messagetype  = 'notice';
				$this->message      = $result->message;
				$this->setRedirect( $redirect, $this->message, $this->messagetype );
				return;
			}
		}

		// if here, add to cart

		// After login, session_id is changed by Joomla, so store this for reference
		$session = JFactory::getSession();
		$session->set( 'old_sessionid', $session->getId() );

		// add the item to the cart
		Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
		$cart_helper = new CitruscartHelperCarts();
		$cartitem = $cart_helper->addItem( $item );

		// fire plugin event

		JFactory::getApplication()->triggerEvent( 'onAfterAddToCart', array( $cartitem, $values ) );

		// get the 'success' redirect url
		switch (Citruscart::getInstance()->get('addtocartaction', 'redirect'))
		{
			case "0":
			case "none":
				// redirects back to product page
				break;
			case "lightbox":
			case "redirect":
			default:
				// if a base64_encoded url is present as return, use that as the return url
				// otherwise return == the product view page
				$returnUrl = base64_encode( $redirect );
				if ($return_url = $input->get('return', '', 'method', 'base64'))
				{
					$return_url = base64_decode($return_url);
					if (JURI::isInternal($return_url))
					{
						$returnUrl = base64_encode( $return_url );
					}
				}

				// if a base64_encoded url is present as redirect, redirect there,
				// otherwise redirect to the cart
				$itemid = $router->findItemid( array('view'=>'checkout') );
				$redirect = JRoute::_( "index.php?option=com_citruscart&view=carts&Itemid=".$itemid, false );
				if ($redirect_url = $input->get('redirect', '', 'method', 'base64'))
				{
					$redirect_url = base64_decode($redirect_url);
					if (JURI::isInternal($redirect_url))
					{
						$redirect = $redirect_url;
					}
				}

				//$returnUrl = base64_encode( $redirect );
				//$itemid = $router->findItemid( array('view'=>'checkout') );
				//$redirect = JRoute::_( "index.php?option=com_citruscart&view=carts&Itemid=".$itemid, false );
				if (strpos($redirect, '?') === false) {
					$redirect .= "?return=".$returnUrl;
				} else { $redirect .= "&return=".$returnUrl;
				}

				break;
		}

		$this->messagetype  = 'message';
		$this->message      = JText::_('COM_CITRUSCART_ITEM_ADDED_TO_YOUR_CART');
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
		return;

	}

	/**
	 * Gets all the product's user reviews
	 * @param $product_id
	 * @return unknown_type
	 */
	function getComments($product_id)
	{
		$input =JFactory::getApplication()->input;
		$html = '';
		$view   = $this->getView( 'products', 'html' );

		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance( 'productcomments', 'CitruscartModel' );
		$selectsort = $input->get('default_selectsort', '');
		$model->setstate('order', $selectsort );
		$limitstart = $input->getInt('limitstart', 0);
		$model->setId( $product_id );
		$model->setstate('limitstart', $limitstart );
		$model->setstate('filter_product', $product_id );
		$model->setstate('filter_enabled', '1' );
		$reviews = $model->getList();

		$count = count($reviews);

		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', true);
		$view->setModel( $model, true );
		$view->setLayout( 'product_comments' );
		$view->assign('product_id', $product_id);
		$view->assign('count', $count);
		$view->assign('reviews', $reviews);

		$user_id = JFactory::getUser()->id;
		$productreview = CitruscartHelperProduct::getUserAndProductIdForReview($product_id, $user_id);
		$purchase_enable = Citruscart::getInstance()->get('purchase_leave_review_enable', '0');
		$login_enable = Citruscart::getInstance()->get('login_review_enable', '0');
		$product_review_enable=Citruscart::getInstance()->get('product_review_enable', '0');

		$result = 1;
		if($product_review_enable=='1')
		{
			$review_enable=1;
		}
		else
		{
			$review_enable=0;
		}
		if (($login_enable == '1'))
		{
			if ($user_id)
			{
				$order_enable = '1';

				if ($purchase_enable == '1')
				{
					$orderexist = CitruscartHelperProduct::getOrders($product_id);
					if (!$orderexist)
					{
						$order_enable = '0';

					}
				}

				if (($order_enable != '1') || !empty($productreview) )
				{
					$result = 0;
				}
			}
			else
			{
				$result = 0;
			}
		}


		$view->assign('review_enable',$review_enable);
		$view->assign('result', $result);
		$view->assign('click','index.php?option=com_citruscart&controller=products&view=products&task=addReview');
		$view->assign('selectsort', $selectsort);
		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/**
	 * Verifies the fields in a submitted form.  Uses the table's check() method.
	 * Will often be overridden. Is expected to be called via Ajax
	 *
	 * @return unknown_type
	 */
	function validateChildren()
	{
		$input =JFactory::getApplication()->input;
		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance();

		// get elements from post
		$elements = json_decode( preg_replace('/[\n\r]+/', '\n', $input->get( 'elements', '', 'post', 'string' ) ) );

		// validate it using table's ->check() method
		if (empty($elements))
		{
			// if it fails check, return message
			$response['error'] = '1';
			$response['msg'] = $helper->generateMessage( "Could not process form" );
			echo ( json_encode( $response ) );
			return;
		}

		if (!Citruscart::getInstance()->get('shop_enabled', '1'))
		{
			$response['msg'] = $helper->generateMessage( "Shop Disabled" );
			$response['error'] = '1';
			echo ( json_encode( $response ) );
			return false;
		}

		// convert elements to array that can be binded
		$values = CitruscartHelperBase::elementsToArray( $elements );
		$attributes_csv = '';
		$product_id = !empty( $values['product_id'] ) ? (int) $values['product_id'] : $input->getInt( 'product_id' );
		$quantities = !empty( $values['quantities'] ) ? $values['quantities'] : array();

		$items = array(); // this will collect the items to add to the cart
		$attributes_csv = '';

		$user = JFactory::getUser();
		$cart_id = $user->id;
		$id_type = "user_id";
		if (empty($user->id))
		{
			$session = JFactory::getSession();
			$cart_id = $session->getId();
			$id_type = "session";
		}

		Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
		$carthelper = new CitruscartHelperCarts();

		$cart_recurs = $carthelper->hasRecurringItem( $cart_id, $id_type );

		// TODO get the children
		// loop thru each child,
		// get the list
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance( 'ProductRelations', 'CitruscartModel' );
		$model->setState( 'filter_product', $product_id );
		$model->setState( 'filter_relation', 'parent' );
		if ($children = $model->getList())
		{
			foreach ($children as $child)
			{
				$product_qty = $quantities[$child->product_id_to];

				// Integrity checks on quantity being added
				if ($product_qty < 0) {
					$product_qty = '1';
				}

				// using a helper file to determine the product's information related to inventory
				$availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getAvailableQuantity ( $child->product_id_to, $attributes_csv );
				if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
				{
					$response['msg'] = $helper->generateMessage( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $availableQuantity->product_name, $product_qty ) );
					$response['error'] = '1';
					echo ( json_encode( $response ) );
					return false;
				}

				// do the item's charges recur? does the cart already have a subscription in it?  if so, fail with notice
				$product = JTable::getInstance('Products', 'CitruscartTable');
				$product->load( array( 'product_id'=>$child->product_id_to ) );

				// if product notforsale, fail
				if ($product->product_notforsale)
				{
					$response['msg'] = $helper->generateMessage( "Product Not For Sale" );
					$response['error'] = '1';
					echo ( json_encode( $response ) );
					return false;
				}

				if ($product->product_recurs && $cart_recurs)
				{
					$response['msg'] = $helper->generateMessage( "Cart Already Recurs" );
					$response['error'] = '1';
					echo ( json_encode( $response ) );
					return false;
				}

				if ($product->product_recurs)
				{
					$product_qty = '1';
				}

				// create cart object out of item properties
				$item = new JObject;
				$item->user_id     = JFactory::getUser()->id;
				$item->product_id  = (int) $child->product_id_to;
				$item->product_qty = (int) $product_qty;
				$item->product_attributes = $attributes_csv;
				$item->vendor_id   = '0'; // vendors only in enterprise version

				// does the user/cart match all dependencies?
				$canAddToCart = $carthelper->canAddItem( $item, $cart_id, $id_type );
				if (!$canAddToCart)
				{
					$response['msg'] = $helper->generateMessage( JText::_('COM_CITRUSCART_CANNOT_ADD_ITEM_TO_CART') . " - " . $carthelper->getError() );
					$response['error'] = '1';
					echo ( json_encode( $response ) );
					return false;
				}

				// no matter what, fire this validation plugin event for plugins that extend the checkout workflow
				$results = array();

				$results = JFactory::getApplication()->triggerEvent( "onValidateAddToCart", array( $item, $values ) );

				for ($i=0; $i<count($results); $i++)
				{
					$result = $results[$i];
					if (!empty($result->error))
					{
						$response['msg'] = $helper->generateMessage( $result->message );
						$response['error'] = '1';
						echo ( json_encode( $response ) );
						return false;
					}
				}

				// if here, add to cart
				$items[] = $item;
			}
		}

		if (!empty($items))
		{
			$response['error'] = '0';
		}
		else
		{
			$response['msg'] = $helper->generateMessage( "No Items Passed Validity Check" );
			$response['error'] = '1';
		}

		echo ( json_encode( $response ) );
		return;
	}

	/**
	 * Verifies the fields in a submitted form.
	 * Then adds the item to the users cart
	 *
	 * @return unknown_type
	 */
	function addChildrenToCart()
	{
		$input =JFactory::getApplication()->input;
		JSession::checkToken() or jexit( 'Invalid Token' );
		$product_id = $input->getInt( 'product_id' );
		$quantities = $input->get('quantities', array(0), 'request', 'array');
		$filter_category = $input->getInt( 'filter_category' );

		Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
		$router = new CitruscartHelperRoute();
		if (!$itemid = $router->product( $product_id, $filter_category, true ))
		{
			$itemid = $router->category( 1, true );
		}

		// set the default redirect URL
		$redirect = "index.php?option=com_citruscart&view=products&task=view&id=".$product_id."&filter_category=".$filter_category."&Itemid=".$itemid;
		$redirect = JRoute::_( $redirect, false );

		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		$helper = CitruscartHelperBase::getInstance();
		if (!Citruscart::getInstance()->get('shop_enabled', '1'))
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_('COM_CITRUSCART_SHOP_DISABLED');
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}

		$items = array(); // this will collect the items to add to the cart

		// convert elements to array that can be binded
		//$values = JRequest::get('post');
		$values = $input->getArray($_POST);
		$attributes_csv = '';

		$user = JFactory::getUser();
		$cart_id = $user->id;
		$id_type = "user_id";
		if (empty($user->id))
		{
			$session = JFactory::getSession();
			$cart_id = $session->getId();
			$id_type = "session";
		}

		Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
		$carthelper = new CitruscartHelperCarts();

		$cart_recurs = $carthelper->hasRecurringItem( $cart_id, $id_type );

		// TODO get the children
		// loop thru each child,
		// get the list
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance( 'ProductRelations', 'CitruscartModel' );
		$model->setState( 'filter_product', $product_id );
		$model->setState( 'filter_relation', 'parent' );
		if ($children = $model->getList())
		{
			foreach ($children as $child)
			{
				$product_qty = $quantities[$child->product_id_to];

				// Integrity checks on quantity being added
				if ($product_qty < 0) {
					$product_qty = '1';
				}

				// using a helper file to determine the product's information related to inventory
				$availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getAvailableQuantity ( $child->product_id_to, $attributes_csv );
				if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
				{
					$this->messagetype  = 'notice';
					$this->message      = JText::_( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $availableQuantity->product_name, $product_qty ) );
					$this->setRedirect( $redirect, $this->message, $this->messagetype );
					return;
				}

				// do the item's charges recur? does the cart already have a subscription in it?  if so, fail with notice
				$product = JTable::getInstance('Products', 'CitruscartTable');
				$product->load( array( 'product_id'=>$child->product_id_to ) );

				// if product notforsale, fail
				if ($product->product_notforsale)
				{
					$this->messagetype  = 'notice';
					$this->message      = JText::_('COM_CITRUSCART_PRODUCT_NOT_FOR_SALE');
					$this->setRedirect( $redirect, $this->message, $this->messagetype );
					return;
				}

				if ($product->product_recurs && $cart_recurs)
				{
					$this->messagetype  = 'notice';
					$this->message      = JText::_('COM_CITRUSCART_CART_ALREADY_RECURS');
					$this->setRedirect( $redirect, $this->message, $this->messagetype );
					return;
				}

				if ($product->product_recurs)
				{
					$product_qty = '1';
				}

				// create cart object out of item properties
				$item = new JObject;
				$item->user_id     = JFactory::getUser()->id;
				$item->product_id  = (int) $child->product_id_to;
				$item->product_qty = (int) $product_qty;
				$item->product_attributes = $attributes_csv;
				$item->vendor_id   = '0'; // vendors only in enterprise version

				// does the user/cart match all dependencies?
				$canAddToCart = $carthelper->canAddItem( $item, $cart_id, $id_type );
				if (!$canAddToCart)
				{
					$this->messagetype  = 'notice';
					$this->message      = JText::_('COM_CITRUSCART_CANNOT_ADD_ITEM_TO_CART') . " - " . $carthelper->getError();
					$this->setRedirect( $redirect, $this->message, $this->messagetype );
					return;
				}

				// no matter what, fire this validation plugin event for plugins that extend the checkout workflow
				$results = array();

				$results = JFactory::getApplication()->triggerEvent( "onBeforeAddToCart", array( $item, $values ) );

				for ($i=0; $i<count($results); $i++)
				{
					$result = $results[$i];
					if (!empty($result->error))
					{
						$this->messagetype  = 'notice';
						$this->message      = $result->message;
						$this->setRedirect( $redirect, $this->message, $this->messagetype );
						return;
					}
				}

				// if here, add to cart
				$items[] = $item;
			}
		}

		if (!empty($items))
		{
			Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
			foreach ($items as $item)
			{
				// add the item to the cart
				$cart_helper = new CitruscartHelperCarts();
				$cartitem = $cart_helper->addItem( $item );

				// fire plugin event

				JFactory::getApplication()->triggerEvent( 'onAfterAddToCart', array( $cartitem, $values ) );
			}

			$this->messagetype  = 'message';
			$this->message      = JText::_('COM_CITRUSCART_ITEMS_ADDED_TO_YOUR_CART');
		}

		// After login, session_id is changed by Joomla, so store this for reference
		$session = JFactory::getSession();
		$session->set( 'old_sessionid', $session->getId() );

		// get the 'success' redirect url
		// TODO Enable redirect via base64_encoded urls?
		switch (Citruscart::getInstance()->get('addtocartaction', 'redirect'))
		{
			case "redirect":
				$returnUrl = base64_encode( $redirect );
				$itemid = $router->findItemid( array('view'=>'checkout') );
				$redirect = JRoute::_( "index.php?option=com_citruscart&view=carts&Itemid=".$itemid, false );
				if (strpos($redirect, '?') === false) {
					$redirect .= "?return=".$returnUrl;
				} else { $redirect .= "&return=".$returnUrl;
				}
				break;
			case "0":
			case "none":
				break;
			case "lightbox":
			default:
				// TODO Figure out how to get the lightbox to display even after a redirect
				break;
		}

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
		return;
	}


	/**
	 *
	 *
	 */
	function addReview()
	{
		$input =JFactory::getApplication()->input;
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$productreviews = JTable::getInstance('productcomments', 'CitruscartTable');
		$post = $input->getArray($_POST);
		$captcha_enable = Citruscart::getInstance()->get('use_captcha', '0');
		$privatekey = "6LcAcbwSAAAAANZOTZWYzYWRULBU_S--368ld2Fb";
		$Itemid = $post['Itemid'];
		$recaptcha_challenge_field = $post['recaptcha_challenge_field'];
		$recaptcha_response_field = $post['recaptcha_response_field'];

		$captcha='1';
		if ($captcha_enable)
		{
			$captcha='0';

			Citruscart::load( 'CitruscartRecaptcha', 'library.recaptcha' );
			$recaptcha = new CitruscartRecaptcha();
			if ($_POST["recaptcha_response_field"])
			{
				$resp = $recaptcha->recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $recaptcha_challenge_field, $recaptcha_response_field);
				if ($resp->is_valid)
				{
					$captcha='1';
				}
			}
		}

		$product_id = $post['product_id'];
		$date = JFactory::getDate();
		$productreviews->bind($post);
		$productreviews->created_date = $date->toSql();
		$redirect = "index.php?option=com_citruscart&view=products&task=view&id=".$product_id."filter_category=".$product_id."&Itemid=".$Itemid;
		$redirect = JRoute::_( $redirect );

		if ($captcha == '1')
		{
			if (!$productreviews->save())
			{
				$this->messagetype  = 'message';
				$this->message      = JText::_('COM_CITRUSCART_UNABLE_TO_SAVE_REVIEW')." :: ".$productreviews->getError();
			}
			else
			{

				JFactory::getApplication()->triggerEvent( 'onAfterSaveProductComments', array( $productreviews ) );
				$this->messagetype  = 'message';
				$this->message      = JText::_('COM_CITRUSCART_SUCCESSFULLY_SUBMITTED_REVIEW');
			}
		}
		else
		{
			$this->messagetype  = 'message';
			$this->message      = JText::_('COM_CITRUSCART_INCORRECT_CAPTCHA');
		}
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Adding helpfulness of review
	 *
	 */
	function reviewHelpfullness()
	{
		$app =JFactory::getApplication();
		$user_id = JFactory::getUser()->id;
		$Itemid = $app->input->getInt('Itemid',0);
		$id = $app->input->getInt('product_id', 0);
		$url = "index.php?option=com_citruscart&view=products&task=view&Itemid=".$Itemid."&id=".$id;

		if ($user_id)
		{
			$productcomment_id = $app->input->getInt('productcomment_id', '');
			Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
			$producthelper = new CitruscartHelperProduct();
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$productcomment = JTable::getInstance('productcomments', 'CitruscartTable');
			$productcomment->load( $productcomment_id );

			$helpful_votes_total = $productcomment->helpful_votes_total;
			$helpful_votes_total = $helpful_votes_total + 1;
			$helpfulness = $app->input->getInt('helpfulness', '');
			if ($helpfulness == 1)
			{
				$helpful_vote = $productcomment->helpful_votes;
				$helpful_vote_new = $helpful_vote + 1;
				$productcomment->helpful_votes = $helpful_vote_new;
			}
			$productcomment->helpful_votes_total = $helpful_votes_total;

			$report = $app->input->getInt('report', '');
			if ($report == 1)
			{
				$productcomment->reported_count = $productcomment->reported_count + 1;
			}

			$help = array();
			$help['productcomment_id'] = $productcomment_id;
			$help['helpful'] = $helpfulness;
			$help['user_id'] = $user_id;
			$help['reported'] = $report;
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$reviewhelpfulness = JTable::getInstance('ProductCommentsHelpfulness', 'CitruscartTable');
			$reviewhelpfulness->load(array('user_id'=>$user_id));
			if ($report == 1 && !empty($reviewhelpfulness->productcommentshelpfulness_id) && empty($reviewhelpfulness->reported))
			{
				$reviewhelpfulness->reported = 1;
				$reviewhelpfulness->save();

				$productcomment->save();
				$app->enqueueMessage( JText::sprintf("COM_CITRUSCART_THANKS_FOR_REPORTING_THIS_COMMENT"));
				$app->redirect($url);
				return;
			}

			$reviewhelpfulness->bind($help);
			if (!empty($reviewhelpfulness->productcommentshelpfulness_id))
			{
				$app->enqueueMessage( JText::sprintf("COM_CITRUSCART_YOU_HAVE_ALREADY_COMMENTED_ON_THIS_REVIEW"));
				$app->redirect($url);
				return;
			}
			else
			{
				$reviewhelpfulness->save();
				$productcomment->save();
				$app->enqueueMessage( JText::sprintf("COM_CITRUSCART_THANKS_FOR_YOUR_FEEDBACK_ON_THIS_COMMENT"));
				$app->redirect($url);
				return;
			}
		}
		else
		{
			Citruscart::load( "CitruscartHelperUser", 'helpers.user' );
			$redirect = JRoute::_( CitruscartHelperUser::getUserLoginUrl( $url ), false );
			$app->redirect( $redirect );
			return;
		}
	}
}

