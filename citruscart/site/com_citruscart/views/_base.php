<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');


require_once(JPATH_SITE.'/libraries/dioscouri/library/view/site.php');
jimport( 'joomla.filter.filteroutput' );
jimport( 'joomla.application.component.view' );

class CitruscartViewBase extends DSCViewSite
{
    function __construct( $config=array() )
    {
        parent::__construct( $config );

        $this->defines = Citruscart::getInstance();

        Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
        $this->router = new CitruscartHelperRoute();

        $this->user = JFactory::getUser();
        $this->session = JFactory::getSession();
    }

	/**
	 * First displays the submenu, then displays the output
	 * but only if a valid _doTask is set in the view object
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function display($tpl=null, $perform = true )
	{
	    // these need to load before jquery to prevent joomla from crying
	    JHTML::_('behavior.modal');
	    JHTML::_('script', 'core.js', 'media/system/js/');

	    DSC::loadJQuery('latest', true, 'citruscartJQ');

	    if ($this->defines->get('use_bootstrap', '0'))
	    {
	    	DSC::loadBootstrap();
	    }

	    JHTML::_('stylesheet', 'common.css', 'media/citruscart/css/');

	    if ($this->defines->get('include_site_css', '0'))
	    {
	        JHTML::_('stylesheet', 'citruscart.css', 'media/citruscart/css/');
	    }

		parent::display($tpl);
	}

	/**
	 * Displays a submenu if there is one and if hidemainmenu is not set
	 * @param $selected
	 * @return unknown_type
	 **/
	function displaySubmenu($selected='')
	{

		if (!JFactory::getApplication()->input->getInt('hidemainmenu') && empty($this->hidemenu))
		{
			$menu = CitruscartMenu::getInstance();
		}
	}

	/**
	 * Basic commands for displaying a list
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function _default($tpl='', $onlyPagination = false )
	{

		Citruscart::load( 'CitruscartSelect', 'library.select' );
		Citruscart::load( 'CitruscartGrid', 'library.grid' );

		if ($onlyPagination || !empty($this->items)) {
		    $this->no_items = true;
		}

		parent::_default($tpl);
	}

	/**
	 * Basic methods for a form
	 * @param $tpl
	 * @return unknown_type
	 **/
	function _form($tpl='')
	{
		$input= JFactory::getApplication()->input;
		Citruscart::load( 'CitruscartSelect', 'library.select' );
		Citruscart::load( 'CitruscartGrid', 'library.grid' );
		$model = $this->getModel();
		if( isset( $this->row ) ) {
			JFilterOutput::objectHTMLSafe( $this->row );
		}
		else
		{
            $row = $model->getItem();
			JFilterOutput::objectHTMLSafe( $row );
			$this->assign('row', $row );
		}

		// form
		$form = array();

		$controller = strtolower( $this->get( '_controller', $input->getString('controller')));
		$view = strtolower( $this->get( '_view', $input->getString('view') ) );
		$task = strtolower( $this->get( '_task', 'edit' ) );
		$form['action'] = $this->get( '_action', "index.php?option=com_citruscart&controller={$controller}&view={$view}&task={$task}&id=".$model->getId() );
		$form['validation'] = $this->get( '_validation', "index.php?option=com_citruscart&controller={$controller}&view={$view}&task=validate&format=raw" );
		$form['validate'] = "<input type='hidden' name='".JSession::getFormToken()."' value='1' />";
		$form['id'] = $model->getId();
		$this->assign( 'form', $form );

		// set the required image
	    // TODO Fix this
		$required = new stdClass();
		$required->text = JText::_('COM_CITRUSCART_REQUIRED');
		$required->image = CitruscartGrid::required( $required->text );
		$this->assign('required', $required );
	}

	/**
	 * Basic commands for displaying a auxiliaty layout
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function _default_light($tpl='')
	{
		Citruscart::load( 'CitruscartSelect', 'library.select' );
		Citruscart::load( 'CitruscartGrid', 'library.grid' );

		$this->no_items = true;
		$this->no_state = true;
		$this->no_pagination = true;

		parent::_default($tpl);
	}
}