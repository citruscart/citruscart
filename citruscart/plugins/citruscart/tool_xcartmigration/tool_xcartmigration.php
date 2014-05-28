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

class plgCitruscartTool_XCartMigration extends CitruscartToolPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
    var $_element   = 'tool_xcartmigration';

    /**
     * @var $_tablename  string  A required tablename to use when verifying the provided prefix
     */
    var $_tablename = 'xcart_products';

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
    	$app = JFactory::getApplication();
        $state = new JObject();
        $state->host = '';
        $state->user = '';
        $state->password = '';
        $state->database = '';
        $state->prefix = '';
        $state->driver = 'mysql';
        $state->port = '3306';

        foreach ($state->getProperties() as $key => $value)
        {
            $new_value = $app->input->get( $key );
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

        if (($state->database != $jDatabase) && ($state->host != $jHost))
        {
            // then we can do an insert select
            $results = $this->_migrateInternal();
        }
            else
        {
            // cannot do an insert select
            $results = $this->_migrateExternal();
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
    function _migrateInternal()
    {
        $queries = array();

        $queries[0]->title = "CATEGORIES";
        $queries[0] = "
            INSERT IGNORE INTO #__citruscart_categories ( category_id, parent_id, category_name, category_description, category_enabled )
            SELECT xcart_categories.categoryid, xcart_categories.parentid, xcart_categories.category, xcart_categories.description, 1 AS Expr1
            FROM xcart_categories;
        ";

        $queries[1]->title = "PRODUCTS";
        $queries[1] = "
            INSERT IGNORE INTO #__citruscart_products ( product_id, product_sku, product_name, product_weight, product_description, product_width, product_length, product_height, product_enabled )
            SELECT xcart_products.productid, xcart_products.productcode, xcart_products.product, xcart_products.weight, xcart_products.descr, xcart_products.width, xcart_products.length, xcart_products.height, 1 AS Expr1
            FROM xcart_products;
        ";

        $queries[2]->title = "QUANTITIES";
        $queries[2] = "
            INSERT IGNORE INTO #__citruscart_productquantities ( quantity, product_id )
            SELECT xcart_products.avail, xcart_products.productid
            FROM xcart_products;
        ";

        $queries[3]->title = "PRICES";
        $queries[3] = "
            INSERT IGNORE INTO #__citruscart_productprices ( product_id, product_price )
            SELECT xcart_pricing.productid, xcart_pricing.price
            FROM xcart_pricing;
        ";

        $queries[4]->title = "PRODUCT CATEGORIES XREF";
        $queries[4] = "
            INSERT IGNORE INTO #__citruscart_productcategoryxref ( category_id, product_id )
            SELECT xcart_products_categories.categoryid, xcart_products_categories.productid
            FROM xcart_products_categories;
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
            $n++;
        }

        return $results;
    }

    /**
     * Do the migration
     * where the target and source db are not the same
     *
     * @return array
     */
    function _migrateExternal()
    {
        $queries = array();

        // migrate categories
        $queries[0]->title = "CATEGORIES";
        $queries[0]->select = "
            SELECT xcart_categories.categoryid, xcart_categories.parentid, xcart_categories.category, xcart_categories.description, 1 AS Expr1
            FROM xcart_categories;
        ";
        $queries[0]->insert = "
            INSERT IGNORE INTO #__citruscart_categories ( category_id, parent_id, category_name, category_description, category_enabled )
            VALUES ( %s )
        ";

        // migrate products
        $queries[1]->title = "PRODUCTS";
        $queries[1]->select = "
            SELECT xcart_products.productid, xcart_products.productcode, xcart_products.product, xcart_products.weight, xcart_products.descr, xcart_products.width, xcart_products.length, xcart_products.height, 1 AS Expr1
            FROM xcart_products;
        ";
        $queries[1]->insert = "
            INSERT IGNORE INTO #__citruscart_products ( product_id, product_sku, product_name, product_weight, product_description, product_width, product_length, product_height, product_enabled )
            VALUES ( %s )
        ";

        // migrate product quantities
        $queries[2]->title = "QUANTITIES";
        $queries[2]->select = "
            SELECT xcart_products.avail, xcart_products.productid
            FROM xcart_products;
        ";
        $queries[2]->insert = "
            INSERT IGNORE INTO #__citruscart_productquantities ( quantity, product_id )
            VALUES ( %s )
        ";

        // migrate product prices
        $queries[3]->title = "PRICES";
        $queries[3]->select = "
            SELECT xcart_pricing.productid, xcart_pricing.price
            FROM xcart_pricing;
        ";
        $queries[3]->insert = "
            INSERT IGNORE INTO #__citruscart_productprices ( product_id, product_price )
            VALUES ( %s )
        ";

        // migrate product categories xref
        $queries[4]->title = "PRODUCT CATEGORIES XREF";
        $queries[4]->select = "
            SELECT xcart_products_categories.categoryid, xcart_products_categories.productid
            FROM xcart_products_categories;
        ";
        $queries[4]->insert = "
            INSERT IGNORE INTO #__citruscart_productcategoryxref ( category_id, product_id )
            VALUES ( %s )
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

        return $results;
    }

}
