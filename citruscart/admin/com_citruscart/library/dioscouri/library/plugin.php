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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

// TODO Make all Sample plugins extend this _base file, to reduce code redundancy

/** Import library dependencies */
jimport('joomla.plugin.plugin');
jimport('joomla.utilities.string');



class DSCPlugin extends JPlugin
{
    /**
     * @var $_element  string  Should always correspond with the plugin's filename,
     *                         forcing it to be unique
     */
    protected $_element    = '';

    /**
    *
    * Dot notation prefix to the plugin, e.g. 'plugin_element.'
    */
    protected $dot_path_prefix = '';

    /**
     *
     * Slash notation prefix to the plugin's path, e.g. 'plugin_element/'
     */
    protected $path_prefix = '';

    /**
     * Checks to make sure that this plugin is the one being triggered by the extension
     *
     * @access public
     * @return mixed Parameter value
     * @since 1.5
     */
    protected function _isMe( $row )
    {
        $element = $this->_element;
        $success = false;
        if (is_object($row) && !empty($row->element) && $row->element == $element )
        {
            $success = true;
        }

        if (is_string($row) && $row == $element ) {
            $success = true;
        }

        return $success;
    }

    /**
     * Prepares variables for the form
     *
     * @return string   HTML to display
     */
    protected function _renderForm()
    {
        $vars = new JObject();
        $html = $this->_getLayout('form', $vars);

        return $html;
    }

    /**
     * Prepares the 'view' tmpl layout
     *
     * @param array
     * @return string   HTML to display
     */
    protected function _renderView()
    {
        $vars = new JObject();
        $html = $this->_getLayout('view', $vars);
        return $html;
    }

    /**
     * Wraps the given text in the HTML
     *
     * @param string $text
     * @return string
     * @access protected
     */
    protected function _renderMessage($message = '')
    {
        $vars = new JObject();
        $vars->message = $message;
        $html = $this->_getLayout('message', $vars);
        return $html;
    }

    /**
     * Gets the parsed layout file
     *
     * @param string $layout The name of  the layout file
     * @param object $vars Variables to assign to
     * @param string $plugin The name of the plugin
     * @param string $group The plugin's group
     * @return string
     * @access protected
     */
    protected function _getLayout($layout, $vars = false, $plugin = '', $group = '' )
    {
        if (empty($group))
        {
            $app = DSC::getApp();
            $com_name = $app->getName();
            $group = str_replace( 'com_', '', $com_name );
            if (empty($group))
            {
                // TODO Try to get it some other way, such as from the name of the plugin?
                return null;
            }
        }

        if (empty($plugin))
        {
            $plugin = $this->_element;
        }

        ob_start();
        $layout = $this->_getLayoutPath( $plugin, $group, $layout );
        include($layout);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }


    /**
     * Get the path to a layout file
     *
     * @param   string  $plugin The name of the plugin file
     * @param   string  $group The plugin's group
     * @param   string  $layout The name of the plugin layout file
     * @return  string  The path to the plugin layout file
     * @access protected
     */
    protected function _getLayoutPath($plugin, $group, $layout = 'default')
    {
        $app = JFactory::getApplication();

        // get the template and default paths for the layout
        $templatePath = JPATH_SITE.'/templates/'.$app->getTemplate().'/html/plugins/'.$group.'/'.$plugin.'/'.$layout.'.php';
		if(version_compare(JVERSION,'1.6.0','ge')) {
            // Joomla! 1.6+ code here
            $defaultPath = JPATH_SITE.'/plugins/'.$group.'/'.$plugin.'/'.$plugin.'/tmpl/'.$layout.'.php';
        } else {
            // Joomla! 1.5 code here
            $defaultPath = JPATH_SITE.'/plugins/'.$group.'/'.$plugin.'/tmpl/'.$layout.'.php';
        }

        // if the site template has a layout override, use it
        jimport('joomla.filesystem.file');
        if (JFile::exists( $templatePath ))
        {
            return $templatePath;
        }
        else
        {
            return $defaultPath;
        }
    }

