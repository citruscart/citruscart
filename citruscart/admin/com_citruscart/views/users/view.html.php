<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base' );

class CitruscartViewUsers extends CitruscartViewBase
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

    	$this->renderSubmenu();


        switch(strtolower($layout))
        {
            case "form":
                //JRequest::setVar('hidemainmenu', '1');
            	$app->input->set('hidemainmenu', '1');
            	$this->_form($tpl);
              break;
            case "view":
            	$this->_form($tpl);
            case "default":
            default:
                $this->set( 'leftMenu', 'leftmenu_users' );
                $this->_default($tpl);
              break;
        }
    }

	function _default($tpl=null)
	{
		Citruscart::load( 'CitruscartUrl', 'library.url' );
		parent::_default($tpl);
	}

	function _defaultToolbar()
	{
	}
}
