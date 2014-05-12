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

require_once JPATH_SITE . '/libraries/dioscouri/library/compatibility/table.php';
require_once JPATH_SITE.'/libraries/dioscouri/library/query.php';


class DSCTable extends DSCTableBase
{
	/**
	 * constructor
	 */
	function __construct( $tbl_name, $tbl_key, &$db, $app=null )
	{

		// set table properties based on table's fields
		if(version_compare(JVERSION,'1.6.0','ge')) {
			// Joomla! 1.6+ does this in $this->getFields(), which is called by the constructor
		} else {
			$this->setTableProperties();
		}

		$prev = $this->get('_app');
		if (empty($prev)) {
			$this->set('_app', $app);
		}

		parent::__construct( $tbl_name, $tbl_key, $db );

	}

	/**
	 * Lock the DB tables
	 * @return unknown_type
	 */
	function _lock()
	{
		$query = "LOCK TABLES {$this->_tbl} WRITE;";
		$this->_db->setQuery( $query );
		$this->_db->query();

		if ($this->_db->getErrorNum())
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		$this->_locked = true;
		return true;
	}

	/**
	 * Unlock the DB tables locked in this session
	 * @return unknown_type
	 */
	function _unlock()
	{
		$query = "UNLOCK TABLES;";
		$this->_db->setQuery( $query );
		$this->_db->query();

		if ($this->_db->getErrorNum())
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		$this->_locked = false;
		return true;
	}

	/**
	 * Get columns from db table
	 * @return unknown_type
	 */
	function getColumns()
	{
		$classname = strtolower( get_class($this) );
		$cache = JFactory::getCache( $classname . '.columns', '' );
		$cache->setCaching(true);
		$cache->setLifeTime('86400');
		$fields = $cache->get($classname);
		if (empty($fields))
		{
			$fields = $this->_db->getTableColumns($this->getTableName());
			if(version_compare(JVERSION,'1.6.0','ge'))
			{
				// joomla! 1.6+ code here
				$cache->store( $fields, $classname);
			}
			else
			{
				$cache->store(  serialize( $fields ), $classname);
			}
		}
		else
		{
			if(!version_compare(JVERSION,'1.6.0','ge'))
			{
				$fields = unserialize( trim( $fields ) );
			}
		}

	    $fields = $fields[$this->getTableName()];

        if (empty($fields)) {
            $fields = array();
        }

		return $fields;
	}

	/**
	 * Set properties of object based on table fields
	 *
	 * @acces   public
	 * @return  object
	 */
	function setTableProperties()
	{
		static $fields;

		if (empty($fields))
		{
			$fields = $this->getColumns();
		}

		foreach ($fields as $name=>$type)
		{
			$this->$name = null;
		}
	}

	/**
	 * Gets the key names
	 *
	 * returned $keynames array typically looks like:
	 * $keynames['product_id']  = 'product_id';
	 * $keynames['category_id'] = 'category_id';
	 *
	 * @return array
	 * @since 1.5
	 */
	public function getKeyNames()
	{
		$keynames = $this->_tbl_keys;

		 if (!is_array($keynames))
		{
			// set _tbl_keys using the primary keyname
			$keynames = array();
			$keyName = $this->getKeyName();
			$keynames[$keyName] = $keyName;
			$this->_tbl_keys = $keynames;

		}

  	return $this->_tbl_keys;
	}

	/**
	 * Sets the keynames
	 *
	 * $keynames typically looks like:
	 * $keynames = array();
	 * $keynames['product_id']  = 'product_id';
	 * $keynames['category_id'] = 'category_id';
	 *
	 * @param $keynames array
	 * @return unknown_type
	 */
	public function setKeyNames( $keynames )
	{
		//$this->_tbl_keys = $keynames;
		$this->_keynames = $keynames;
		return $this->_tbl_keys;
	}

