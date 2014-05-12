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

class CitruscartViewReports extends CitruscartViewBase 
{
    function getLayoutVars($tpl=null) 
    {
    	/* Get the application */
        $app = JFactory::getApplication();
    	$layout = $this->getLayout();
        switch(strtolower($layout))
        {
        	case "view":
            case "form":
                $app->input->set('hidemainmenu', '1');
            	$this->_form($tpl);
              break;
            case "default":
            default:
                $this->set( 'leftMenu', 'leftmenu_tools' );
                $this->_default($tpl);
              break;
        }
    }
    
	function _form($tpl=null)
	{  
        JHTML::_('script', 'bootstrapped-advanced-ui.js', 'media/citruscart/js/');
        JHTML::_('stylesheet', 'bootstrapped-advanced-ui.css', 'media/citruscart/css/');
        JHTML::_('stylesheet', 'reports.css', 'media/com_citruscart/css/');
        parent::_form($tpl);
        
        // load the plugin
		$row = $this->getModel()->getItem();
		$import = JPluginHelper::importPlugin( 'Citruscart', $row->element );
	}
	
	function _defaultToolbar()
	{
	}
	
	function _viewToolbar($isNew = null)
	{
		JToolBarHelper::custom( 'view', 'forward', 'forward', 'COM_CITRUSCART_SUBMIT', false );
		JToolBarHelper::cancel( 'close', 'COM_CITRUSCART_CLOSE' );
	}
}
