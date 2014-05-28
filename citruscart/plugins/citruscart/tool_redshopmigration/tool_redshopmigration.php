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

Citruscart::load('CitruscartToolPlugin', 'library.plugins.tool');

class plgCitruscartTool_RedShopMigration extends CitruscartToolPlugin {
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
	var $_element = 'tool_redshopmigration';

	/**
	 * @var $_tablename  string  A required tablename to use when verifying the provided prefix
	 */
	var $_tablename = 'redshop_product';

	function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
		$language = JFactory::getLanguage();
		$language -> load('plg_citruscart_' . $this -> _element, JPATH_ADMINISTRATOR, 'en-GB', true);
		$language -> load('plg_citruscart_' . $this -> _element, JPATH_ADMINISTRATOR, null, true);
	}

	/**
	 * Overriding
	 *
	 * @param $options
	 * @return unknown_type
	 */
	function onGetToolView($row) {
		if (!$this -> _isMe($row)) {
			return null;
		}

		// go to a "process suffix" method
		// which will first validate data submitted,
		// and if OK, will return the html?
		$suffix = $this -> _getTokenSuffix();
		$html = $this -> _processSuffix($suffix);

		return $html;
	}

	/**
	 * Validates the data submitted based on the suffix provided
	 *
	 * @param $suffix
	 * @return html
	 */
	function _processSuffix($suffix = '') {
		$html = "";

		switch($suffix) {
			case"2" :
				if (!$verify = $this -> _verifyDB()) {
					JError::raiseNotice('_verifyDB', $this -> getError());
					$html .= $this -> _renderForm('1');
				} else {
					// migrate the data and output the results
					$html .= $this -> _doMigration();
				}
				break;
			case"1" :
				if (!$verify = $this -> _verifyDB()) {
					JError::raiseNotice('_verifyDB', $this -> getError());
					$html .= $this -> _renderForm('1');
				} else {
					$suffix++;
					// display a 'connection verified' message
					// and request confirmation before migrating data
					$html .= $this -> _renderForm($suffix);
					$html .= $this -> _renderView($suffix);
				}
				break;
			default :
				$html .= $this -> _renderForm('1');
				break;
		}

		return $html;
	}

	/**
	 * Prepares the 'view' tmpl layout
	 *
	 * @return unknown_type
	 */
	function _renderView($suffix = '') {
		$vars = new JObject();
		$layout = 'view_' . $suffix;
		$html = $this -> _getLayout($layout, $vars);

		return $html;
	}

	/**
	 * Prepares variables for the form
	 *
	 * @return unknown_type
	 */
	function _renderForm($suffix = '') {
		$vars = new JObject();
		$vars -> token = $this -> _getToken($suffix);
		$vars -> state = $this -> _getState();

		$layout = 'form_' . $suffix;
		$html = $this -> _getLayout($layout, $vars);

		return $html;
	}

	/**
	 * Gets the appropriate values from the request
	 *
	 * @return unknown_type
	 */
	function _getState() {
		$state = new JObject();
		$state -> host = '';
		$state -> user = '';
		$state -> password = '';
		$state -> database = '';
		$state -> prefix = 'jos_';
		$state -> redshop_prefix = 'redshop_';
		$state -> driver = 'mysql';
		$state -> port = '3306';
		$state -> external_site_url = '';

		foreach ($state->getProperties() as $key => $value) {
			$new_value = JRequest::getVar($key);
			$value_exists = array_key_exists($key, $_POST);
			if ($value_exists && !empty($key)) {
				$state -> $key = $new_value;
			}
		}
		return $state;
	}

	/**
	 * Perform the data migration
	 *
	 * @return html
	 */
	function _doMigration() {
		$html = "";
		$vars = new JObject();

		// perform the data migration
		// grab all the data and insert it into the Citruscart tables
		// if the host or database names are diff from the joomla one
		$state = $this -> _getState();
		$conf = JFactory::getConfig();
		$jHost = $conf -> getValue('config.host');
		$jDatabase = $conf -> getValue('config.db');

		if (($state -> database != $jDatabase) && ($state -> host != $jHost)) {
			// then we can do an insert select
			$results = $this -> _migrateInternal($state -> prefix, $state -> redshop_prefix);
		} else {
			// cannot do an insert select
			$results = $this -> _migrateExternal($state -> prefix, $state -> redshop_prefix);
		}
		$vars -> results = $results;

		$suffix = $this -> _getTokenSuffix();
		$suffix++;
		$layout = 'view_' . $suffix;

		$html = $this -> _getLayout($layout, $vars);
		return $html;
	}

	/**
	 * Do the migration
	 * where the target and source db is the same
	 *
	 * @return array
	 */
	function _migrateInternal($prefix = 'jos_', $redshop_prefix = 'redshop_') {
		$queries = array();

		$p = $prefix . $redshop_prefix;

		//migrate categories
		$queries[0] -> title = "CATEGORIES";
		$queries[0] = "
            INSERT INTO #__citruscart_categories ( category_id, category_name, category_description, parent_id, ordering, category_enabled, isroot )
			SELECT c.category_id, c.category_name, c.category_description, cx.category_parent_id, c.ordering, c.published, 0 AS Expr1
			FROM {$p}category AS c INNER JOIN {$p}category_xref AS cx ON c.category_id = cx.category_child_id;
        ";

		//migrate manufacturers
		$queries[1] -> title = "MANUFACTURERS";
		$queries[1] = "
            INSERT INTO #__citruscart_manufacturers ( manufacturer_id, manufacturer_name, manufacturer_enabled, created_date, modified_date )
			SELECT m.manufacturer_id, m.manufacturer_name, m.published, Now() AS Expr1, Now() AS Expr2
			FROM {$p}manufacturer AS m;
		";

		//migrate products
		$queries[2] -> title = "PRODUCTS";
		$queries[2] = "
			INSERT IGNORE INTO #__citruscart_products ( product_id, manufacturer_id, product_sku, product_name, product_weight, product_description, product_description_short, product_width, product_length, product_height, product_full_image, product_enabled )
            SELECT p.product_id, p.manufacturer_id, p.product_number, p.product_name, p.weight, p.product_desc, p.product_s_desc, p.product_width, p.product_length, p.product_height, p.product_full_image, p.published
            FROM {$p}product as p;
        ";

		//migrate quantities
		$queries[3] -> title = "QUANTITIES";
		$queries[3] = "
           	INSERT INTO #__citruscart_productquantities ( product_id, quantity )
			SELECT p.product_id, 100 AS Expr1
			FROM {$p}product AS p;
        ";

		//migrate prices
		$queries[4] -> title = "PRICES";
		$queries[4] = "
            INSERT IGNORE INTO #__citruscart_productprices ( product_id, product_price_startdate, product_price, price_quantity_start, price_quantity_end, group_id )
			SELECT p.product_id, Now() AS Expr1, p.product_price, 1 AS Expr2, 10 AS Expr3, 1 AS Expr4
			FROM {$p}product AS p;
        ";

		//migrate product categoies xref
		$queries[5] -> title = "PRODUCT CATEGORIES XREF";
		$queries[5] = "
            INSERT IGNORE INTO #__citruscart_productcategoryxref ( category_id, product_id )
			SELECT cx.category_id, cx.product_id
			FROM {$p}category_xref AS cx;
        ";

		$results = array();
		$db = JFactory::getDBO();
		$n = 0;
		foreach ($queries as $query) {
			$db -> setQuery($query);
			$results[$n] -> title = $query -> title;
			$results[$n] -> query = $db -> getQuery();
			$results[$n] -> error = '';
			if (!$db -> query()) {
				$results[$n] -> error = $db -> getErrorMsg();
			}
			$results[$n] -> affectedRows = $db -> getAffectedRows();
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
	function _migrateExternal($prefix = 'jos_', $redshop_prefix = 'redshop_') {
		$queries = array();

		$p = $prefix . $redshop_prefix;

		// migrate categories
		$queries[0] -> title = "CATEGORIES";
		$queries[0] -> select = "
           	SELECT c.category_id, c.category_name, c.category_description, cx.category_parent_id, c.ordering, c.published, 0 AS Expr1
			FROM {$p}category AS c INNER JOIN {$p}category_xref AS cx ON c.category_id = cx.category_child_id;
        ";
		$queries[0] -> insert = "
            INSERT IGNORE INTO #__citruscart_categories ( category_id, category_name, category_description, parent_id, ordering, category_enabled, isroot )
            VALUES ( %s )
        ";

		// migrate manufacturers
		$queries[1] -> title = "MANUFACTURERS";
		$queries[1] -> select = "
            SELECT m.manufacturer_id, m.manufacturer_name, m.published, Now() AS Expr1, Now() AS Expr2
			FROM {$p}manufacturer AS m;
        ";
		$queries[1] -> insert = "
            INSERT IGNORE INTO #__citruscart_manufacturers ( manufacturer_id, manufacturer_name, manufacturer_enabled, created_date, modified_date )
        	VALUES ( %s )
        ";

		// migrate products
		$queries[2] -> title = "PRODUCTS";
		$queries[2] -> select = "
            SELECT p.product_id, p.manufacturer_id, p.product_number, p.product_name, p.weight, p.product_desc, p.product_s_desc, p.product_width, p.product_length, p.product_height, p.product_full_image, p.published
            FROM {$p}product as p;
        ";
		$queries[2] -> insert = "
            INSERT IGNORE INTO #__citruscart_products ( product_id, manufacturer_id, product_sku, product_name, product_weight, product_description, product_description_short, product_width, product_length, product_height, product_full_image, product_enabled )
            VALUES ( %s )
        ";

		// migrate product quantities
		$queries[3] -> title = "QUANTITIES";
		$queries[3] -> select = "
           	SELECT p.product_id, 100 AS Expr1
			FROM {$p}product AS p;
        ";
		$queries[3] -> insert = "
            INSERT IGNORE INTO #__citruscart_productquantities ( product_id, quantity )
            VALUES ( %s )
        ";

		// migrate product prices
		$queries[4] -> title = "PRICES";
		$queries[4] -> select = "
            SELECT p.product_id, Now() AS Expr1, p.product_price, 1 AS Expr2, 10 AS Expr3, 1 AS Expr4
			FROM {$p}product AS p;
        ";
		$queries[4] -> insert = "
			INSERT IGNORE INTO #__citruscart_productprices ( product_id, product_price_startdate, product_price, price_quantity_start, price_quantity_end, group_id )
            VALUES ( %s )
        ";

		// migrate product categories xref
		$queries[5] -> title = "PRODUCT CATEGORIES XREF";
		$queries[5] -> select = "
            SELECT cx.category_id, cx.product_id
			FROM {$p}product_category_xref AS cx;
        ";
		$queries[5] -> insert = "
            INSERT IGNORE INTO #__citruscart_productcategoryxref ( category_id, product_id )
            VALUES ( %s )
        ";

		$results = array();
		$jDBO = JFactory::getDBO();
		$sourceDB = $this -> _verifyDB();
		$n = 0;

		foreach ($queries as $query) {
			$errors = array();
			$sourceDB -> setQuery($query -> select);

			if ($rows = $sourceDB -> loadObjectList()) {
				foreach ($rows as $row) {
					$values = array();
					foreach (get_object_vars($row) as $key => $value) {
						$values[] = $jDBO -> Quote($value);
					}
					$string = implode(",", $values);
					$insert_query = sprintf($query -> insert, $string);

					$jDBO -> setQuery($insert_query);
					if (!$jDBO -> query()) {
						$errors[] = $jDBO -> getErrorMsg();
					}
				}
			}
			$results[$n] -> title = $query -> title;
			$results[$n] -> query = $query -> insert;
			$results[$n] -> error = implode('\n', $errors);
			$results[$n] -> affectedRows = count($rows);
			$n++;
		}

		return $results;
	}

}