	/**
	 * Loads a row from the database and binds the fields to the object properties
	 *
	 * @access	public
	 * @param	mixed	Optional primary key.  If not specifed, the value of current key is used
	 * @return	boolean	True if successful
	 */
	function load( $oid=null, $reset=true )
	{
		JPluginHelper::importPlugin('Citruscart');
		$app  = JFactory::getApplication();


		if (!is_array($oid))
		{
			// load by primary key if not array
			$keyName = $this->getKeyName();
			$oid = array( $keyName => $oid );
		}

		if (empty($oid))
		{
			// if empty, use the value of the current key
			$keyName = $this->getKeyName();
			$oid = $this->$keyName;
			if (empty($oid))
			{
				// if still empty, fail
				$this->setError( JText::_( "Cannot load with empty key" ) );
				return false;
			}
		}

		// allow $oid to be an array of key=>values to use when loading
		$oid = (array) $oid;

		if (!empty($reset))
		{
			$this->reset();
		}

		$db = $this->getDBO();

		// initialize the query
		$query = new DSCQuery();
		$query->select( '*' );
		$query->from( $this->getTableName() );

		foreach ($oid as $key=>$value)
		{
			// Check that $key is field in table
			if ( !in_array( $key, array_keys( $this->getProperties() ) ) )
			{
				$this->setError( get_class( $this ).' does not have the field '.$key );
				return false;
			}
			// add the key=>value pair to the query
			$value = $db->Quote( $db->escape( trim( strtolower( $value ) ) ) );
			$query->where( $key.' = '.$value);
		}

		$db->setQuery( (string) $query );

		if ( $result = $db->loadAssoc() )
		{
			$result = $this->bind($result);

			if( $result )
			{

				$app->triggerEvent( 'onLoad'.$this->get('_suffix'), array( &$this ) );
			}

			return $result;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Generic save function
	 *
	 * @access	public
	 * @returns TRUE if completely successful, FALSE if partially or not successful
	 */
	function save($src='', $orderingFilter = '', $ignore = '')
	{
		$this->_isNew = false;
		$key = $this->getKeyName();
		if (empty($this->$key))
		{
			$this->_isNew = true;
		}

		if ( !$this->check() )
		{
			return false;
		}

		if ( !$this->store() )
		{
			return false;
		}

		if ( !$this->checkin() )
		{
			$this->setError( $this->_db->stderr() );
			return false;
		}

		/*$app = $this->get('_app');
		if ( empty($app) || DSC::getApp( $app )->get('enable_reorder_table', '0') ||  )
		{
			$this->reorder();

		}*/

		$this->setError('');

		// TODO Move ALL onAfterSave plugin events here as opposed to in the controllers, duh
		//
		//JFactory::getApplication()->triggerEvent( 'onAfterSave'.$this->get('_suffix'), array( $this ) );
		return true;
	}

	/**
	 *
	 * @param unknown_type $updateNulls
	 * @return unknown_type
	 */
	function store( $updateNulls=false )
	{
		$app = JFactory::getApplication();
		JPluginHelper::importPlugin('Citruscart');

		$before = $app->triggerEvent( 'onBeforeStore'.$this->get('_suffix'), array( &$this ) );
		if (in_array(false, $before, true))
		{
			return false;
		}

		if ( $return = parent::store( $updateNulls ))
		{

			$app->triggerEvent( 'onAfterStore'.$this->get('_suffix'), array( $this ) );
		}
		return $return;
	}

	/**
	 *
	 * @param $oid
	 * @return unknown_type
	 */
	function delete( $oid=null )
	{

		$before = JFactory::getApplication()->triggerEvent( 'onBeforeDelete'.$this->get('_suffix'), array( $this, $oid ) );
		if (in_array(false, $before, true))
		{
			return false;
		}

		if ( $return = parent::delete( $oid ))
		{

			JFactory::getApplication()->triggerEvent( 'onAfterDelete'.$this->get('_suffix'), array( $this, $oid ) );
		}
		return $return;
	}

	/**
	 *
	 * @param unknown_type $change
	 * @param unknown_type $where
	 * @return unknown_type
	 */
	function move($change, $where='')
	{
		if ( !in_array( 'ordering', array_keys( $this->getProperties() ) ) )
		{
			$this->setError( get_class( $this ).' does not support ordering');
			return false;
		}

		settype($change, 'int');

		if ($change !== 0)
		{
			$old = $this->ordering;
			$new = $this->ordering + $change;
			$new = $new <= 0 ? 1 : $new;

			$query =  ' UPDATE '.$this->getTableName().' ';

			if ($change < 0) {
				$query .= 'SET ordering = ordering+1 WHERE '.$new.' <= ordering AND ordering < '.$old;
				$query .= ($where ? ' AND '.$where : '');
			} else {
				$query .= 'SET ordering = ordering-1 WHERE '.$old.' < ordering AND ordering <= '.$new;
				$query .= ($where ? ' AND '.$where : '');
			}

			$this->_db->setQuery( $query );
			if (!$this->_db->query())
			{
				$err = $this->_db->getErrorMsg();
				JError::raiseError( 500, $err );
				return false;
			}

			$this->ordering = $new;
			return $this->save();
		}

		return $this;
	}

	/**
	 * Uses the parameters from com_content to clean the HTML from a fieldname
	 *
	 * @param $fieldname (optional) default = description
	 * @return void
	 */
	function filterHTML( $fieldname='description' )
	{
		if ( !in_array( $fieldname, array_keys( $this->getProperties() ) ) )
		{
			$this->setError( get_class( $this ).' does not have a field named `'.$fieldname.'`' );
			return;
		}

		// Filter settings
		jimport( 'joomla.application.component.helper' );
		$config	= JComponentHelper::getParams( 'com_content' );
		$user	= JFactory::getUser();
		$gid	= $user->get( 'gid' );

		$filterGroups	= $config->get( 'filter_groups' );

		// convert to array if one group selected
		if ( (!is_array($filterGroups) && (int) $filterGroups > 0) )
		{
			$filterGroups = array($filterGroups);
		}

		if (is_array($filterGroups) && in_array( $gid, $filterGroups ))
		{
			$filterType		= $config->get( 'filter_type' );
			$filterDSC		= preg_split( '#[,\s]+#', trim( $config->get( 'filter_sample' ) ) );
			$filterAttrs	= preg_split( '#[,\s]+#', trim( $config->get( 'filter_attritbutes' ) ) );
			switch ($filterType)
			{
				case 'NH':
					$filter	= new JFilterInput();
					break;
				case 'WL':
					$filter	= new JFilterInput( $filterDSC, $filterAttrs, 0, 0 );
					break;
				case 'BL':
				default:
					$filter	= new JFilterInput( $filterDSC, $filterAttrs, 1, 1 );
					break;
			}
			$this->$fieldname	= $filter->clean( $this->$fieldname );
		}
		elseif (empty($filterGroups))
		{
			$filter = new JFilterInput(array(), array(), 1, 1);
			$this->$fieldname = $filter->clean( $this->$fieldname );
		}
	}

	/**
	 * Retrieve row field value
	 *
	 * @param  	string 	The user-specified column name.
	 * @return 	string 	The corresponding column value.
	 */
	public function __get($columnName)
	{
		if ($columnName == 'id')
		{
			$columnName = $this->getKeyName();
		}
		return $this->get($columnName);
	}

	/**
	 * (non-PHPdoc)
	 * @see JObject::getProperties()
	 */
	function trimProperties( $public = true )
	{
		$vars  = get_object_vars($this);

		if($public)
		{
			foreach ($vars as $key => $value)
			{
				if ('_' == substr($key, 0, 1)) {
					unset($this->$key);
				}
			}
		}

		return $this;
	}

	/**
	 * Any errors set?  If so, check fails
	 *
	 * @see JTable::check()
	 */
	public function check()
	{
	    $errors = $this->getErrors();
	    if (!empty($errors))
	    {
	        foreach ($errors as $key=>$error)
	        {
	            $error = trim( $error );
	            if (empty($error))
	            {
	                unset($errors[$key]);
	            }
	        }

	        if (!empty($errors))
	        {
	            return false;
	        }
	    }

	    return true;
	}




}
