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

class CitruscartToolPlugin extends CitruscartPluginBase
{
    /**
     * @var $_element  string  Should always correspond with the plugin's filename,
     *                         forcing it to be unique
     */
    var $_element    = '';

    /**
     * @var $_tablename  string  A required tablename to use when verifying the provided prefix
     */
    var $_tablename = '';

    /**
     * Wrapper for the internal _renderView method
     * Generally you won't have to override this,
     * but you can if you want to
     *
     * @param $options
     * @return unknown_type
     */
    function onGetToolView( $row )
    {
        if (!$this->_isMe($row))
        {
            return null;
        }

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
        $ns = $app->getName().'::'.'com.Citruscart.tool.'.$this->get('_element');
        return $ns;
    }

    /**
     * Attempts to connect to a DB using provided info
     * Will often be extended
     *
     * @return boolean if fail, JDatabase object if success
     */
    function _verifyDB()
    {
        $state = $this->_getState();

        // verify connection
        $option = array();
        $option['driver']   = $state->driver;           // Database driver name
        $option['host']     = $state->host;             // Database host name
        if ($state->port != '3306')
        {
            $option['host'] .= ":".$state->port;        // alternative ports
        }
        $option['user']     = $state->user;             // User for database authentication
        $option['password'] = $state->password;         // Password for database authentication
        $option['database'] = $state->database;         // Database name
        $option['prefix']   = $state->prefix;           // Database prefix (may be empty)

        if (!$option['host'] || !$option['database'] || !$option['user'] || !$option['password'] || !$option['driver'] )
        {
            $this->setError( JText::_('COM_CITRUSCART_PLEASE_PROVIDE_ALL_REQUIRED_INFORMATION') );
            return false;
        }

        $database = JDatabase::getInstance( $option );

        // check that $newdatabase is_object and has method setQuery
        if (!is_object($database) || !method_exists($database, 'setQuery'))
        {
            $this->setError( JText::_('COM_CITRUSCART_COULD_NOT_CREATE_DATABASE_INSTANCE') );
            return false;
        }

        $database->setQuery(" SELECT NOW(); ");
        if (!$result = $database->loadResult())
        {
            $this->setError( JText::_('COM_CITRUSCART_COULD_NOT_PROPERLY_QUERY_THE_DATABASE') );
            return false;
        }

        if (!empty($option['prefix']))
        {
            // Check that the prefix is valid by checking a table name on the target DB
            $tables = $database->getTableList();
            $table_name = $option['prefix'].$this->_tablename;

            if (!in_array($table_name, $tables))
            {
                $this->setError( JText::_('COULD_NOT_FIND_APPROPRIATE_TABLES_USING_PROVIDED_PREFIX') );
                return false;
            }
        }

        return $database;
    }




}