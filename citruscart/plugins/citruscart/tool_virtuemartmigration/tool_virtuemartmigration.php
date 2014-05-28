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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartToolPlugin', 'library.plugins.tool' );

class plgCitruscartTool_VirtueMartMigration extends CitruscartToolPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
    var $_element   = 'tool_virtuemartmigration';

    /**
     * @var $_tablename  string  A required tablename to use when verifying the provided prefix
     */
    var $_tablename = 'vm_product';

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$language = JFactory::getLanguage();
		$language -> load('plg_citruscart_'.$this->_element, JPATH_ADMINISTRATOR, 'en-GB', true);
		$language -> load('plg_citruscart_'.$this->_element, JPATH_ADMINISTRATOR, null, true);
	}

    /**
     * Overriding
     *
     * @param $options
     * @return unknown_type
     */
    function onGetToolView( $row )
    {
        if (!$this->_isMe($row))
        {
            return null;
        }

        // go to a "process suffix" method
        // which will first validate data submitted,
        // and if OK, will return the html?
        $suffix = $this->_getTokenSuffix();
        $html = $this->_processSuffix( $suffix );

        return $html;
    }

    /**
     * Validates the data submitted based on the suffix provided
     *
     * @param $suffix
     * @return html
     */
    function _processSuffix( $suffix='' )
    {
        $html = "";

        switch($suffix)
        {
            case"2":
                if (!$verify = $this->_verifyDB())
                {
                    JError::raiseNotice('_verifyDB', $this->getError());
                    $html .= $this->_renderForm( '1' );
                }
                    else
                {
                    // migrate the data and output the results
                    $html .= $this->_doMigration();
                }
                break;
            case"1":
                if (!$verify = $this->_verifyDB())
                {
                    JError::raiseNotice('_verifyDB', $this->getError());
                    $html .= $this->_renderForm( '1' );
                }
                    else
                {
                    $suffix++;
                    // display a 'connection verified' message
                    // and request confirmation before migrating data
                    $html .= $this->_renderForm( $suffix );
                    $html .= $this->_renderView( $suffix );
                }
                break;
            default:
                $html .= $this->_renderForm( '1' );
                break;
        }

        return $html;
    }

    /**
     * Prepares the 'view' tmpl layout
     *
     * @return unknown_type
     */
    function _renderView( $suffix='' )
    {
        $vars = new JObject();
        $layout = 'view_'.$suffix;
        $html = $this->_getLayout($layout, $vars);

        return $html;
    }

    /**
     * Prepares variables for the form
     *
     * @return unknown_type
     */
    function _renderForm( $suffix='' )
    {
        $vars = new JObject();
        $vars->token = $this->_getToken( $suffix );
        $vars->state = $this->_getState();

        $layout = 'form_'.$suffix;
        $html = $this->_getLayout($layout, $vars);

        return $html;
    }

    /**
     * Gets the appropriate values from the request
     *
     * @return unknown_type
     */
    function _getState()
    {
        $state = new JObject();
        $state->host = '';
        $state->user = '';
        $state->password = '';
        $state->database = '';
        $state->prefix = 'jos_';
        $state->vm_prefix = 'vm_';
        $state->driver = 'mysql';
        $state->port = '3306';
        $state->external_site_url = '';

        foreach ($state->getProperties() as $key => $value)
        {
            $new_value = JRequest::getVar( $key );
            $value_exists = array_key_exists( $key, $_POST );
            if ( $value_exists && !empty($key) )
            {
                $state->$key = $new_value;
            }
        }
        return $state;
    }

    /**
     * Perform the data migration
     *
     * @return html
     */
    function _doMigration()
    {
        $html = "";
        $vars = new JObject();

        // perform the data migration
        // grab all the data and insert it into the Citruscart tables
        // if the host or database names are diff from the joomla one
        $state = $this->_getState();
        $conf = JFactory::getConfig();
        $jHost       = $conf->getValue('config.host');
        $jDatabase   = $conf->getValue('config.db');

        if (($state->database == $jDatabase) && ($state->host == $jHost))
        {
            // then we can do an insert select
            $results = $this->_migrateInternal($state->prefix, $state->vm_prefix);
        }
            else
        {
            // cannot do an insert select
            $results = $this->_migrateExternal($state->prefix, $state->vm_prefix);
        }
        $vars->results = $results;

        $suffix = $this->_getTokenSuffix();
        $suffix++;
        $layout = 'view_'.$suffix;

        $html = $this->_getLayout($layout, $vars);
        return $html;
    }

    /**
     * Do the migration
     * where the target and source db is the same
     *
     * @return array
     */
    function _migrateInternal($prefix = 'jos_', $vm_prefix = 'vm_')
    {
        $queries = array();

        $p = $prefix.$vm_prefix;

        $queries[0]->title = "CATEGORIES";
        $queries[0] = "
            INSERT IGNORE INTO #__citruscart_categories ( category_id, parent_id, category_name, category_description, category_full_image, category_enabled )
            SELECT c.category_id, cx.category_parent_id, c.category_name, c.category_description, category_full_image, IF(c.category_publish = 'Y', 1, 0) AS category_enabled
            FROM {$p}category as c, {$p}category_xref as cx WHERE c.category_id = cx.category_child_id AND c.category_id >1;
        ";

        $queries[1]->title = "PRODUCTS";
        $queries[1] = "
            INSERT IGNORE INTO #__citruscart_products ( product_id, product_sku, product_name, product_weight, product_description, product_width, product_length, product_height, product_full_image, product_enabled )
            SELECT p.product_id, p.product_sku, p.product_name, p.product_weight, p.product_desc, p.product_width, p.product_length, p.product_height, product_full_image, IF(p.product_publish = 'Y', 1, 0) AS product_enabled
            FROM {$p}product as p;
        ";

        $queries[2]->title = "QUANTITIES";
        $queries[2] = "
            INSERT IGNORE INTO #__citruscart_productquantities ( quantity, product_id )
            SELECT p.product_in_stock, p.product_id
            FROM {$p}product as p;
        ";

        $queries[3]->title = "PRICES";
        $queries[3] = "
            INSERT IGNORE INTO #__citruscart_productprices ( product_id, product_price, price_quantity_start, price_quantity_end, group_id )
            SELECT p.product_id, p.product_price, p.price_quantity_start, p.price_quantity_end, ".Citruscart::getInstance()->get('default_user_group', '1')."
            FROM {$p}product_price as p;
        ";

        $queries[4]->title = "PRODUCT CATEGORIES XREF";
        $queries[4] = "
            INSERT IGNORE INTO #__citruscart_productcategoryxref ( category_id, product_id )
            SELECT p.category_id, p.product_id
            FROM {$p}product_category_xref as p WHERE p.category_id > 1;
        "; // ALL categories

        $queries[5]->title = "ORDERS";
        $queries[5] = "
            INSERT IGNORE INTO #__citruscart_orders ( order_id, user_id, order_number, order_total, order_subtotal, order_tax, order_shipping, order_shipping_tax, order_discount, order_currency, customer_note, ip_address )
            SELECT o.order_id, o.user_id, o.order_number, o.order_total, o.order_subtotal, o.order_tax, o.order_shipping, o.order_shipping_tax, o.order_discount, o.order_currency, o.customer_note, o.ip_address
            FROM {$p}orders as o;
        ";

        $queries[6]->title = "ORDER PAYMENTS";
        $queries[6] = "
            INSERT IGNORE INTO #__citruscart_orderpayments ( order_id, orderpayment_type, transaction_details, transaction_id )
            SELECT order_id, order_payment_name, order_payment_log, order_payment_trans_id
            FROM {$p}order_payment;
        ";

        $queries[7]->title = "ORDER ITEMS";
        $queries[7] = "
            INSERT IGNORE INTO #__citruscart_orderitems ( order_id, product_id, orderitem_attributes, orderitem_sku, orderitem_name, orderitem_quantity, orderitem_price, orderitem_final_price )
            SELECT order_id, product_id, product_attribute, order_item_sku, order_item_name, product_quantity, product_item_price, product_final_price
            FROM {$p}order_item;
        ";

        $queries[8]->title = "ORDER INFO";
        $queries[8] = "
            INSERT IGNORE INTO #__citruscart_orderinfo ( order_id, billing_company, billing_last_name, billing_first_name, billing_middle_name, billing_phone_1, billing_phone_2, billing_fax, billing_address_1, billing_address_2, billing_city, billing_zone_name, billing_country_name, billing_postal_code, user_email, user_id )
            SELECT order_id, company, last_name, first_name, middle_name, phone_1, phone_2, fax, address_1, address_2, city, state, country, zip, user_email, user_id
            FROM {$p}order_user_info WHERE address_type = 'BT';
        ";

        $results = array();
        $db = JFactory::getDBO();
        $n=0;
        foreach ($queries as $query)
        {
            $db->setQuery($query);
            $results[$n]->title = $query->title;
            $results[$n]->query = $db->getQuery();
            $results[$n]->error = '';
            if (!$db->query())
            {
                $results[$n]->error = $db->getErrorMsg();
            }
            $results[$n]->affectedRows = $db->getAffectedRows();
            $n++;
        }

        // Rebuild Categories tree
        Citruscart::load('CitruscartModelCategories', 'models.categories');
        Citruscart::load('CitruscartTableCategories', 'tables.categories');
        JModelLegacy::getInstance('Categories', 'CitruscartModel')->getTable()->updateParents();
		JModelLegacy::getInstance('Categories', 'CitruscartModel')->getTable()->rebuildTreeOrdering();

        $this->_migrateImages($prefix, $vm_prefix, $results);

        return $results;
    }


    private function _migrateImages($prefix = 'jos_', $vm_prefix = 'vm_', &$results, $internal = true)
    {
    	$p = $prefix.$vm_prefix;

    	// Fetch the VM full image
    	if($internal)
    		$db = JFactory::getDBO();
    	else
    		$db = $this->_verifyDB();

    	$query = "SELECT product_id as id, product_full_image as image FROM {$p}product";
    	$db->setQuery($query);
    	$products = $db->loadAssocList();

    	Citruscart::load('CitruscartImage', 'library.image');

    	if($internal)
    		$vm_image_path = JPATH_SITE."/components/com_virtuemart/shop_image/product/";
    	else
    	{
    		$state = $this->_getState();
    		$url = $state->external_site_url;
    		$vm_image_path = $url."/components/com_virtuemart/shop_image/product/";
    	}



    	$n = count($results);

    	$results[$n]->title = 'Product Images';
        $results[$n]->query = 'Copy Product Images & Resize';
        $results[$n]->error = '';
        $results[$n]->affectedRows = 0;

    	foreach( $products as $result )
    	{
    		$check = false;
    		if($internal)
    		{
    			$check = JFile::exists($vm_image_path.$result['image']);
    		}
    		else
    		{
    			$check = $this->url_exists($vm_image_path) && $result['image'];
    		}

    		if($check)
    		{
    			if($internal)
    			{
	    			$img = new CitruscartImage($vm_image_path.$result['image']);
    			}
    			else
    			{
    				$tmp_path = JFactory::getApplication()->getCfg('tmp_path');
    				$file = fopen($vm_image_path.$result['image'], 'r');
    				$file_content = stream_get_contents($file);
    				fclose($file);

    				$file = fopen($tmp_path.DIRECTORY_SEPARATOR.$result['image'], 'w');

    				fwrite($file, $file_content);

    				fclose($file);

    				$img = new CitruscartImage($tmp_path.DS.$result['image']);
    			}

	    		Citruscart::load( 'CitruscartTableProducts', 'tables.products' );
	        	$product = JTable::getInstance( 'Products', 'CitruscartTable' );

	    		$product->load($result['id']);
	    		$path = $product->getImagePath();
	    		$type = $img->getExtension();

	            $img->load();
				$name = $img->getPhysicalName();
	    		// Save full Image
	    		if(!$img->save($path.$name))
	    		{
	    			$results[$n]->error .= '::Could not Save Product Image- From: '.$vm_image_path.$result['image'].' To: '.$path.$result['image'];
	    		}
				$img->setDirectory($path);

	    		// Save Thumb
	    		Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
				$imgHelper = CitruscartHelperBase::getInstance('Image', 'CitruscartHelper');
				if (!$imgHelper->resizeImage( $img, 'product'))
				{
					$results[$n]->error .= '::Could not Save Product Thumb';
				}

				// Save correct image naming
				$product->product_full_image = $name;
				$product->save();

				$results[$n]->affectedRows++;

	    	}
    	}

    	$n++;

    	// CATEGORIES

    	// Fetch the VM full image
    	$query = "SELECT category_id as id, category_full_image as image FROM {$p}category";
    	$db->setQuery($query);
    	$products = $db->loadAssocList();

    	Citruscart::load('CitruscartImage', 'library.image');

   		if($internal)
    		$vm_image_path = JPATH_SITE."/components/com_virtuemart/shop_image/category/";
    	else
    	{
    		$state = $this->_getState();
    		$url = $state->external_site_url;
    		$vm_image_path = $url."/components/com_virtuemart/shop_image/category/";
    	}

    	$results[$n]->title = 'Category Images';
        $results[$n]->query = 'Copy Category Images & Resize';
        $results[$n]->error = '';
        $results[$n]->affectedRows = 0;

    	foreach( $products as $result )
    	{
    		$check = false;
    		if($internal)
    		{
    			$check = JFile::exists($vm_image_path.$result['image']);
    		}
    		else
    		{
    			$check = $this->url_exists($vm_image_path) && $result['image'];
    		}

    		if($check)
    		{
    			if($internal)
    			{
	    			$img = new CitruscartImage($vm_image_path.$result['image']);
    			}
    			else
    			{
    				$tmp_path = JFactory::getApplication()->getCfg('tmp_path');
    				$file = fopen($vm_image_path.$result['image'], 'r');
    				$file_content = stream_get_contents($file);
    				fclose($file);

    				$file = fopen($tmp_path.DS.$result['image'], 'w');

    				fwrite($file, $file_content);

    				fclose($file);

    				$img = new CitruscartImage($tmp_path.DS.$result['image']);
    			}

	            $img->load();

				$path = Citruscart::getPath('categories_images').DS;
				$name = $img->getPhysicalName();
	    		// Save full Image
	    		if(!$img->save($path.$name))
	    		{
	    			$results[$n]->error .= '::Could not Save Category Image - From: '.$vm_image_path.$result['image'].' To: '.$path.$result['image'];
	    		}
				$img->setDirectory($path);

	    		// Save Thumb
	    		Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
				$imgHelper = CitruscartHelperBase::getInstance('Image', 'CitruscartHelper');
				if (!$imgHelper->resizeImage( $img, 'category'))
				{
					$results[$n]->error .= '::Could not Save Category Thumb';
				}

				// Save correct image name
				Citruscart::load( 'CitruscartTableCategories', 'tables.categories' );
	        	$category = JTable::getInstance( 'Categories', 'CitruscartTable' );

	    		$category->load($result['id']);
				$category->category_full_image = $name;
				$category->save();

				$results[$n]->affectedRows++;
	    	}
    	}

    }

    /**
     * Do the migration
     * where the target and source db are not the same
     *
     * @return array
     */
    function _migrateExternal($prefix = 'jos_', $vm_prefix = 'vm_')
    {
        $queries = array();

        $p = $prefix.$vm_prefix;

        // migrate categories
        $queries[0]->title = "CATEGORIES";
        $queries[0]->select = "
            SELECT 99999,c.category_id, cx.category_parent_id, c.category_name, c.category_description, c.category_full_image, IF(c.category_publish = 'Y', 1, 0) AS category_enabled
            FROM {$p}category as c, {$p}category_xref as cx WHERE c.category_id = cx.category_child_id;
        ";
        $queries[0]->insert = "
            INSERT IGNORE INTO #__citruscart_categories ( category_id, parent_id, category_name, category_description, category_full_image, category_enabled )
            VALUES ( %s )
        ";

        // migrate category with id = 1
        $queries[0]->title = "CATEGORY 1";
        $queries[0]->select = "
            SELECT cx.category_parent_id, c.category_name, c.category_description, c.category_full_image, IF(c.category_publish = 'Y', 1, 0) AS category_enabled
            FROM {$p}category as c, {$p}category_xref as cx WHERE c.category_id = cx.category_child_id AND c.category_id = 1;
        ";
        $queries[0]->insert = "
            INSERT IGNORE INTO #__citruscart_categories ( parent_id, category_name, category_description, category_full_image, category_enabled )
            VALUES ( %s )
        ";

        // migrate products
        $queries[1]->title = "PRODUCTS";
        $queries[1]->select = "
            SELECT p.product_id, p.product_sku, p.product_name, p.product_weight, p.product_desc, p.product_width, p.product_length, p.product_height, p.product_full_image, IF(p.product_publish = 'Y', 1, 0) AS product_enabled
            FROM {$p}product as p;
        ";
        $queries[1]->insert = "
            INSERT IGNORE INTO #__citruscart_products ( product_id, product_sku, product_name, product_weight, product_description, product_width, product_length, product_height, product_full_image, product_enabled )
            VALUES ( %s )
        ";

        // migrate product quantities
        $queries[2]->title = "QUANTITIES";
        $queries[2]->select = "
            SELECT p.product_in_stock, p.product_id
            FROM {$p}product as p;
        ";
        $queries[2]->insert = "
            INSERT IGNORE INTO #__citruscart_productquantities ( quantity, product_id )
            VALUES ( %s )
        ";

        // migrate product prices
        $queries[3]->title = "PRICES";
        $queries[3]->select = "
            SELECT p.product_id, p.product_price, p.price_quantity_start, p.price_quantity_end, ".Citruscart::getInstance()->get('default_user_group', '1')."
            FROM {$p}product_price as p;
        ";
        $queries[3]->insert = "
            INSERT IGNORE INTO #__citruscart_productprices ( product_id, product_price, price_quantity_start, price_quantity_end, group_id )
            VALUES ( %s )
        ";

        // migrate product categories xref
		$queries[4]->title = "PRODUCT CATEGORIES XREF";
        $queries[4]->select = "
            SELECT p.category_id, p.product_id
            FROM {$p}product_category_xref as p;
        ";
        $queries[4]->insert = "
            INSERT IGNORE INTO #__citruscart_productcategoryxref ( category_id, product_id )
            VALUES ( %s )
        ";

		// migrate product categories xref with id = 1
		$queries[4]->title = "PRODUCT CATEGORIES XREF 1";
        $queries[4]->select = "
            SELECT 99999, p.product_id
            FROM {$p}product_category_xref as p WHERE p.category_id = 0;
        ";
        $queries[4]->insert = "
            INSERT IGNORE INTO #__citruscart_productcategoryxref ( category_id, product_id )
            VALUES ( %s )
        ";

        $queries[5]->title = "ORDERS";
        $queries[5]->select = "
            SELECT o.order_id, o.user_id, o.order_number, o.order_total, o.order_subtotal, o.order_tax, o.order_shipping, o.order_shipping_tax, o.order_discount, o.order_currency, o.customer_note, o.ip_address
            FROM {$p}orders as o;
        ";
        $queries[5]->insert = "
            INSERT IGNORE INTO #__citruscart_orders ( order_id, user_id, order_number, order_total, order_subtotal, order_tax, order_shipping, order_shipping_tax, order_discount, order_currency, customer_note, ip_address )
        ";

        $queries[6]->title = "ORDER PAYMENTS";
        $queries[6]->select = "
            SELECT order_id, order_payment_name, order_payment_log, order_payment_trans_id
            FROM {$p}order_payment;
        ";
        $queries[6]->insert = "
            INSERT IGNORE INTO #__citruscart_orderpayments ( order_id, orderpayment_type, transaction_details, transaction_id )
        ";

        $queries[7]->title = "ORDER ITEMS";
        $queries[7]->select = "
            SELECT order_id, product_id, product_attribute, order_item_sku, order_item_name, product_quantity, product_item_price, product_final_price
            FROM {$p}order_item;
        ";
        $queries[7]->insert = "
            INSERT IGNORE INTO #__citruscart_orderitems ( order_id, product_id, orderitem_attributes, orderitem_sku, orderitem_name, orderitem_quantity, orderitem_price, orderitem_final_price )
        ";

        $queries[8]->title = "ORDER INFO";
        $queries[8]->select = "
            SELECT order_id, company, last_name, first_name, middle_name, phone_1, phone_2, fax, address_1, address_2, city, state, country, zip, user_email, user_id
            FROM {$p}order_user_info WHERE address_type = 'BT';
        ";
        $queries[8]->insert = "
            INSERT IGNORE INTO #__citruscart_orderinfo ( order_id, billing_company, billing_last_name, billing_first_name, billing_middle_name, billing_phone_1, billing_phone_2, billing_fax, billing_address_1, billing_address_2, billing_city, billing_zone_name, billing_country_name, billing_postal_code, user_email, user_id )
        ";


        $results = array();
        $jDBO = JFactory::getDBO();
        $sourceDB = $this->_verifyDB();
        $n=0;
        foreach ($queries as $query)
        {
            $errors = array();
            $sourceDB->setQuery($query->select);

            if ($rows = $sourceDB->loadObjectList())
            {
                foreach ($rows as $row)
                {
                    $values = array();
                    foreach (get_object_vars($row) as $key => $value)
                    {
                        $values[] = $jDBO->Quote( $value );
                    }
                    $string = implode( ",", $values );
                    $insert_query = sprintf( $query->insert, $string );

                    $jDBO->setQuery( $insert_query );
                    if (!$jDBO->query())
                    {
                        $errors[] = $jDBO->getErrorMsg();
                    }
                }
            }
            $results[$n]->title = $query->title;
            $results[$n]->query = $query->insert;
            $results[$n]->error = implode('\n', $errors);
            $results[$n]->affectedRows = count( $rows );
            $n++;
        }

		// Rebuild categories tree
		Citruscart::load('CitruscartModelCategories', 'models.categories');
        Citruscart::load('CitruscartTableCategories', 'tables.categories');
        JModelLegacy::getInstance('Categories', 'CitruscartModel')->getTable()->updateParents();
		JModelLegacy::getInstance('Categories', 'CitruscartModel')->getTable()->rebuildTreeOrdering();

        $this->_migrateImages($prefix, $vm_prefix, $results, false);

        return $results;
    }


	private function url_exists($url){
        $url = str_replace("http://", "", $url);
        if (strstr($url, "/")) {
            $url = explode("/", $url, 2);
            $url[1] = "/".$url[1];
        } else {
            $url = array($url, "/");
        }

        $fh = fsockopen($url[0], 80);
        if ($fh) {
            fputs($fh,"GET ".$url[1]." HTTP/1.1\nHost:".$url[0]."\n\n");
            if (fread($fh, 22) == "HTTP/1.1 404 Not Found") { return FALSE; }
            else { return TRUE;    }

        } else { return FALSE;}
    }

}
