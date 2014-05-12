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

class CitruscartControllerCurrencies extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'currencies');
        $this->registerTask( 'currency_enabled.enable', 'boolean' );
        $this->registerTask( 'currency_enabled.disable', 'boolean' );
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

        $state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
        $state['filter_name']         = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
        $state['filter_enabled']         = $app->getUserStateFromRequest($ns.'enabled', 'filter_enabled', '', '');
        $state['filter_code']         = $app->getUserStateFromRequest($ns.'code', 'filter_code', '', '');

        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }

}

