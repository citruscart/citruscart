<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableXref extends CitruscartTable
{
	public $_keynames;
	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	function store( $updateNulls=false )
	{
		JPluginHelper::importPlugin('citruscart');
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$before = $app->triggerEvent( 'onBeforeStore'.$this->get('_suffix'), array( $this ) );

		if (in_array(false, $before, true))
		{
			return false;
		}

		// check if a record exists with these key values
			$already = clone $this;
			//$keynames = $this->getKeyNames();

			// get the keynames of the table
			$keynames = $this->_keynames;
			//print_r($keynames); exit;

			foreach ($keynames as $key=>$value)
			{
				$keynames[$key] = $this->$key;
			}

            if ( $already->load( $keynames ) )
			{

				$ret = $this->updateObject( $updateNulls );
			}
				else
			{
				$ret = $this->insertObject($keynames);

			}

			if( !$ret )
			{
				$this->setError(get_class( $this ).'::store failed - '.$this->getError() );
				$return = false;
			}
				else
			{
				$return = true;
			}

		if ( $return )
		{
			$app->triggerEvent( 'onAfterStore'.$this->get('_suffix'), array( $this ) );
		}
		return $return;
	}

	/**
	 * (non-PHPdoc)
	 * @see Citruscart/admin/tables/CitruscartTable#delete($oid)
	 */
	function delete( $oid='' )
	{


		JPluginHelper::importPlugin('citruscart');
        $app = JFactory::getApplication();
	    if (empty($oid))
        {
            // if empty, use the values of the current keys
           // $keynames = $this->getKeyNames();
        	$keynames = $this->_keynames;
            foreach ($keynames as $key=>$value)
            {
                $oid[$key] = $this->$key;
            }
            if (empty($oid))
            {
                // if still empty, fail
                $this->setError( JText::_('COM_CITRUSCART_CANNOT_DELETE_WITH_EMPTY_KEY') );

                return false;
            }
        }
        $oid = (array) $oid;

	    //
        $before = $app->triggerEvent( 'onBeforeDelete'.$this->get('_suffix'), array( $this, $oid ) );
        if (in_array(false, $before, true))
        {
            return false;
        }

	    $db = $this->getDBO();

        // initialize the query
        $query = new CitruscartQuery();
        $query->delete();
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
            $value = $db->q( $db->escape( trim( strtolower( $value ) ) ) );
            $query->where( $key.' = '.$value);
        }

        $db->setQuery( (string) $query );

		if ($db->query())
		{
			//
			$app->triggerEvent( 'onAfterDelete'.$this->get('_suffix'), array( $this, $oid ) );
			return true;
		}
		else
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Inserts a row into a table based on an objects properties
	 *
	 * @access	public
	 * @param	string	The name of the table
	 * @param	object	An object whose properties match table fields
	 * @param	string	The name of the primary key. If provided the object property is updated.
	 */
	function insertObject($data)
	{
		$db = JFactory::getDbo();

		$table = $this->getTableName();

		$key = implode(',',array_keys($data));

		$key_value = implode(',',array_values($data));

		$fmtsql = 'INSERT INTO '.$db->qn($table).' ( %s ) VALUES ( %s ) ';
		//$fmtsql = "INSERT INTO $table( $key ) VALUES ( $key_value ) ";

		$fields = array();

		foreach (get_object_vars( $this ) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[] = $this->_db->quoteName( $k );
			$values[] = $this->_db->isQuoted( $k ) ? $this->_db->Quote( $v ) : (int) $v;
		}
		$this->_db->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
		if (!$this->_db->query())
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 * Updates an existing role
	 *
	 * @access public
	 * @param [type] $updateNulls
	 */
	function updateObject( $updateNulls=true )
	{

		$table = $this->getTableName();


		$fmtsql = 'UPDATE '.$this->_db->quoteName($table).' SET %s WHERE %s';
		$tmp = array();
		$where = array();
		foreach (get_object_vars( $this ) as $k => $v)
		{
			if ( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
				continue;
			}

			if ( in_array( $k, $this->getKeyNames() ) )
			{
                // Allow PKs to be updated
				// TODO Use query builder
				// ->where()
				$where[] = $k . '=' . $this->_db->Quote( $v );
			}

			if ($v === null)
			{
				if ($updateNulls) {
					$val = 'NULL';
				} else {
					continue;
				}
			} else {
				$val = $this->_db->isQuoted( $k ) ? $this->_db->Quote( $v ) : (int) $v;
			}
			$tmp[] = $this->_db->quoteName( $k ) . '=' . $val;
		}
		$this->_db->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , implode( " AND ", $where ) ) );
		if (!$this->_db->query())
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
}
