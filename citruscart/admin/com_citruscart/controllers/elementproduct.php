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
require_once(JPATH_SITE.'/libraries/dioscouri/library/controller/admin.php');
class CitruscartControllerElementProduct extends DSCControllerAdmin
{
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'elementproduct');
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

        $state['filter_state']    = $app->getUserStateFromRequest($ns.'filter_state', 'product_state', '', 'string');
        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }

    function display($cachable=false, $urlparams = false)
    {
    	$app = JFactory::getApplication();
    	$this->hidefooter = true;
        $object = $app->input->get('object');
        $view = $this->getView( $this->get('suffix'), 'html' );
        $view->assign( 'object', $object );
        parent::display();
    }
}

