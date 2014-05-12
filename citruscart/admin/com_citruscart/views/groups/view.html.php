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

class CitruscartViewGroups extends CitruscartViewBase 
{
	
	/**
     * 
     * @param $tpl
     * @return unknown_type
     */
    function getLayoutVars($tpl=null) 
    {
    	/* Get the application */
        $app = JFactory::getApplication();
    	$layout = $this->getLayout();
        
        switch(strtolower($layout))
        {
        	case "selectusers":
                $this->_default($tpl);
              break;
            case "form":
            	
            	$app->input->set('hidemainmenu', '1');
                //JRequest::setVar('hidemainmenu', '1');
                $this->_form($tpl);
              break;
            case "default":
            default:
            	
                $this->set( 'leftMenu', 'leftmenu_users' );
                $this->_default($tpl);
                
              break;
        }
    }
}
