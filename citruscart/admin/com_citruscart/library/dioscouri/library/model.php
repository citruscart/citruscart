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

jimport( 'joomla.filter.filterinput' );
jimport( 'joomla.application.component.model' );

require_once JPATH_SITE .'/libraries/dioscouri/library/compatibility/model.php';

require_once(JPATH_SITE.'/libraries/dioscouri/library/query.php');

require_once(JPATH_SITE.'/libraries/dioscouri/library/table.php');

class DSCModel extends DSCModelBase
{
    var $_filterinput = null; // instance of JFilterInput
    public $cache_enabled = true;
    public $cache_lifetime = '900';

    function __construct($config = array())
    {
        parent::__construct($config);
        $this->_filterinput = JFilterInput::getInstance();

    	if (empty($this->option))
		{
			$r = null;

			if (!preg_match('/(.*)Model/i', get_class($this), $r))
			{
				JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'));
			}

			$this->option = 'com_' . strtolower($r[1]);
		}

        //set the model state
		if (array_key_exists('state', $config))  {
			$this->_state = $config['state'];
			$this->state = $config['state'];
		} else {
			$this->_state = new JObject();
			$this->state = new JObject();
		}
    }

    /**
     * Method to get a table object, load it if necessary.
     *
     * @access  public
     * @param   string The table name. Optional.
     * @param   string The class prefix. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  object  The table
     * @since   1.5
     */
    function getTable($name='', $prefix=null, $options = array())
    {
        if (empty($name)) {
            $name = $this->getName();

        }

        if (empty($prefix)) {
        	$prefix = str_replace('com_', '', $this->option) . 'Table';
        }

        DSCTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_sample/tables' );
        if ($table = $this->_createTable( $name, $prefix, $options ))  {
            return $table;
        }

        JError::raiseError( 0, 'Table ' . $prefix.$name . ' not supported. File not found.' );
        $null = null;
        return $null;
    }

	/**
	 * Empties the state
	 *
	 * @return unknown_type
	 */
	public function emptyState()
	{
		$state = JArrayHelper::fromObject( $this->getState() );
		foreach ($state as $key=>$value)
		{
			if (substr($key, '0', '1') != '_')
			{
				$this->setState( $key, '' );
			}
		}
		return $this->getState();
	}

	/**
	 * Gets a property from the model's state, or the entire state if no property specified
	 * @param $property
	 * @param $default
	 * @param string The variable type {@see JFilterInput::clean()}.
	 *
	 * @return unknown_type
	 */
	public function getState( $property=null, $default=null, $return_type='default' )
	{
		if(version_compare(JVERSION,'1.6.0','ge')) {
            // Joomla! 1.6+ code here
            $return = ($property === null) ? $this->state : $this->state->get($property, $default);
        } else {
            // Joomla! 1.5 code here
            $return = ($property === null) ? $this->_state : $this->_state->get($property, $default);
        }

		return $this->_filterinput->clean( $return, $return_type );
	}

	/**
	 * Gets the model's query, building it if it doesn't exist
	 * @return valid query object
	 */
	public function getQuery($refresh = false)
	{
		if (empty( $this->_query ) || $refresh)
		{
			$this->_query = $this->_buildQuery($refresh);
		}
		return $this->_query;
	}

	/**
	 * Sets the model's query
	 * @param $query	A valid query object
	 * @return valid query object
	 */
	public function setQuery( $query )
	{
		$this->_query = $query;
		return $this->_query;
	}

	/**
	 * Gets the model's query, building it if it doesn't exist
	 * @return valid query object
	 */
	public function getResultQuery( $refresh=false )
	{
		if (empty( $this->_resultQuery ) || $refresh )
		{
			$this->_resultQuery = $this->_buildResultQuery();
		}
		return $this->_resultQuery;
	}

	/**
	 * Sets the model's query
	 * @param $query	A valid query object
	 * @return valid query object
	 */
	public function setResultQuery( $query )
	{
		$this->_resultQuery = $query;
		return $this->_resultQuery;
	}

	/**
	 * Before individual items in a list are processed, this method allows you to modify the entire list.
	 * You could remove items from the list before they are individually processed, etc.
	 *
	 * @param unknown_type $item
	 * @param unknown_type $key
	 * @param unknown_type $refresh
	 */
	protected function prepareList( &$list, $refresh=false )
	{
		JPluginHelper::importPlugin('citruscart');
		$app = JFactory::getApplication();
	    //$dispatcher = JDispatcher::getInstance( );
	    $app->triggerEvent( 'onPrepareList' . $this->getTable( )->get( '_suffix' ), array( &$list ) );
	}

