<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base' );

class CitruscartViewCoupons extends CitruscartViewBase
{
    /**
     *
     * @param $tpl
     * @return unknown_type
     */
    function getLayoutVars($tpl=null)
    {
        $layout = $this->getLayout();

        /* Get the application */
        $app = JFactory::getApplication();
        $this->renderSubmenu();

        switch(strtolower($layout))
        {
	        case "selectproducts":
	        	$app->input->set('hidemainmenu', '1');
	        	//JRequest::setVar('hidemainmenu', '1');
        		$this->_default($tpl);
        		break;
            case "form":
            	$app->input->set('hidemainmenu', '1');
            	//JRequest::setVar('hidemainmenu', '1');
                $this->_form($tpl);
              break;
            case "default":
            default:
                $this->set( 'leftMenu', 'leftmenu_coupons' );
                $this->_default($tpl);
              break;
        }
    }

    function _form($tpl=null){

    	$model = $this->getModel();
    	$item = $model->getItem();
    	$this->row=$item;
    	if(empty($this->row->coupon_id)){
    		$item = JTable::getInstance('Coupons', 'CitruscartTable');
    		//$state->coupon_params = new DSCParameter($state->coupon_params);
    		//print_r($state->coupon_params);
    		$this->assign('row', $item);

    	}
    	parent::_form($tpl);
    }

	/**
	 * (non-PHPdoc)
	 * @see Citruscart/admin/views/CitruscartViewBase#_defaultToolbar()
	 */
	function _defaultToolbar()
	{
		JToolBarHelper::publishList( 'coupon_enabled.enable' );
		JToolBarHelper::unpublishList( 'coupon_enabled.disable' );
		JToolBarHelper::divider();
		parent::_defaultToolbar();
	}
}
