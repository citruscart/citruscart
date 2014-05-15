<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base' );

class CitruscartViewShipping extends CitruscartViewBase
{
	/**
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function getLayoutVars($tpl=null)
	{
		$layout = $this->getLayout();
		$this->renderSubmenu();

		switch(strtolower($layout))
		{
			case "view":
				$this->set( 'leftMenu', 'leftmenu_configuration' );
				$this->_form($tpl);
				break;

			case "form":

				JFactory::getApplication()->input->set('hidemainmenu', '1');
				$this->_form($tpl);
				break;
			case "default":
			default:
				$this->set( 'leftMenu', 'leftmenu_configuration' );
				$this->_default($tpl);
				break;
		}
	}

	function _form($tpl=null)
	{
        parent::_form($tpl);

        // the row is already loaded in base view so this might not be needed leaving this hear to help figure this out in joomla 1.5 if needed.
       if(!version_compare(JVERSION,'1.6.0','ge')) {
		$row = $this->getModel()->getItem();
		$path = JPATH_COMPONENT.$row->folder.'/'.$row->element.'/'.'plg_xml';
		$params = new DSCParameter( $row->params,$path);
		//$params = new DSCParameter( $row->params, JApplicationHelper::getPath( 'plg_xml', $row->folder.'/'.$row->element), 'plugin' );
		$this->assignRef('params',$params);
	   }
	   	$row = $this->getModel()->getItem();
		if(!empty($row)){
	   	$this->assign( 'row', $row );
		$import = JPluginHelper::importPlugin( 'Citruscart', $row->element );
		}
	}

/*	function _form($tpl=null)
	{
		$model = $this->getModel();

		// set the model state
		$this->assign( 'state', $model->getState() );

		// get the data
		// not using getItem here to enable ->checkout (which requires JTable object)
		$row = $model->getTable();
		$row->load( (int) $model->getId() );
		// TODO Check if the item is checked out and if so, setlayout to view

		if (empty($this->hidemenu))
		{
			// set toolbar
			$layout = $this->getLayout();
			$isNew = ($row->id < 1);
			switch(strtolower($layout))
			{
				case "view":
					$this->_viewToolbar($isNew);
					break;
				case "form":
				default:
					// Checkout the item if it isn't already checked out
					$row->checkout( JFactory::getUser()->id );
					$this->_formToolbar($isNew);
					break;
			}
			$view = strtolower( JRequest::getVar('view') );
			$this->displayTitle( 'Edit '.$view );
		}

		// load the plugin
		$row = $this->getModel()->getItem();

    	if(version_compare(JVERSION,'1.6.0','ge'))
	    {
    	  // Joomla! 1.6+ code
	      $row->params = json_decode( $row->params );
	   	  if( !get_object_vars( $row->params ) )
    	  	$row->params = '';
	    }
	    echo JApplicationHelper::getPath( 'plg_xml', $row->folder.DIRECTORY_SEPARATOR.$row->element ).'###';
		$params = new DSCParameter( $row->params, JApplicationHelper::getPath( 'plg_xml', $row->folder.DIRECTORY_SEPARATOR.$row->element ) );
		$this->assignRef('params',$params);

		$this->assign('row', $model->getItem() );

		// load the plugin
		$row = $this->getModel()->getItem();
		$import = JPluginHelper::importPlugin( 'Citruscart', $row->element );
	}
 * *
 */

	function _defaultToolbar()
	{
	}

 function _viewToolbar( $isNew = null )
    {
    	//JToolBarHelper::custom( 'view', 'forward', 'forward', 'COM_CITRUSCART_SUBMIT', false );
    	//JToolBarHelper::cancel( 'close', 'COM_CITRUSCART_CLOSE' );
    }
}