    /**
     * This displays the content article
     * specified in the plugin's params
     *
     * @return unknown_type
     */
    protected function _displayArticle()
    {
        $html = '';

        $articleid = $this->params->get('articleid');
        if ($articleid)
        {
            $html = DSCArticle::display( $articleid );
        }

        return $html;
    }

    /**
     * Checks for a form token in the request
     * Using a suffix enables multi-step forms
     *
     * @param string $suffix
     * @return boolean
     */
    protected function _checkToken( $suffix='', $method='post' )
    {
        $token  = JSession::getFormToken();
        $token .= ".".strtolower($suffix);
        if (JFactory::getApplication()->input->get( $token, '', $method, 'alnum' ))
        {
            return true;
        }
        return false;
    }

    /**
     * Generates an HTML form token and affixes a suffix to the token
     * enabling the form to be identified as a step in a process
     *
     * @param string $suffix
     * @return string HTML
     */
    protected function _getToken( $suffix='' )
    {
        $token  = JSession::getFormToken();
        $token .= ".".strtolower($suffix);
        $html  = '<input type="hidden" name="'.$token.'" value="1" />';
        $html .= '<input type="hidden" name="tokenSuffix" value="'.$suffix.'" />';
        return $html;
    }

    /**
     * Gets the suffix affixed to the form's token
     * which helps identify which step this is
     * in a multi-step process
     *
     * @return string
     */
    protected function _getTokenSuffix( $method='post' )
    {
    	$input = JFactory::getApplication()->input;
    	$suffix = $input->getString('tokenSuffix');
        //$suffix = JRequest::getVar( 'tokenSuffix', '', $method );
        if (!$this->_checkToken($suffix, $method))
        {
            // what to do if there isn't this suffix's token in the request?
            // anything?
        }
        return $suffix;
    }

    /**
     * Gets the row from the __plugins DB table that corresponds to this plugin
     *
     * @return object
     */
    protected function _getMe()
    {
        if (empty($this->_row))
        {
            JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_sample/tables' );
            $table = JTable::getInstance('Shipping', 'SampleTable');
            $table->load( array('element'=>$this->_element, 'folder'=>'sample') );
            $this->_row = $table;
        }
        return $this->_row;
    }

    /**
     * Make the standard Sample Tables avaiable in the plugin
     */
    protected function includeSampleTables()
    {
        // Include Sample Tables Classes
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_sample/tables' );
    }

    /**
     * Include a particular Sample Model
     * @param $name the name of the mode (ex: products)
     */
    protected function includeSampleModel($name)
    {
        if (strtolower($name) != 'base')
            Sample::load( 'SampleModel'.ucfirst(strtolower($name)), 'models.'.strtolower($name) );
        else
            Sample::load( 'SampleModelBase', 'models._base' );
    }

    /**
     * Include a particular Custom Model
     * @param $name the name of the model
     * @param $plugin the name of the plugin in which the model is stored
     * @param $group the group of the plugin
     */
    protected function includeCustomModel($name, $plugin = '', $group = 'sample')
    {
        if (empty($plugin))
        {
            $plugin = $this->_element;
        }

        if(!class_exists('SampleModel'.$name))
            JLoader::import( 'plugins.'.$group.'.'.$plugin.'.models.'.strtolower($name), JPATH_SITE );
    }

    /**
     * add a user-defined table to list of available tables (including the Sample tables
     * @param $plugin the name of the plugin in which the table is stored
     * @param $group the group of the plugin
     */
    protected function includeCustomTables($plugin = '', $group = 'sample'){

        if (empty($plugin))
        {
            $plugin = $this->_element;
        }

        $this->includeSampleTables();
        $customPath = JPATH_SITE.'/plugins/'.$group.'/'.$plugin.'/tables';
        JTable::addIncludePath( $customPath );
    }

    /**
     * Include a particular Custom View
     * @param $name the name of the view
     * @param $plugin the name of the plugin in which the view is stored
     * @param $group the group of the plugin
     */
    protected function includeCustomView($name, $plugin = '', $group = 'sample')
    {
        if (empty($plugin))
        {
            $plugin = $this->_element;
        }

        if(!class_exists('SampleView'.$name))
            JLoader::import( 'plugins.'.$group.'.'.$plugin.'.views.'.strtolower($name), JPATH_SITE );
    }


}