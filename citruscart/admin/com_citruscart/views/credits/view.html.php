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

class CitruscartViewCredits extends CitruscartViewBase
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
        $this->renderSubmenu();
        switch(strtolower($layout))
        {
            case "form":
                $app->input->set('hidemainmenu', '1');
                $this->_form($tpl);
              break;
            case "default":
            default:
                $this->set( 'leftMenu', 'leftmenu_users' );
                $this->_default($tpl);
              break;
        }
    }



    function _form($tpl=null){

    	$model = $this->getModel();
    	$item = $model->getItem();
    	$this->row=$item;

       	if(empty($this->row->credit_id)){
    		$item = JTable::getInstance('Credits', 'CitruscartTable');
    		//$state->coupon_params = new DSCParameter($state->coupon_params);
    		//print_r($state->coupon_params);
    		$this->assign('row', $item);

    	}
    	parent::_form($tpl);
    }



}
