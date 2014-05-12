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

Citruscart::load( 'CitruscartPluginBase', 'library.plugins._base' );
Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartReportPlugin extends CitruscartPluginBase
{
    /**
     * @var $_element  string  Should always correspond with the plugin's filename,
     *                         forcing it to be unique
     */
    var $_element    = '';

    var $_pagination = '';
    /**
     * @var array() instances of Models to be used by the report
     */
    public $_models = array();

    /**
     * @var $default_model  string  Default model used by report
     */
    var $default_model    = '';

    /**
     * Wrapper for the internal _renderView method
     *
     * @param $options
     * @return unknown_type
     */
    function onGetReportView( $row )
    {
        if (!$this->_isMe($row))
        {
            return null;
        }

        $data = $this->_getData();

        $html = "";
        $html .= $this->_renderForm();
        $html .= $this->_renderView();

        return $html;
    }

    /**
     * Gets the reports namespace for state variables
     * @return string
     */
    function _getNamespace()
    {
        $app = JFactory::getApplication();
        $ns = $app->getName().'::'.'com.Citruscart.report.'.$this->get('_element');
        return $ns;
    }

    /**
     * Gets the state
     *
     * @return array
     */
    function _getState()
    {
        $app = JFactory::getApplication();
        $model = $this->_getModel( $this->get('default_model') );
        $ns = $this->_getNamespace();

        $state = array();

        $state['filter']    = $app->getUserStateFromRequest($ns.'.filter', 'filter', '', 'string');
        $state['filter_enabled']    = $app->getUserStateFromRequest($ns.'enabled', 'filter_enabled', '', '');
        $state['filter_date_from'] = $app->getUserStateFromRequest($ns.'date_from', 'filter_date_from', '', '');
        $state['filter_date_to'] = $app->getUserStateFromRequest($ns.'date_to', 'filter_date_to', '', '');
        $state['filter_datetype']   = $app->getUserStateFromRequest($ns.'datetype', 'filter_datetype', '', '');
        $state['filter_range']    = $app->getUserStateFromRequest($ns.'range', 'filter_range', '', '');
        $state['limit'] = $app->getUserStateFromRequest($ns.'limit', 'limit', '', '');
        $state['limitstart'] = $app->getUserStateFromRequest($ns.'limitstart', 'limitstart', '', '');
        $state = $this->_handleRangePresets( $state );

        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }

        return $state;
    }

    /**
     * Gets the model
     * only creating it if it doesn't exist
     *
     * @return array
     */
    function _getModel( $name = '', $prefix = 'CitruscartModel', $config = array() )
    {
    	if (empty( $name ))
    	{
    		$name = $this->get('default_model', 'Base');
    	}

        $fullname = strtolower( $prefix.$name );
        if (empty($this->_models[$fullname]))
        {
        	JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
            if ( !$model = JModelLegacy::getInstance($name, $prefix, $config) )
            {
                $model = new CitruscartModelBase();
            }
            $this->_models[$fullname] = $model;
        }
        return $this->_models[$fullname];
    }

    /**
     * Sets the date range filters if a preset value was selected
     * @param $state    Array
     * @return array
     */
    function _handleRangePresets( $state )
    {
        // TODO Do some pretty stuff based on the value of filter_range, if it is one of the presets
    	return $state;
    }

    /**
     * Processes the report form
     * and returns data to be processed and displayed
     *
     * @return unknown_type
     */
    function _getData()
    {
    	$state = $this->_getState();
        $model = $this->_getModel();
        $data = $model->getList();

        return $data;
    }



    public function getPagination()
    {
        if (empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
            $model = $this->_getModel();

            $this->_pagination = new JPagination( $model->getTotal(), $model->getState('limitstart'), $model->getState('limit') );
        }

        return $this->_pagination;
    }
    /************************************
     * Note to 3pd:
     *
     * The methods between here
     * and the next comment block are
     * yours to modify by overrriding them in your report plugin
     *
     ************************************/

    /**
     * Prepares the 'view' tmpl layout
     * when viewing a report
     *
     * @return unknown_type
     */

   protected   function _renderView($options='')
    {
        // TODO Load the report, get the data, and render the report html using the form inputs & data

        $vars = new JObject();
        $vars->items = $this->_getData();
        $vars->state = $this->_getModel()->getState();
        $vars->pagination = $this->getPagination();
        $html = $this->_getLayout('view', $vars);

        return $html;
    }

    /**
     * Prepares variables for the report form
     *
     * @return unknown_type
     */
    function _renderForm()
    {
        $vars = new JObject();
        $vars->state = $this->_getModel()->getState();

        $html = $this->_getLayout('form', $vars);

        return $html;
    }

}