	/**
	 * Set basic properties for the item, whether in a list or a singleton
	 *
	 * @param unknown_type $item
	 * @param unknown_type $key
	 * @param unknown_type $refresh
	 */
	protected function prepareItem( &$item, $key=0, $refresh=false )
	{
	    if (!empty($this->_objectClass) && !is_a($item, $this->_objectClass)) {
	        $clone = $item;
	        $item = $this->getTable();
	        foreach (get_object_vars($clone) as $prop=>$def)
	        {
	            $item->$prop = $clone->$prop;
	        }
	    }

	    JPluginHelper::importPlugin('citruscart');
		$app = JFactory::getApplication();
	    $app->triggerEvent( 'onPrepare' . $this->getTable( )->get( '_suffix' ), array( &$item ) );
	}

	/**
	 * Retrieves the data for a paginated list
	 * @return array Array of objects containing the data from the database
	 */
	public function getList($refresh = false)
	{
		if (empty( $this->_list ) || $refresh)
		{
		    $cache_key = base64_encode(serialize($this->getState())) . '.list';

		    $classname = strtolower( get_class($this) );
		    $cache = JFactory::getCache( $classname . '.list', '' );
		    $cache->setCaching($this->cache_enabled);
		    $cache->setLifeTime($this->cache_lifetime);
		    $list = $cache->get($cache_key);
			if(!version_compare(JVERSION,'1.6.0','ge'))
			{
				$list = unserialize( trim( $list ) );
			}
		    if (!$list || $refresh)
		    {
    			$query = $this->getQuery($refresh);
    			$list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );

		        if ( empty( $list ) )
		        {
		            $list = array( );
		        }

		        $this->prepareList( $list, $refresh );

		        foreach ( $list as $key=>&$item )
		        {
		            $this->prepareItem( $item, $key, $refresh );
		        }

				if(version_compare(JVERSION,'1.6.0','ge'))
				{
					// joomla! 1.6+ code here
					$cache->store($list, $cache_key);
				}
				else
				{
					// Joomla! 1.5 code here
					$cache->store(  serialize( $list ), $cache_key);
				}
		    }

		    $this->_list = $list;

		}
		return $this->_list;
	}

	/**
	 * Gets an item for displaying (as opposed to saving, which requires a DSCTable object)
	 * using the query from the model and the tbl's unique identifier
	 *
	 * @return database->loadObject() record
	 */
	public function getItem( $pk=null, $refresh=false, $emptyState=true )
	{
	    if (empty($this->_item) || $refresh)
	    {
	        if (is_bool($pk)) {
	            // backwards compatibility
	            $refresh = $pk;
	            $pk = null;
	        }
	        $cache_key = $pk ? $pk : $this->getID();

	        $classname = strtolower( get_class($this) );
	        $cache = JFactory::getCache( $classname . '.item', '' );
	        $cache->setCaching($this->cache_enabled);
	        $cache->setLifeTime($this->cache_lifetime);
	        $item = $cache->get($cache_key);
			if(!version_compare(JVERSION,'1.6.0','ge'))
			{
				$item = unserialize( trim( $item ) );
			}
	        if (!$item || $refresh)
	        {
	            $item = $this->_getItem( $pk, $refresh, $emptyState );

	            if (!empty($item))
	            {
	                $this->prepareItem( $item, 0, $refresh );
	            }

				if(version_compare(JVERSION,'1.6.0','ge'))
				{
					// joomla! 1.6+ code here
					$cache->store($item, $cache_key);
				}
				else
				{
					// Joomla! 1.5 code here
					$cache->store(  serialize( $item ), $cache_key);
				}
	        }

	        $this->_item = $item;

	    }

		return $this->_item;
	}


	protected function _getItem( $pk=null, $refresh=false, $emptyState=true )
	{
	    $cache_key = $pk ? $pk : $this->getID();

	    if ($emptyState)
	    {
	        $this->emptyState();
	    }

	    $query = $this->getQuery( $refresh );
	    $keyname = $this->getTable()->getKeyName();
	    $value  = $this->_db->q( $cache_key );
	    $query->where( "tbl.$keyname = $value" );
	    $this->_db->setQuery( (string) $query );

	    $item = $this->_db->loadObject();

	    return $item;
	}

	/**
	 * Retrieves the data for an un-paginated list
	 * @return array Array of objects containing the data from the database
	 */
	public function getAll()
	{
		if (empty( $this->_all ))
		{
			$query = $this->getQuery();
			$this->_all = $this->_getList( (string) $query, 0, 0 );
		}
		return $this->_all;
	}

    public function getSurrounding( $id )
    {
    	$return = array();
    	$return["prev"] = '';
    	$return["next"] = '';

    	if (empty($id))
    	{
    	    return $return;
    	}

        $prev = $this->getState('prev');
        $next = $this->getState('next');
        if (strlen($prev) || strlen($next))
        {
            $return["prev"] = $prev;
            $return["next"] = $next;
            return $return;
        }

        $db = $this->getDBO();
        $key = $this->getTable()->getKeyName();

        $this->setState('select', 'tbl.' . $key );
        $query = $this->getQuery( true );

        $query->select( '@rownum := @rownum+1 as rownum' );
        $query->join( ' ', ' (SELECT @rownum := 0) r ' );

        $rowset_query = (string) $query;

        $q2 = "
        	SELECT x.rownum INTO @midpoint FROM (
        	$rowset_query
        	) x WHERE x.$key = '$id';
        	";
        $db->setQuery( $q2 );
        $db->query();

        /*
        $q2_5 = "SELECT @midpoint;
        ";
        $db = JFactory::getDBO();
        $db->setQuery( $q2_5 );
        $id_rownum = $db->loadResult();
        echo "<p>Row Number of this ID:</p>". Publications::dump( $id_rownum );
		*/

        $q3 = "
            SELECT x.* FROM (
            $rowset_query
            ) x
            WHERE x.rownum BETWEEN @midpoint - 1 AND @midpoint + 1;
		";
        $db->setQuery( $q3 );
        $rowset = $db->loadObjectList();
        $count = count($rowset);

        $found = false;
        $prev_id = '';
        $next_id = '';

        JArrayHelper::sortObjects( $rowset, 'rownum', '1' );

        for ($i=0; $i < $count && empty($found); $i++)
        {
            $row = $rowset[$i];
            if ($row->$key == $id)
            {
                $found = true;
                $prev_num = $i - 1;
                $next_num = $i + 1;
                if (!empty($rowset[$prev_num]->$key)) { $prev_id = $rowset[$prev_num]->$key; }
                if (!empty($rowset[$next_num]->$key)) { $next_id = $rowset[$next_num]->$key; }
            }
        }

        $return["prev"] = $prev_id;
        $return["next"] = $next_id;
        return $return;
    }

	/**
	 * Paginates the data
	 * @return array Array of objects containing the data from the database
	 */
	public function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	/**
	 * Retrieves the count
	 * @return array Array of objects containing the data from the database
	 */
	public function getTotal()
	{
	    if (empty($this->_total))
	    {
	        $cache_key = base64_encode(serialize($this->getState())) . '.list-totals';

	        $classname = strtolower( get_class($this) );
	        $cache = JFactory::getCache( $classname . '.list-totals', '' );
	        $cache->setCaching($this->cache_enabled);
	        $cache->setLifeTime($this->cache_lifetime);
			$item = $cache->get($cache_key);
			if(!version_compare(JVERSION,'1.6.0','ge'))
			{
				$item = unserialize( trim( $item ) );
			}

	        if (!$item)
	        {
                $query = $this->getQuery();
                $item = $this->_getListCount( (string) $query);

				if(version_compare(JVERSION,'1.6.0','ge'))
				{
					// joomla! 1.6+ code here
		            $cache->store($item, $cache_key);
				}
				else
				{
					// Joomla! 1.5 code here
					$cache->store(  serialize( $item ), $cache_key);
				}
	        }

	        $this->_total = $item;

	    }
	    return $this->_total;
	}

	/**
	 * Retrieves the result from the query
	 * Useful on SUM and COUNT queries
	 *
	 * @return array Array of objects containing the data from the database
	 */
	public function getResult( $refresh=false )
	{
		if (empty($this->_result) || $refresh)
		{
			$query = $this->getResultQuery( $refresh );
			$this->_db->setQuery( (string) $query );
			$this->_result = $this->_db->loadResult();
		}
		return $this->_result;
	}

	/**
	 * Method to set the identifier
	 *
	 * @access	public
	 * @param	int identifier
	 * @return	void
	 */
	public function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Gets the identifier, setting it if it doesn't exist
	 * @return unknown_type
	 */
	public function getId()
	{
		$input = JFactory::getApplication()->input;

		$id= $input->getInt('id',0);
		if (empty($this->_id))
		{
			$id= $input->getInt('id',0);
			$array=$input->get('cid',array($id),'Array');
			$this->setId( (int) $array[0] );
		}


		return $this->_id;
	}

    /**
     * Builds a generic SELECT query
     *
     * @return  string  SELECT query
     */
    protected function _buildQuery( $refresh=false )
    {
    	if (!empty($this->_query) && !$refresh)
    	{
    		return $this->_query;
    	}

    	$query = new DSCQuery();

        $this->_buildQueryFields($query);
        $this->_buildQueryFrom($query);
        $this->_buildQueryJoins($query);
        $this->_buildQueryWhere($query);
        $this->_buildQueryGroup($query);
        $this->_buildQueryHaving($query);
        $this->_buildQueryOrder($query);

		return $query;
    }

 	/**
     * Builds a generic SELECT COUNT(*) query
     */
    protected function _buildResultQuery()
    {
    	$query = new DSCQuery();
		$query->select( $this->getState( 'select', 'COUNT(*)' ) );

        $this->_buildQueryFrom($query);
        $this->_buildQueryJoins($query);
        $this->_buildQueryWhere($query);
        $this->_buildQueryGroup($query);
        $this->_buildQueryHaving($query);

        return $query;
    }

    /**
     * Builds SELECT fields list for the query
     */
    protected function _buildQueryFields(&$query)
    {
		$query->select( $this->getState( 'select', 'tbl.*' ) );
    }

	/**
     * Builds FROM tables list for the query
     */
    protected function _buildQueryFrom(&$query)
    {
    	jimport( 'joomla.database.table' );

    	$name = $this->getTable()->getTableName();
    	$query->from($name.' AS tbl');
    }

    /**
     * Builds JOINS clauses for the query
     */
    protected function _buildQueryJoins(&$query)
    {
    }

    /**
     * Builds WHERE clause for the query
     */
    protected function _buildQueryWhere(&$query)
    {
    }

    /**
     * Builds a GROUP BY clause for the query
     */
    protected function _buildQueryGroup(&$query)
    {
    }

    /**
     * Builds a HAVING clause for the query
     */
    protected function _buildQueryHaving(&$query)
    {
    }

    /**
     * Builds a generic ORDER BY clause based on the model's state
     */
    protected function _buildQueryOrder(&$query)
    {
		$order      = $this->_db->escape( $this->getState('order') );
       	$direction  = $this->_db->escape( strtoupper( $this->getState('direction') ) );

        if ($order)
        {
            $query->order("$order $direction");
        }

       	// TODO Find an abstract way to determine if order is a valid field in query
    	// if (in_array($order, $this->getTable()->getColumns())) does not work
    	// because you could be ordering by a field from one of the JOINed tables

		$cols  = $this->getTable()->getFields();
		//if(DSC_JVERSION == '30') { $cols  = $this->getTable()->getFields();} else {$cols  = $this->getTable()->getColumns();}

		if (in_array('ordering', $cols))
		{
    		$query->order('ordering ASC');
    	}
    }

	protected function getOverriddenMethods($class)
	{
	    $rClass = new ReflectionClass($class);
	    $array = array();

	    foreach ($rClass->getMethods() as $rMethod)
	    {
	        try
	        {
	            // attempt to find method in parent class
	            new ReflectionMethod($rClass->getParentClass()->getName(),
	                                $rMethod->getName());
	            // check whether method is explicitly defined in this class
	            if ($rMethod->getDeclaringClass()->getName()
	                == $rClass->getName())
	            {
	                // if so, then it is overriden, so add to array
	                $array[] =  $rMethod->getName();
	            }
	        }
	        catch (exception $e)
	        {    /* was not in parent class! */    }
	    }

	    return $array;
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
	 * Clean the cache
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function clearCache()
	{
	    if(version_compare(JVERSION,'1.6.0','ge')) {
	        $classname = strtolower( get_class($this) );
	        parent::cleanCache($classname . '.item');
	        parent::cleanCache($classname . '.list');
	        parent::cleanCache($classname . '.list-totals');
	    } else {
	        // Joomla! 1.5 code here
	        return TRUE;

	        //TODO #18  actually clear the cache
	    }

	}
}
