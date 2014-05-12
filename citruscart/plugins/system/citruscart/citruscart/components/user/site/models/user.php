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

Citruscart::load( 'CitruscartModelBase', 'models._base' );
//jimport('joomla.application.component.model');

/**
 * User Component User Model
 *
 * @package     Joomla
 * @subpackage  User
 * @since 1.5
 */
class UserModelUser extends CitruscartModelBase
{
    /**
     * User id
     *
     * @var int
     */
    var $_id = null;

    /**
     * User data
     *
     * @var array
     */
    var $_data = null;

    /**
     * Constructor
     *
     * @since 1.5
     */
    function __construct()
    {
    	$input = JFactory::getApplication()->input;
        parent::__construct();

        $id = $input->getInt('id', 0);
        $this->setId($id);
    }

    function getTable()
    {
        $return = JTable::getInstance('user');
        return $return;
    }

    /**
     * Method to set the weblink identifier
     *
     * @access  public
     * @param   int Weblink identifier
     */
    function setId($id)
    {
        // Set weblink id and wipe data
        $this->_id      = $id;
        $this->_data    = null;
    }

    /**
     * Method to get a user
     *
     * @since 1.5
     */
    function &getData()
    {
        // Load the weblink data
        if ($this->_loadData()) {
            //do nothing
        }

        return $this->_data;
    }

    /**
     * Method to store the user data
     *
     * @access  public
     * @return  boolean True on success
     * @since   1.5
     */
    function store($data)
    {
        $user       = JFactory::getUser();
        $username   = $user->get('username');

        // Bind the form fields to the user table
        if (!$user->bind($data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Store the web link table to the database
        if (!$user->save()) {
            $this->setError( $user->getError() );
            return false;
        }

        $session = JFactory::getSession();
        $session->set('user', $user);

        // check if username has been changed
        if ( $username != $user->get('username') )
        {
            $table = $this->getTable('session', 'JTable');
            $table->load($session->getId());
            $table->username = $user->get('username');
            $table->store();

        }

        return true;
    }

    /**
     * Method to load user data
     *
     * @access  private
     * @return  boolean True on success
     * @since   1.5
     */
    function _loadData()
    {
        // Lets load the content if it doesn't already exist
        if (empty($this->_data))
        {
            $this->_data = JFactory::getUser();
            return (boolean) $this->_data;
        }
        return true;
    }
}

