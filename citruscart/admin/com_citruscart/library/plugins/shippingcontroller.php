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

class CitruscartControllerShippingPlugin extends CitruscartController {

	// the same as the plugin's one!
	var $_element = '';

	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Overrides the getView method, adding the plugin's layout path
	 */
	public function getView( $name = '', $type = '', $prefix = '', $config = array() ){
    	$view = parent::getView( $name, $type, $prefix, $config );
		  if(version_compare(JVERSION,'1.6.0','ge')) {
			   // Joomla! 1.6+ code
    	   $view->addTemplatePath(JPATH_SITE.'/plugins/Citruscart/'.$this->_element.'/'.$this->_element.'/tmpl/');
      }
      else {
    	   $view->addTemplatePath(JPATH_SITE.'/plugins/Citruscart/'.$this->_element.'/tmpl/');
      }
    	return $view;
    }

    /**
     * Overrides the delete method, to include the custom models and tables.
     */
    public function delete()
    {
    	$this->includeCustomModel('ShippingRates');
    	$this->includeCustomTables();
    	parent::delete();
    }

    protected function includeCustomTables(){
   		// Include the custom table
    	
		JFactory::getApplication()->triggerEvent('includeCustomTables', array() );
    }

    protected function includeCustomModel( $name ){
    	
		JFactory::getApplication()->triggerEvent('includeCustomModel', array($name, $this->_element) );
    }

    protected function includeCitruscartModel( $name ){
    	
		JFactory::getApplication()->triggerEvent('includeCitruscartModel', array($name) );
    }

    protected function baseLink(){
    	
    	/* Get the application */
    	$app = JFactory::getApplication();
    	$id = $app->input->getInt('id', '');
    	
    	//$id = JRequest::getInt('id', '');
    	return "index.php?option=com_citruscart&view=shipping&task=view&id={$id}";
    }
}