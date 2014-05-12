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

class CitruscartViewTools extends CitruscartViewBase 
{
    function getLayoutVars($tpl=null) 
    {
        $layout = $this->getLayout();
        
        /* Get the application */
        $app = JFactory::getApplication();
        switch(strtolower($layout))
        {
            case "form":
                $app->input->set('hidemainmenu', '1');
                $this->_form($tpl);
              break;
            case "view":
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
        parent::_form($tpl);
        
        // load the plugin
		$row = $this->getModel()->getItem();
		$import = JPluginHelper::importPlugin( 'Citruscart', $row->element );
	}
	
	function _defaultToolbar()
	{
	}
	
    function _viewToolbar( $isNew = null )
    {
    	JToolBarHelper::custom( 'view', 'forward', 'forward', 'COM_CITRUSCART_SUBMIT', false );
    	JToolBarHelper::cancel( 'close', 'COM_CITRUSCART_CLOSE' );
    }
}
