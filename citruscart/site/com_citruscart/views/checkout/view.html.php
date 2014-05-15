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

Citruscart::load( 'CitruscartViewBase', 'views._base', array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_citruscart' ) );

class CitruscartViewCheckout extends CitruscartViewBase
{
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function display($tpl=null, $perform = true) 
	{
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "view":
				$this->_form($tpl);
			  	break;
			case "form":
				$this->_form($tpl);
			  	break;
			case "default":
			default:
				$this->_default($tpl);
			  	break;
		}
		parent::display($tpl);		
	}
	
	/**
	 * We could actually get rid of this override entirely 
	 * and just call $items = CitruscartHelperPlugin::getPlugins();
	 * from within the tmpl file  
	 * 
	 */
	function _default($tpl='', $onlyPagination = false)
	{
        parent::_default($tpl);
        
        /* Get the application */
        $app = JFactory::getApplication();
        
        Citruscart::load( 'CitruscartUrl', 'library.url' );
        Citruscart::load( 'CitruscartSelect', 'library.select' );
        Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
			
		$model = $this->getModel();
		
        // form
		$form = array();
		
		//$controller = strtolower( $this->get( '_controller', JRequest::getVar('controller', JRequest::getVar('view') ) ) );
		$controller = strtolower( $this->get( '_controller', $app->input->getString('controller', $app->input->getString('view') ) ) );
		
		//$view = strtolower( $this->get( '_view', JRequest::getVar('view') ) );
		$view = strtolower( $this->get( '_view', $app->input->getString('view') ) );

		$task = strtolower( $this->get( '_task', 'edit' ) );
		$form['action'] = $this->get( '_action', "index.php?option=com_citruscart&view={$view}");
		$form['validation'] = $this->get( '_validation', "index.php?option=com_citruscart&controller={$controller}&task=validate&format=raw" );
		$form['validate'] = "<input type='hidden' name='".JSession::getFormToken()."' value='1' />";
		$form['id'] = $model->getId();
		$this->assign( 'form', $form );
	}
	
	/**
	 * Displays the checkout progress
	 * @param int step
	 * @return html the progress layout
	 */
	function displayProgress($step)
	{
		
	}

  /**
   * Loads layour for displaying taxes
   * 
   * @params $tpl Specifies name of layout (null means cart_taxes)
   * 
   * @return Content of a layout with taxes
   */
	function displayTaxes( $tpl = null )
	{
		$tmpl = 'cart_taxes';
		if( $tpl !== null )
			$tmpl = $tpl;
		$this->setLayout( $tmpl );
			
		return $this->loadTemplate( null );
	}

	/**
	 * Generates shipping hash
	 * @param $rate		Array with a shipping rate which is actually set
	 * 
	 * @return	Shipping hash as a string
	 */
	function generateHash( $rate )
	{
		Citruscart::load( 'CitruscartHelperShipping', 'helpers.shipping' );
		$ship_values = array();
		$ship_values['type'] = $rate->shipping_type;
		$ship_values['name'] = $rate->shipping_name;
		$ship_values['price'] = $rate->shipping_price;
		$ship_values['tax'] = $rate->shipping_tax;
		$ship_values['code'] = $rate->shipping_code;
		$ship_values['extra'] = $rate->shipping_extra;
		
		return CitruscartHelperShipping::generateShippingHash( $ship_values );
	}
}
