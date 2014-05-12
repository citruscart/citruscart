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

jimport('joomla.filter.filteroutput');
jimport('joomla.application.component.view');

/*Use this file to include admin only specific code*/

//require_once JPATH_SITE . '/libraries/dioscouri/library/view/admin.php';
//require_once JPATH_SITE . '/libraries/dioscouri/library/view.php';
//require_once JPATH_SITE . '/libraries/dioscouri/library/menu.php';

require_once(JPATH_SITE.'/libraries/dioscouri/library/view.php');

require_once(JPATH_SITE.'/libraries/dioscouri/library/menu.php');

class DSCViewAdmin extends DSCView
{
	/**
	 * Displays a layout file
	 *
	 * @param unknown_type $tpl
	 * @return unknown_type
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		JHTML::_('stylesheet', 'admin.css', 'media/' . $this -> _option . '/css/');

		$this->getLayoutVars($tpl);

		$this->displayTitle($this->get('title'));

		if (!$app->input->getInt('hidemainmenu') && empty($this->hidemenu)) {

		require_once(JPATH_SITE.'/libraries/dioscouri/library/menu.php');

		DSCMenu::getInstance()->display();

		/*
			if(DSC_JVERSION == 30) {
				DSCMenu::getInstance()->display();
			} else {
				$menu = DSCMenu::getInstance();
			} */


		}

		jimport('joomla.application.module.helper');
		$modules = JModuleHelper::getModules($this->_name . "_left");
		if ($modules && !$app->input->getInt('hidemainmenu') && empty($this->hidemenu) || !empty($this->leftMenu) && empty($this->hidemenu)) {
		$this->displayWithLeftMenu($tpl = null, $this->leftMenu);


		} else {
		    parent::display($tpl);
		}
	}




	/**
	* Displays text as the title of the page
	*
	* @param $text
	* @return unknown_type
	*/
	public function displayTitle($text = '')
	{
		$input = JFactory::getApplication()->input;
	    $layout = $this->getLayout();
        switch(strtolower($layout))
        {
            case "footer":
                break;
            case "default":
            default:
                $app = DSC::getApp();
                $title = $text ? JText::_($text) : JText::_(ucfirst($input->getString('view')));
                JToolBarHelper::title($title, $app->getName());
                break;
        }
	}

	/**
	 * Displays a layout file with room for a left menu bar
	 * @param $tpl
	 * @return unknown_type
	 */
	public function displayWithLeftMenu($tpl = null, $menuname)
	{
	    // TODO This is an ugly, quick hack - fix it
	    echo "<table width='100%'>";
	    echo "<tr>";
	    echo "<td style='width: 180px; padding-right: 5px; vertical-align: top;' >";

	    DSC::load('DSCMenu', 'library.menu');
	    if ($menu = DSCMenu::getInstance($menuname)) {
	        $menu->display('leftmenu');
	    }

	    $modules = JModuleHelper::getModules($this->_name . "_left");
	    $document = JFactory::getDocument();
	    $renderer = $document->loadRenderer('module');
	    $attribs = array();
	    $attribs['style'] = 'xhtml';
	    foreach ($modules as $mod) {
	        echo $renderer->render($mod, $attribs);
	    }

	    echo "</td>";
	    echo "<td style='vertical-align: top;' >";
	    parent::display($tpl);
	    echo "</td>";
	    echo "</tr>";
	    echo "</table>";
	}

	/**
	* Basic commands for displaying a list
	*
	* @param $tpl
	* @return unknown_type
	*/
	function _default($tpl = '')
	{

	    if (empty($this->hidemenu))
	    {
	        // add toolbar buttons
	        $this->_defaultToolbar();
	    }

	    parent::_default($tpl);
	}

	/**
	* Basic methods for displaying an item from a list
	* @param $tpl
	* @return unknown_type
	*/
	function _form($tpl = '')
	{
		$app = JFactory::getApplication();
	    if (empty($this->hidemenu))
	    {
	        $model = $this->getModel();

	        // get the data
	        $table = $model->getTable();
	        $table->load( (int) $model->getId() );

	        // set toolbar
	        $layout = $this->getLayout();
	        $isNew = ($table->id < 1);
	        $view = ucwords( strtolower( $app->input->getString('view') ) );
	        switch(strtolower($layout))
	        {
	            case "view" :
	                $this->set( "title", 'View ' . $view);
	                $this->_viewToolbar($isNew);
	                break;
	            case "form" :
	            default :
	                $this->set( "title", 'Edit ' . $view);
	                $this->_formToolbar($isNew);
	                break;
	        }
	    }

	    parent::_form($tpl);
	}

	/**
	* The default toolbar for a list
	* @return unknown_type
	*/
	function _defaultToolbar()
	{
	    JToolBarHelper::editList();
	    JToolBarHelper::deleteList(JText::_('VALIDDELETEITEMS'));
	    JToolBarHelper::addnew();
	}

	/**
	* The default toolbar for editing an item
	* @param $isNew
	* @return unknown_type
	*/
	function _formToolbar($isNew = null)
	{
	    $divider = false;
	    $surrounding = (!empty($this->surrounding)) ? $this->surrounding : array();
	    if (!empty($surrounding['prev'])) {
	        $divider = true;
	        JToolBarHelper::custom('saveprev', "saveprev", "saveprev", 'Save and Prev', false);
	    }
	    if (!empty($surrounding['next'])) {
	        $divider = true;
	        JToolBarHelper::custom('savenext', "savenext", "savenext", 'Save and Next', false);
	    }
	    if ($divider) {
	        JToolBarHelper::divider();
	    }

	    JToolBarHelper::custom('savenew', "savenew", "savenew", 'Save and New', false);
	    JToolBarHelper::save('save');
	    JToolBarHelper::apply('apply');

	    if ($isNew) {
	        JToolBarHelper::cancel();
	    } else {
	        JToolBarHelper::cancel('close', 'Close');
	    }
	}

	/**
	* The default toolbar for viewing an item
	* @param $isNew
	* @return unknown_type
	*/
	function _viewToolbar($isNew = null)
	{
	    $divider = false;
	    $surrounding = (!empty($this->surrounding)) ? $this->surrounding : array();
	    if (!empty($surrounding['prev'])) {
	        $divider = true;
	        JToolBarHelper::custom('prev', "prev", "prev", 'Prev', false);
	    }
	    if (!empty($surrounding['next'])) {
	        $divider = true;
	        JToolBarHelper::custom('next', "next", "next", 'Next', false);
	    }
	    if ($divider) {
	        JToolBarHelper::divider();
	    }

	    JToolBarHelper::cancel('close', 'Close' );
	}

}
