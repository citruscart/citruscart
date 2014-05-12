<?php
/**
 * @package	Citruscart
 * @author 	Citruscart Team
 * @link 	http://www.Citruscart.com
 * @copyright Copyright (C) 2014 Citruscart Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_citruscart/defines.php'))
{
    // Check the registry to see if our Citruscart class has been overridden
    if ( !class_exists('Citruscart') )
        JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

     Citruscart::load( 'CitruscartPluginBase', 'library.plugins._base' );

    class plgContentCitruscart_Content_Product extends CitruscartPluginBase
    {
    	/**
    	 * @var $_element  string  Should always correspond with the plugin's filename,
    	 *                         forcing it to be unique
    	 */
        var $_element    = 'citruscart_content_product';

    	public function __construct(& $subject, $config)
    	{
    		parent::__construct($subject, $config);
    		$this->loadLanguage( '', JPATH_ADMINISTRATOR );
    		$this->loadLanguage('com_citruscart');
    	}

     	/**
         * Checks the extension is installed
         *
         * @return boolean
         */
        private function isInstalled()
        {
            $success = false;

            jimport('joomla.filesystem.file');
            if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_citruscart/defines.php'))
            {
                $success = true;
                // Check the registry to see if our Citruscart class has been overridden
                if ( !class_exists('Citruscart') )
                    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );
            }
            return $success;
        }

        /**
         * Joomla < 1.6 prepare content function
         *
         * Enter description here ...
         * @param unknown_type $row
         * @param unknown_type $params
         * @param unknown_type $page
         */
        public function onPrepareContent( &$row, &$params, $page=0 )
        {
            $context = 'com_content.article';
            return $this->doContentPrepare($context, $row, $params, $page);
        }

        /**
         * Joomla 1.6+ prepare content function
         *
         * @param unknown_type $context
         * @param unknown_type $article
         * @param unknown_type $params
         * @param unknown_type $page
         */
        public function onContentPrepare($context, &$article, &$params, $page = 0)
        {
            return $this->doContentPrepare($context, $article, $params, $page);
        }

    	/**
         * Search for the tag and replace it with the product view {Citruscartproduct}
         *
         * @param $article
         * @param $params
         * @param $limitstart
         */
       	private function doContentPrepare( $context, &$row, &$params, $page=0 )
       	{
       		if( !$this->isInstalled() )
       			return true;

    	   	// simple performance check to determine whether bot should process further
    		if ( JString::strpos( $row->text, 'citruscartproduct' ) === false ) {
    			return true;
    		}

    		// Get plugin info
    		$plugin = JPluginHelper::getPlugin('content', 'citruscart_content_product');

    	 	// expression to search for
    	 	$regex = '/{citruscartproduct\s*.*?}/i';

    	 	$pluginParams = new DSCParameter( $plugin->params );

    		// check whether plugin has been unpublished
    		if ( !$pluginParams->get( 'enabled', 1 ) ) {
    			$row->text = preg_replace( $regex, '', $row->text );
    			return true;
    		}

    	 	// find all instances of plugin and put in $matches
    		preg_match_all( $regex, $row->text, $matches );

    		// Number of plugins
    	 	$count = count( $matches[0] );

    	 	// plugin only processes if there are any instances of the plugin in the text
    	 	if ( $count )
    	 	{
    	 	    DSC::loadJQuery('latest', true, 'citruscartJQ');

				$doc = JFactory::getDocument();
				$uri = JURI::getInstance();
				$js = "var com_citruscart = {};\n";
				$js.= "com_citruscart.jbase = '".$uri->root()."';\n";
				$doc->addScriptDeclaration($js);

    	 		foreach($matches as $match) {
    	 			$this->showProducts( $row, $matches, $count, $regex );
    	 		}
    		}
       	}

       	/**
       	 * Shows the products
       	 * @param $row
       	 * @param $matches
       	 * @param $count
       	 * @param $regex
       	 * @return unknown_type
       	 */
       	private function showProducts( &$row, &$matches, $count, $regex )
       	{
       	    Citruscart::load( 'CitruscartSelect', 'library.select' );

       		for ( $i=0; $i < $count; $i++ )
    		{
    	 		$load = str_replace( 'citruscartproduct', '', $matches[0][$i] );
    	 		$load = str_replace( '{', '', $load );
    	 		$load = str_replace( '}', '', $load );
    	 		$load = trim( $load );

    			$product	= $this->showProduct( $load );
    			$row->text 	= str_replace($matches[0][$i], $product, $row->text );
    	 	}

    	  	// removes tags without matching
    		$row->text = preg_replace( $regex, '', $row->text );
    	}

    	/**
    	 * Loads an individual product
    	 * and displays it
    	 *
    	 * @param $load
    	 * @return unknown_type
    	 */
    	private function showProduct( $load )
    	{
    		$inline_params = explode(" ", $load);
    		$params = $this->get('params');

    		$params = $params->toArray();

    		$params['attributes'] = array();
    		// Merge plugin parameters with tag parameters, overwriting wherever necessary
    		foreach( $inline_params as $p )
    		{
    			$data = explode("=", $p);
    			$k = $data[0];
    			$v = $data[1];

    			// Merge the attribute options in one subarray
    			if (substr($k, 0, 10) == 'attribute_')
    				$params['attributes'][$k] = $v;
    			else
    				$params[$k] = $v;
    		}

    		// No id set, return
    		if( !array_key_exists('id', $params) )
    			return;

    		// Load Product
    		Citruscart::load('CitruscartModelProducts', 'models.products');
    		$model = JModelLegacy::getInstance('Products', 'CitruscartModel');
    		$model->setId( (int) $params['id'] );
    		$row = $model->getItem();

    		// Error?
    		if( !$row )
    			return;

    	   $categories = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getCategories( $row->product_id );
           if (!empty($categories))
           {
               $filter_category = $categories[0];
           }

    		if (empty($row->product_enabled))
    		{
    			return;
    		}

    		Citruscart::load( 'CitruscartArticle', 'library.article' );
    		$product_description = CitruscartArticle::fromString( $row->product_description );

    		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
    		$cmodel = Citruscart::getClass( 'CitruscartModelCategories', 'models.categories' );
    		$cat = $cmodel->getTable();
    		$cat->load( $filter_category );

    		// Check If the inventroy is set then it will go for the inventory product quantities
    		if ($row->product_check_inventory)
    		{
    			$inventoryList = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getProductQuantities( $row->product_id );

                if (!Citruscart::getInstance()->get('display_out_of_stock') && empty($inventoryList))
                {
                    return;
                }

    			// if there is no entry of product in the productquantities
    			if (count($inventoryList) == 0)
    			{
    				$inventoryList[''] = '0';
    			}
    		}
            

            ob_start();
            JFactory::getApplication()->triggerEvent( 'onBeforeDisplayProduct', array( $row->product_id ) );
            $onBeforeDisplayProduct = ob_get_contents() ;
            ob_end_clean();

            ob_start();
            JFactory::getApplication()->triggerEvent( 'onAfterDisplayProduct', array( $row->product_id ) );
            $onAfterDisplayProduct = ob_get_contents();
            ob_end_clean();

    		$files = $this->getFiles( $row->product_id );
    		$product_buy = $this->getAddToCart( $row->product_id, $params['attributes'], $params );
    		$product_relations = $this->getRelationshipsHtml( $row->product_id, 'relates' );
    		$product_children = $this->getRelationshipsHtml( $row->product_id, 'parent' );
    		$product_requirements = $this->getRelationshipsHtml( $row->product_id, 'requires' );

    		// In this case, we need to add some variables to show the price in the normal view
    		if($params['show_price'] == '1' && $params['show_buy'] == '0')
    		{
    			$vars = new JObject();
	    		$config = Citruscart::getInstance();
	            $show_tax = $config->get('display_prices_with_tax');
	            $vars->show_tax = $show_tax ;
	            $vars->tax = 0 ;
	            $vars->taxtotal = '';
	            $vars->shipping_cost_link = '';

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
	                $vars->taxtotal = $taxtotal;
	                $vars->tax = $tax ;
	            }

	            // TODO What about this??
	            $show_shipping = $config->get('display_prices_with_shipping');
	            if ($show_shipping)
	            {
	                $article_link = $config->get('article_shipping', '');
	                $shipping_cost_link = JRoute::_('index.php?option=com_content&view=article&id='.$article_link);
	                $vars->shipping_cost_link = $shipping_cost_link ;
	            }
    		}
            ob_start();
    		$layout_file = $this->_getLayoutPath($this->_element, 'content', 'view');
    		include( $layout_file );
    		$return = ob_get_contents();
    		ob_end_clean();


    		switch($params['layout'])
    		{
    			case 'product_buy';
    				return $product_buy;
    			case 'product_children';
    				return $product_children;
				case 'product_files';
    				return $product_files;
				case 'product_relations';
    				return $product_relations;
    			case 'product_requirements';
    				return $product_requirements;


    			case 'view':
    			default:
    				return $return;
    		}


    	}

    	/**
         * Gets a product's related items
         * formatted for display
         *
         * @param int $address_id
         * @return string html
         */
        private function getRelationshipsHtml( $product_id, $relation_type='relates' )
        {
        	$input = JFactory::getApplication()->input;
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
            	$user = JFactory::getUser();
            	$config = Citruscart::getInstance();
        		$show_tax = $config->get('display_prices_with_tax');
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

                    //get the right price base on the $filter_group
        			$filter_group = CitruscartHelperUser::getUserGroup($user->id, $item->product_id);
        			$priceObj = CitruscartHelperProduct::getPrice($item->product_id, '1', $filter_group);
					$item->product_price = $priceObj->product_price;

                    $itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->product( $item->product_id, $filter_category, true );
                    $item->itemid = $input->getInt('Itemid', $itemid);
                    $item->tax = 0;
                    $item->showtax = $show_tax;
                	if ($show_tax)
		        	{
			            Citruscart::load('CitruscartHelperUser', 'helpers.user');
			            $geozones = CitruscartHelperUser::getGeoZones( $user->id );
			            if (empty($geozones))
			            {
			                // use the default
			                $table = JTable::getInstance('Geozones', 'CitruscartTable');
			                $table->load(array('geozone_id'=>$config->get('default_tax_geozone')));
			                $geozones = array( $table );
			            }

			            $taxtotal = CitruscartHelperProduct::getTaxTotal($item->product_id, $geozones);
			            $tax = $taxtotal->tax_total;
			            $item->taxtotal = $taxtotal;
			            $item->tax = $tax;
		        	}
                }
            }

            if (!empty($items))
            {
            	$vars = new JObject();
                $vars->items = $items;
                $vars->product_id = $product_id;
                $vars->filter_category = $filter_category;
                $vars->validation =  $validation;

                ob_start();
                echo $this->_getLayout( $layout, $vars );
                $html = ob_get_contents();
                ob_end_clean();
            }

            return $html;
        }

    	/**
    	 * Gets a product's files list
    	 * formatted for display
    	 *
    	 * @param int $address_id
    	 * @return string html
    	 */
    	private function getFiles( $product_id )
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

                $vars = new JObject();

    			$vars->downloadItems = $filtered_items[0];
    			$vars->nondownloadItems = $filtered_items[1];
    			$vars->product_id = $product_id;

    			ob_start();
    			$this->_getLayout( 'product_files', $vars );
    			$html = ob_get_contents();
    			ob_end_clean();
    		}

    		return $html;
    	}

    	/**
         * Gets a product's add to cart section
         * formatted for display
         *
         * @param int $address_id
         * @return string html
         */
        private function getAddToCart( $product_id, $values=array(), $params = array() )
        {
        	$input=JFactory::getApplication()->input;
            $html = '';

            Citruscart::load('CitruscartModelProducts', 'models.products');
            $model  = JModelLegacy::getInstance('Products', 'CitruscartModel');

            $user = JFactory::getUser();
            Citruscart::load('CitruscartHelperUser', 'helpers.user');
        	$filter_group = CitruscartHelperUser::getUserGroup($user->id, $product_id);

        	$model->setState('filter_group', $filter_group);
            $model->setId( $product_id );
            $row = $model->getItem(false);

            $vars = new JObject();

            if ($row->product_notforsale || Citruscart::getInstance()->get('shop_enabled') == '0')
            {
                return $html;
            }

           	$vars->item = $row ;
            $vars->product_id = $product_id;
            $vars->values = $values;
            $vars->validation = "index.php?option=com_citruscart&view=products&task=validate&format=raw";
            $vars->params = $params;

            $config = Citruscart::getInstance();
            $show_tax = $config->get('display_prices_with_tax');
            $vars->show_tax = $show_tax ;
            $vars->tax = 0 ;
            $vars->taxtotal = '';
            $vars->shipping_cost_link = '';

            if ($show_tax)
            {
                // finish CitruscartHelperUser::getGeoZone -- that's why this isn't working
                Citruscart::load('CitruscartHelperUser', 'helpers.user');
                $geozones = CitruscartHelperUser::getGeoZones( JFactory::getUser()->id );
                if (empty($geozones))
                {
                    // use the default
                    $table = JTable::getInstance('Geozones', 'CsitruscartTable');
                    $table->load(array('geozone_id'=>Citruscart::getInstance()->get('default_tax_geozone')));
                    $geozones = array( $table );
                }

                $taxtotal = CitruscartHelperProduct::getTaxTotal($product_id, $geozones);
                $tax = $taxtotal->tax_total;
                $vars->taxtotal = $taxtotal;
                $vars->tax = $tax ;
            }

            // TODO What about this??
            $show_shipping = $config->get('display_prices_with_shipping');
            if ($show_shipping)
            {
                $article_link = $config->get('article_shipping', '');
                $shipping_cost_link = JRoute::_('index.php?option=com_content&view=article&id='.$article_link);
                $vars->shipping_cost_link = $shipping_cost_link ;
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
                if ($product_qty < 0) { $product_qty = '1'; }

                // using a helper file to determine the product's information related to inventory
                $availableQuantity = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getAvailableQuantity ( $product_id, $attributes_csv );
                if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
                {
                    $invalidQuantity = '1';
                }
            }

            $vars->availableQuantity = $availableQuantity ;
            $vars->invalidQuantity = $invalidQuantity ;

            
						JPluginHelper::importPlugin( 'Citruscart' );

            ob_start();
            JFactory::getApplication()->triggerEvent( 'onDisplayProductAttributeOptions', array( $product_id ) );
            $vars->onDisplayProductAttributeOptions = ob_get_contents();
            ob_end_clean();

            ob_start();
           	echo $this->_getLayout('product_buy', $vars);
            $html = ob_get_contents();
            ob_end_clean();

            return $html;
        }


        public function _getLayout($layout, $vars = false, $plugin = '', $group = 'content')
        {
        	return parent::_getLayout($layout, $vars, $plugin, $group);
        }

    }
}
