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

Citruscart::load('CitruscartReportPlugin', 'library.plugins.report');

class plgCitruscartReport_sales extends CitruscartReportPlugin {
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
	var $_element = 'report_sales';

	/**
	 * @var $default_model  string  Default model used by report
	 */
	var $default_model = 'orders';

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
		$language = JFactory::getLanguage();
		$language -> load('plg_citruscart_' . $this -> _element, JPATH_ADMINISTRATOR, 'en-GB', true);
		$language -> load('plg_citruscart_' . $this -> _element, JPATH_ADMINISTRATOR, null, true);
	}

	/**
	 * Override parent::_getData() to insert groupBy and orderBy clauses into query
	 *
	 * @return unknown_type
	 */
	function _getData() {
		$state = $this -> _getState();
		$model = $this -> _getModel();

		// filter only complete orders ( 3 - Shipped, 5 - Complete, 17 - Payment Received )
		$order_states = array('3', '5', '17');
		$model -> setState('filter_orderstates', $order_states);

		$query = $model -> getQuery();

		// order results by the total sales
		$query -> order('order_total DESC');

		$model -> setQuery($query);
		$data = $model -> getList();

		return $data;
	}

}
