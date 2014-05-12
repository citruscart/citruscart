<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base' );
//require_once('_base.php');
class CitruscartViewDashboard extends CitruscartViewBase
{
	function dispaly($tpl=null){

		//$this->_defaultToolbar();
		$this->_renderSubmenu();
		parent::display($tpl);


	}
    /**
     * The default toolbar for a list
     * @return unknown_type
     */
    function _defaultToolbar()
    {

    	/* defined('_JEXEC') or die('Restricted access');
    	$doc = JFactory::getDocument();
    	$doc->addStyleSheet(JUri::root().'/media/citruscart/css/menu.css');
    	//JHTML::_('stylesheet', 'menu.css', 'media/com_citruscart/css/');

    	require_once(JPATH_ADMINISTRATOR.'/components/com_citruscart/helpers/toolbar.php');
    	$toolbar = new CitruscartToolBar();
    	$toolbar->renderLinkbar(); */
    }

    function _renderSubmenu(){

    	$doc = JFactory::getDocument();
    	$doc->addStyleSheet(JUri::root().'/media/citruscart/css/menu.css');
    	//JHTML::_('stylesheet', 'menu.css', 'media/com_citruscart/css/');
    	require_once(JPATH_ADMINISTRATOR.'/components/com_citruscart/helpers/toolbar.php');
    	$toolbar = new CitruscartToolBar();
    	$toolbar->renderLinkbar();

    }

}
