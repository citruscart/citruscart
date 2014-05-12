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

class CitruscartControllerOrderItems extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->set('suffix', 'orderitems');
	}

	/**
	 * Sets the model's state
	 *
	 * @return array()
	 */
    function _setModelState()
    {
    	$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
    	$ns = $this->getNamespace();

        $state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'o.created_date', 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word');
        $state['filter_orderid']       = $app->getUserStateFromRequest($ns.'filter_orderid', 'filter_orderid', '', '');
        $state['filter_type']       = $app->getUserStateFromRequest($ns.'filter_type', 'filter_type', '', '');
        $state['filter_transaction']    = $app->getUserStateFromRequest($ns.'filter_transaction', 'filter_transaction', '', '');
        $state['filter_product_name']         = $app->getUserStateFromRequest($ns.'filter_product_name', 'filter_product_name', '', '');
        $state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
        $state['filter_date_from'] = $app->getUserStateFromRequest($ns.'date_from', 'filter_date_from', '', '');
        $state['filter_date_to'] = $app->getUserStateFromRequest($ns.'date_to', 'filter_date_to', '', '');
        $state['filter_datetype']   = 'created';
        $state['filter_total_from']    = $app->getUserStateFromRequest($ns.'filter_total_from', 'filter_total_from', '', '');
        $state['filter_total_to']      = $app->getUserStateFromRequest($ns.'filter_total_to', 'filter_total_to', '', '');
        $state['filter_paymentstatus']      = $app->getUserStateFromRequest($ns.'filter_paymentstatus', 'filter_paymentstatus', '', '');

    	foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
  		return $state;
    }
}

?>