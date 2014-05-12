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

Citruscart::load( 'CitruscartReportPlugin', 'library.plugins.report' );

class plgCitruscartReport_abandoned_cart extends CitruscartReportPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
	var $_element    = 'report_abandoned_cart';

	/**
	 * @var $default_model  string  Default model used by report
	 */
	var $default_model    = 'carts';

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
	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$language = JFactory::getLanguage();
		$language -> load('plg_citruscart_'.$this->_element, JPATH_ADMINISTRATOR, 'en-GB', true);
		$language -> load('plg_citruscart_'.$this->_element, JPATH_ADMINISTRATOR, null, true);
	}

	/**
	 * Override parent::_getData() to sort products in users cart
	 *
	 * @return objectlist
	 */
	function _getData()
	{
		$state = $this->_getState();
        $model = $this->_getModel();
        $query = $model->getQuery();
        $query->select( 'u.*' );
		$query->join('LEFT', '#__users AS u ON u.id = tbl.user_id');
		$query->where('tbl.user_id != 0');
				
        $model->setQuery( $query );
        $items = $model->getList();

		$data = array();
		$subtotals	 = array();
		$total_items = array();
		foreach($items as $item)
		{
			if(empty($subtotals[$item->user_id])) $subtotals[$item->user_id] = 0;
			if(empty($total_items[$item->user_id])) $total_items[$item->user_id] = 0;
			$subtotals[$item->user_id] += (float) $item->product_price;
			$total_items[$item->user_id] += (int) $item->product_qty;
		}

		foreach($items as $item)
		{
			$data[$item->user_id] = $item;
			$data[$item->user_id]->subtotal = $subtotals[$item->user_id];
			$data[$item->user_id]->total_items = $total_items[$item->user_id];
		}

        return $data;
	}

	/**
	 * Override parent::_getState() to do the filtering
	 *
	 * @return object
	 */
	function _getState()
	{
		$app = JFactory::getApplication();
		$model = $this->_getModel();
		$ns = $this->_getNamespace();

		$state = parent::_getState(); // get the basic state values from the parent method


		$state['filter_name'] = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
        $state['filter_date_from'] = $app->getUserStateFromRequest($ns.'date_from', 'filter_date_from', '', '');
		$state['filter_date_to'] = $app->getUserStateFromRequest($ns.'date_to', 'filter_date_to', '', '');
        $state = $this->_handleRangePresets( $state );

        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }

        return $state;
    }

}
