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

Citruscart::load( 'CitruscartViewBase', 'views._base', array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_citruscart' ) );

class CitruscartViewSubscriptions extends CitruscartViewBase
{
    function getLayoutVars($tpl=null)
    {
        $layout = $this->getLayout();
        switch(strtolower($layout))
        {
        	case "print":
            case "view":
                $this->_form($tpl);
              break;
            case "form":
            	
            	/* Get the application */
                $app = JFactory::getApplication();
            	$app->input->get('hidemainmenu', '1');
                //JRequest::setVar('hidemainmenu', '1');
                
            	$this->_form($tpl);
              break;
            case "default":
            default:
                $this->set( 'leftMenu', 'COM_CITRUSCART_LEFTMENU_ORDERS' );
                $this->_default($tpl);
              break;
        }
    }

    /**
     * (non-PHPdoc)
     * @see Citruscart/admin/views/CitruscartViewBase#_viewToolbar($isNew)
     */
    function _viewToolbar( $isNew=null )
    {
        JToolBarHelper::custom( 'edit', 'edit', 'edit', JText::_('COM_CITRUSCART_EDIT'), false);
        JToolBarHelper::divider();
        parent::_viewToolbar($isNew);
    }
}
