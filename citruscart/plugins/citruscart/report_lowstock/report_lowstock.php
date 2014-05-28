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

Citruscart::load( 'CitruscartReportPlugin', 'library.plugins.report' );

class plgCitruscartReport_lowstock extends CitruscartReportPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
    var $_element    = 'report_lowstock';

    /**
     * @var $default_model  string  Default model used by report
     */
    var $default_model    = 'products';

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
     * Override parent::_getData() to set the direction of the product quantity
     *
     * @return objectlist
     */
    function _getData()
    {
        $state = $this->_getState();
        $model = $this->_getModel();

		//set the direction of the quantity to asc to show the product with lowest quantity
        $model->setState( 'order', 'product_quantity' );
        $model->setState( 'direction', 'ASC' );

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
        $model = $this->_getModel( 'products' );
        $ns = $this->_getNamespace();
        $state = array();
       	$state['filter_name'] = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
        $state['filter_quantity_from'] = $app->getUserStateFromRequest($ns.'quantity_from', 'filter_quantity_from', '', '');
        $state['filter_quantity_to'] = $app->getUserStateFromRequest($ns.'quantity_to', 'filter_quantity_to', '', '');
    	$state['filter_category'] = $app->getUserStateFromRequest($ns.'category', 'filter_category', '', '');
		$state['filter_enabled'] 	= $app->getUserStateFromRequest($ns.'enabled', 'filter_enabled', '', '');


        $state = $this->_handleRangePresets( $state );

        foreach (@$state as $key=>$value)
        {
            $model->setState( $key, $value );
        }

        return $state;

    }
}
