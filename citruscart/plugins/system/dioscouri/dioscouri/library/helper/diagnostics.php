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

jimport( 'joomla.application.component.model' );

require_once(JPATH_SITE.'/libraries/dioscouri/library/helper.php');
class DSCHelperDiagnostics extends DSCHelper
{
    /**
     * Redirects with message
     *
     * @param object $message [optional]    Message to display
     * @param object $type [optional]       Message type
     */
    function redirect($message = '', $type = '')
    {
        $mainframe = JFactory::getApplication();

        if ($message)
        {
            $mainframe->enqueueMessage($message, $type);
        }

        $mainframe->set('controller', 'config');
        $mainframe->set('view', 'config');
        $mainframe->set('task', '');
        return;
    }

    /**
     * Performs basic checks on your installation to ensure it is OK
     * @return unknown_type
     */
    function checkInstallation()
    {
        /* Check default currency
        if (!$this->checkDefaultCurrency())
        {
            return $this->redirect( JText::_('DIAGNOSTIC CHECKDEFAULTCURRENCY FAILED') .' :: '. $this->getError(), 'error' );
        }*/


    }

    /**
     * Creates a table if it doesn't exist
     *
     * @param $table
     * @param $definition
     */
    function createTable( $table, $definition )
    {
        if (!$this->tableExists( $table ))
        {
            $db = JFactory::getDBO();
            $db->setQuery( $definition );
            if (!$db->query())
            {
                $this->setError( $db->getErrorMsg() );
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if a table exists
     *
     * @param $table
     */
    function tableExists( $table )
    {
        $db = JFactory::getDBO();

        // Manually replace the Joomla Tables prefix. Automatically it fails
        // because the table name is between single-quotes
        $db->setQuery(str_replace('#__', $db->getPrefix(), "SHOW TABLES LIKE '$table'"));
        $result = $db->loadObject();

        if ($result === null) return false;
        else return true;
    }

    /**
     * Inserts fields into a table
     *
     * @param string $table
     * @param array $fields
     * @param array $definitions
     * @return boolean
     */
    function insertTableFields($table, $fields, $definitions)
    {
        $database = JFactory::getDBO();
        $fields = (array) $fields;
        $errors = array();

        foreach ($fields as $field)
        {
            $query = " SHOW COLUMNS FROM {$table} LIKE '{$field}' ";
            $database->setQuery( $query );
            $rows = $database->loadObjectList();
            if (!$rows && !$database->getErrorNum())
            {
                $query = "ALTER TABLE `{$table}` ADD `{$field}` {$definitions[$field]}; ";
                $database->setQuery( $query );
                if (!$database->query())
                {
                    $errors[] = $database->getErrorMsg();
                }
            }
        }

        if (!empty($errors))
        {
            $this->setError( implode('<br/>', $errors) );
            return false;
        }
        return true;
    }

    /**
     * Changes fields in a table
     *
     * @param string $table
     * @param array $fields
     * @param array $definitions
     * @param array $newnames
     * @return boolean
     */
    function changeTableFields($table, $fields, $definitions, $newnames)
    {
        $database = JFactory::getDBO();
        $fields = (array) $fields;
        $errors = array();

        foreach ($fields as $field)
        {
            $query = " SHOW COLUMNS FROM {$table} LIKE '{$field}' ";
            $database->setQuery( $query );
            $rows = $database->loadObjectList();
            if ($rows && !$database->getErrorNum())
            {
                $query = "ALTER TABLE `{$table}` CHANGE `{$field}` `{$newnames[$field]}` {$definitions[$field]}; ";
                $database->setQuery( $query );
                if (!$database->query())
                {
                    $errors[] = $database->getErrorMsg();
                }
            }
        }

        if (!empty($errors))
        {
            $this->setError( implode('<br/>', $errors) );
            return false;
        }
        return true;
    }

    /**
     * Drops fields from a table
     *
     * @param string $table
     * @param array $fields
     * @param array $definitions
     * @return boolean
     */
    function dropTableFields($table, $fields)
    {
        $database = JFactory::getDBO();
        $fields = (array) $fields;
        $errors = array();

        foreach ($fields as $field)
        {
            $query = " SHOW COLUMNS FROM {$table} LIKE '{$field}' ";
            $database->setQuery( $query );
            $rows = $database->loadObjectList();
            if ($rows && !$database->getErrorNum())
            {
                $query = "ALTER TABLE `{$table}` DROP `{$field}`; ";
                $database->setQuery( $query );
                if (!$database->query())
                {
                    $errors[] = $database->getErrorMsg();
                }
            }
        }

        if (!empty($errors))
        {
            $this->setError( implode('<br/>', $errors) );
            return false;
        }
        return true;
    }

}
