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

class CitruscartControllerSearch extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->set('suffix', 'search');
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

		$state['filter']      = $app->getUserStateFromRequest($ns.'.filter', 'filter', '', 'string');
		$state['filter_id_from'] 	= $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
		$state['filter_id_to'] 		= $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
		$state['filter_name'] 		= $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
		$state['filter_enabled'] 	= 1;
		$state['filter_category'] 	= $app->getUserStateFromRequest($ns.'filter_category', 'filter_category', '', '');
	    $state['filter_stock'] = $app->getUserStateFromRequest($ns.'filter_stock', 'filter_stock', '', '');

		if (!empty($state['filter_stock']))
		{
			$state['filter_quantity_from'] 	= 1;
		}
    		else
		{
			$state['filter_quantity_from'] 	= $app->getUserStateFromRequest($ns.'quantity_from', 'filter_quantity_from', '', '');
		}

		$state['filter_quantity_to'] 		= $app->getUserStateFromRequest($ns.'quantity_to', 'filter_quantity_to', '', '');
		$state['filter_multicategory'] 		= $app->getUserStateFromRequest($ns.'filter_multicategory', 'filter_multicategory', '', '');
		$state['filter_sku'] 		= $app->getUserStateFromRequest($ns.'sku', 'filter_sku', '', '');
		$state['filter_price_from'] 	= $app->getUserStateFromRequest($ns.'price_from', 'filter_price_from', '', '');
		$state['filter_price_to'] 		= $app->getUserStateFromRequest($ns.'price_to', 'filter_price_to', '', '');
		$state['filter_taxclass']   = $app->getUserStateFromRequest($ns.'taxclass', 'filter_taxclass', '', '');
		$state['filter_ships']   = $app->getUserStateFromRequest($ns.'ships', 'filter_ships', '', '');
		$state['filter_manufacturer']   = $app->getUserStateFromRequest($ns.'filter_manufacturer', 'filter_manufacturer', '', '');
		$state['filter_description']   = $app->getUserStateFromRequest($ns.'filter_description', 'filter_description', '', '');

		foreach ($state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

	/**
     * Displays search results
     *
     * (non-PHPdoc)
     * @see Citruscart/admin/CitruscartController#display($cachable)
     */
    function display($cachable = false, $urlparams = false)
    {
    	$input =JFactory::getApplication()->input;
        $input->set( 'view', $this->get('suffix') );
        $view   = $this->getView( $this->get('suffix'), JFactory::getDocument()->getType() );
        $model  = $this->getModel( $this->get('suffix') );
        $this->_setModelState();

        if ($items = $model->getList())
        {
            foreach ($items as $row)
            {
                $row->category_id = 0;
                $categories = Citruscart::getClass( 'CitruscartHelperProduct', 'helpers.product' )->getCategories( $row->product_id );
                if (!empty($categories))
                {
                    $row->category_id = $categories[0];
                }

                $itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->product( $row->product_id, $row->category_id, true );
                $row->itemid = empty($itemid) ? $input->getInt('Itemid') : $itemid;
            }
        }

        parent::display($cachable, $urlparams);
    }

}