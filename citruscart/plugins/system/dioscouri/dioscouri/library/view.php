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

jimport('joomla.filter.filteroutput');
jimport('joomla.application.component.view');


require_once( JPATH_SITE.'/libraries/dioscouri/library/grid.php');

require_once JPATH_SITE . '/libraries/dioscouri/library/compatibility/view.php';

class DSCView extends DSCViewBase
{
	var $_option = NULL;
	var $_name = NULL;
	protected $_doTask = null;

	function __construct($config = array())
	{
		$input = JFactory::getApplication()->input;
		$app = DSC::getApp();
		$this->_option = !empty($app) ? 'com_'.$app->getName() : $input->get('option');
		parent::__construct($config);
	}

	/**
	* Sets the task to something valid
	*
	* @access   public
	* @param    string $task The task name.
	* @return   string Previous value
	* @since    1.5
	*/
	public function setTask($task)
	{
	    $previous = $this->_doTask;
	    $this->_doTask  = $task;
	    return $previous;
	}

	/**
	 *
	 * Enter description here ...
	 * @return string
	 */
	public function getTask()
	{
	    return $this->_doTask;
	}

	/**
	 * Displays a layout file
	 *
	 * @param unknown_type $tpl
	 * @return unknown_type
	 */
	function display($tpl = null)
	{
	    // display() will return null if 'doTask' is not set by the controller
	    // This prevents unauthorized access by bypassing the controllers
	    $task = $this->getTask();
	    if (empty($task))
	    {
	        return null;
	    }

	    parent::display($tpl);
	}

    /**
     * Gets layout vars for the view
     *
     * @return unknown_type
     */
    function getLayoutVars($tpl=null)
    {	 $input  = JFactory::getApplication()->input;
        $layout = $this->getLayout();
        switch(strtolower($layout))
        {
            case "view":
                $this->_form($tpl);
              break;
            case "form":
                //JRequest::setVar('hidemainmenu', '1');
            	$input->getInt('hidemainmenu', 1);
                $this->_form($tpl);
              break;
            case "default":
            default:
                $this->_default($tpl);
              break;
        }
    }

    /**
     * Basic commands for displaying a list
     *
     * @param $tpl
     * @return unknown_type
     */
    function _default($tpl='')
    {
    	$input = JFactory::getApplication()->input;
        $model = $this->getModel();
        // set the model state
            $state = new JObject();
            if (empty($this->no_state) && method_exists( $model, 'getState') ) {
                $state = $model->getState();
            }
            JFilterOutput::objectHTMLSafe( $state );
            $this->assign( 'state', $state );

        // page-navigation
            if (empty($this->no_pagination) && method_exists( $model, 'getPagination') ) {
                $this->assign( 'pagination', $model->getPagination() );
            }

        // list of items
            if (empty($this->no_items) && method_exists( $model, 'getList') ) {
                $this->assign('items', $model->getList());
            }



		$validate = JSession::getFormToken();
          $form = array();
           $view = strtolower(  $input->getString('view') );
           $form['action'] = $this->get( '_action', "index.php?option={$this->_option}&controller={$view}&view={$view}" );
           $form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
           $this->assign( 'form', $form );
    }


    /**
     * Basic methods for a form
     * @param $tpl
     * @return unknown_type
     */
    function _form($tpl='')
    {
    	$input = JFactory::getApplication()->input;
        $model = $this->getModel();

        // get the data
            $row = $model->getItem();
            JFilterOutput::objectHTMLSafe( $row );
            $this->assign('row', $row );

        // form
            $form = array();
            $controller = strtolower( $this->get( '_controller', $input->getString('controller', $input->getString('view') ) ) );
            $view = strtolower( $this->get( '_view', $input->getString('view') ) );
            $task = strtolower( $this->get( '_task', 'edit' ) );
            $form['action'] = $this->get( '_action', "index.php?option={$this->_option}&controller={$controller}&view={$view}&task={$task}&id=".$model->getId() );
            $form['validation'] = $this->get( '_validation', "index.php?option={$this->_option}&controller={$controller}&view={$view}&task=validate&format=raw" );

            $validate = JSession::getFormToken();


			//if(DSC_JVERSION == '30') { $validate = JSession::getFormToken();} else {$validate = JSession::getFormToken();}
		    $form['validate'] = "<input type='hidden' name='".$validate."' value='1' />";

		    $form['id'] = $model->getId();
            $this->assign( 'form', $form );

        // set the required image
        // TODO Fix this
            $required = new stdClass();
            $required->text = JText::_( 'LIB_DSC_REQUIRED' );
            $required->image = DSCGrid::required( 'LIB_DSC_REQUIRED' );
            $this->assign('required', $required );
    }

	/**
	 * The default toolbar for a list
	 * @return unknown_type
	 */
	function _defaultToolbar()
	{
	}

	/**
	 * The default toolbar for editing an item
	 * @param $isNew
	 * @return unknown_type
	 */
	function _formToolbar($isNew = null)
	{
	}

	/**
	 * The default toolbar for viewing an item
	 * @param $isNew
	 * @return unknown_type
	 */
	function _viewToolbar($isNew = null)
	{
	}

}

