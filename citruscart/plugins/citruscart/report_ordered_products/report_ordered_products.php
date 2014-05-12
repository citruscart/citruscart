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

class plgCitruscartReport_ordered_products extends CitruscartReportPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
    var $_element    = 'report_ordered_products';

    /**
     * @var $default_model  string  Default model used by report
     */
    var $default_model    = 'orderitems';

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
     * Override parent::_getData()
     *
     * @return unknown_type
     */
    function _getData()
    {
        $state = $this->_getState();
		$model = $this->_getModel();

		// filter only complete orders ( 3 - Shipped, 5 - Complete, 17 - Payment Orders )
        $order_states = array ( '3', '5', '17');
        $model->setState( 'filter_orderstates', $order_states );

		$model->setState( 'order', 'total_quantity' );
        $model->setState( 'direction', 'DESC' );

		$query = $model->getQuery();

		// select the quantity
        $field = array();
        $field[] = " SUM(tbl.orderitem_quantity) AS total_quantity ";

        $query->group( 'orderitem_attributes' );
        $query->group( 'product_id' );

        $query->select( $field );
        $model->setQuery( $query );

		$data = $model->getList();

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
        $model = $this->_getModel( $this->get('default_model') );
        $ns = $this->_getNamespace();

        $state = parent::_getState(); // get the basic state values from the parent method

        // then add your own custom ones just for this report
        // $state['filter_name'] = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
        //$state['filter_product_id'] = $app->getUserStateFromRequest($ns.'product_id', 'filter_product_id', '', '');
        $state['filter_product_name'] = $app->getUserStateFromRequest($ns.'product_name', 'filter_product_name', '', '');
        $state['filter_manufacturer_id'] = $app->getUserStateFromRequest($ns.'manufacturer_id', 'filter_manufacturer_id', '', '');

        // then apply the states to the model
        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }
}
