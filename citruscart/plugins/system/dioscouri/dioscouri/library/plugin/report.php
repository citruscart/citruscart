<?php
/**
 * @version 1.5
 * @package Sample
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class DSCPluginReport extends DSCPlugin
{
    /**
     * @var $_element  string  Should always correspond with the plugin's filename,
     *                         forcing it to be unique
     */
    var $_element    = '';

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
        $ns = $app->getName().'::'.'com.sample.report.'.$this->get('_element');
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
    function _getModel( $name = '', $prefix = 'SampleModel', $config = array() )
    {
    	if (empty( $name ))
    	{
    		$name = $this->get('default_model', 'Base');
    	}

        $fullname = strtolower( $prefix.$name );
        if (empty($this->_models[$fullname]))
        {
        	DSCModel::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_sample/models' );
            if ( !$model = DSCModel::getInstance($name, $prefix, $config) )
            {
                $model = new SampleModelBase();
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
    function _renderView()
    {
        // TODO Load the report, get the data, and render the report html using the form inputs & data

        $vars = new JObject();
        $vars->items = $this->_getData();
        $vars->state = $this->_getModel()->getState();

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