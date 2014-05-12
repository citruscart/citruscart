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

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base' );

class CitruscartViewConfig extends CitruscartViewBase 
{
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function getLayoutVars($tpl=null) 
	{
	    $doc = JFactory::getDocument();
	    $saveOnClick = 'CitruscartJQ(document).ready(function(){ Citruscart.saveConfigOnClick(); })';
	    $doc->addScriptDeclaration( $saveOnClick );
	    
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "default":
			default:
			    $this->set( 'leftMenu', 'leftmenu_configuration' );
				$this->_default($tpl);
			  break;
		}
	}
	
	/**
	 * 
	 * @return void
	 **/
	function _default($tpl = null) 
	{
		Citruscart::load( 'CitruscartSelect', 'library.select' );
		Citruscart::load( 'CitruscartGrid', 'library.grid' );
		Citruscart::load( 'CitruscartTools', 'library.tools' );
		
		/* Get the application */
		$app = JFactory::getApplication();

		// check config
			$row = Citruscart::getInstance();
			$this->assign( 'row', $row );
		
		// add toolbar buttons
			JToolBarHelper::apply('save');
			JToolBarHelper::cancel( 'close', 'COM_CITRUSCART_CLOSE' );
			
		// plugins
        	$filtered = array();
	        $items = CitruscartTools::getPlugins();
			for ($i=0; $i<count($items); $i++) 
			{
				$item = $items[$i];
				// Check if they have an event
				if ($hasEvent = CitruscartTools::hasEvent( $item, 'onListConfigCitruscart' )) {
					// add item to filtered array
					$filtered[] = $item;
				}
			}
			$items = $filtered;
			$this->assign( 'items_sliders', $items );
			
		// Add pane
			jimport('joomla.html.pane');
			//$sliders = JPane::getInstance( 'sliders' );
			//$this->assign('sliders', $sliders);
			
		// form
			//$validate = JSession::getFormToken();
			$validate = JSession::getFormToken();
			
			$form = array();
			$view = strtolower( $app->input->get('view') );
			//$view = strtolower( JRequest::getVar('view') );
			$form['action'] = "index.php?option=com_citruscart&controller={$view}&view={$view}";
			$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
			$this->assign( 'form', $form );
			
		// set the required image
		// TODO Fix this to use defines
			$required = new stdClass();
			$required->text = JText::_('COM_CITRUSCART_REQUIRED');
			$required->image = "<img src='".JURI::root()."/media/citruscart/images/required_16.png' alt='{$required->text}'>";
			$this->assign('required', $required );
			
		// Elements
		$elementArticleModel 	= JModelLegacy::getInstance( 'ElementArticle', 'CitruscartModel' );
		$this->assign( 'elementArticleModel', $elementArticleModel );
		
			// terms
			$elementArticle_terms 		= $elementArticleModel->fetchElement( 'article_terms', $row->get('article_terms') );
			$resetArticle_terms			= $elementArticleModel->clearElement( 'article_terms', '0' );
			$this->assign('elementArticle_terms', $elementArticle_terms);
			$this->assign('resetArticle_terms', $resetArticle_terms);
            // shipping
            $elementArticle_shipping       = $elementArticleModel->fetchElement( 'article_shipping', $row->get('article_shipping') );
            $resetArticle_shipping         = $elementArticleModel->clearElement( 'article_shipping', '0' );
            $this->assign('elementArticle_shipping', $elementArticle_shipping);
            $this->assign('resetArticle_shipping', $resetArticle_shipping);			
			

    }
    
}
