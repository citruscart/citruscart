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

Citruscart::load( 'CitruscartViewBase', 'views._base' );

class CitruscartViewOrderItems extends CitruscartViewBase
{
    /**
     *
     * @param $tpl
     * @return unknown_type
     */
    function getLayoutVars($tpl=null)
    {
    	$app = JFactory::getApplication();
        $layout = $this->getLayout();
        switch(strtolower($layout))
        {
        	case "print":
            case "view":
                $this->_form($tpl);
              break;
            case "form":
                $app->input->set('hidemainmenu', '1');
                $this->_form($tpl);
              break;
            case "default":
            default:
                $this->set( 'leftMenu', 'leftmenu_orders' );
                $this->_default($tpl);
              break;
        }
    }
    function _form($tpl=null){

    	parent::_form($tpl);

    	$app = JFactory::getApplication();
    	$model = $this->getModel();

    	$table_name = $app->input->getString('view');

    	$this->row = $model->getItem();

    	if (empty($this->row->orderitem_id))
    	{
    		// this is a new product
    		$item = JTable::getInstance($table_name, 'CitruscartTable');
    		$this->assign( 'row', $item );
    	}


    }
}
