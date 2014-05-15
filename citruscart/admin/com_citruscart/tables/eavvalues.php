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

class CitruscartTableEavValues extends CitruscartTable
{
	// If the table type was set
	private $active = false;
	
	// The type of the eav
	protected $type = '';
	
	// Allowed table types
	private $allowed_types = array();
	
	function CitruscartTableEavValues( &$db )
	{
		
		$this->allowed_types = array('int', 'varchar', 'decimal', 'text', 'datetime', 'time');		
		
		// do NOT parent::__construct, do the two thing that we can do now
		$this->_tbl_key	= 'eavvalue_id';
		$this->_db		= $db;
		
		// do NOT set table properties based on table's fields
		// delegate this to the setType() method
	}
	
	/**
	 * 
	 * Set the type of the table, to correctly use the related db table (eavvaluesvarchar, etc)
	 * @param string $type
	 */
	public function setType($type)
	{
		// Check the type
		$type = strtolower($type);
		if(!in_array($type, $this->allowed_types))
		{
			$type = 'varchar'; // default to varchar
		}
		
		$name 		= 'citruscart';
		$eav_suffix = 'eavvalues';
		$this->type = $type;
		
		// Set the correct suffix
		$this->set( '_suffix', $eav_suffix.$type );
		$tbl_name =  "#__{$name}_{$eav_suffix}{$type}";
		$this->_tbl = $tbl_name;
		
		// Now set the properties!
		$this->setTableProperties();
		
		// Table Type defined: Activate the table
		$this->active = true;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function store( $updateNulls=false )
	{
		// Check the table activation status first
		if(!$this->active)
		{
			// Activate it with a default value
			$this->setType('');
		}
		
		if( $this->getType() == 'datetime' )
		{	
			if( isset( $this->eavvalue_value ) )
			{
				$null_date = JFactory::getDbo()->getNullDate();
				if( $this->eavvalue_value == $null_date || $this->eavvalue_value == '' )
				{
					$this->eavvalue_value = $null_date;	
				}
				else 
				{
					$offset = JFactory::getConfig()->getValue( 'config.offset' );
					$this->eavvalue_value = date( 'Y-m-d H:i:s', strtotime( CitruscartHelperBase::getOffsetDate( $this->eavvalue_value, -$offset ) ) );
				}
			}
		}
		return parent::store( $updateNulls );
	}
	
	public function load( $oid=null, $reset=true)
	{
		// Check the table activation status first
		if(!$this->active)
		{
			// Activate it with a default value
			$this->setType('');
		}
		
		return parent::load( $oid, $reset );
	}
	
	public function save($src='', $orderingFilter = '', $ignore = '')
	{
		// Check the table activation status first
		if(!$this->active)
		{
			// Activate it with a default value
			$this->setType('');
		}
		
		return parent::save($src, $orderingFilter, $ignore);
	}
	
	public function reset()
	{
		// Check the table activation status first
		if(!$this->active)
		{
			// Activate it with a default value
			$this->setType('');
		}
		
		return parent::reset();
	}
	
	public function check()
	{
		// Check the table activation status first
		if(!$this->active)
		{
			// Activate it with a default value
			$this->setType('');
		}
		
		$nullDate	= $this->_db->getNullDate();
		if (empty($this->modified_date) || $this->modified_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->modified_date = $date->toSql();
		}
		if (empty($this->created_date) || $this->created_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->created_date = $date->toSql();
		}
		
		return true;
	}
	
	public function delete( $oid = null )
	{
		// Check the table activation status first
		if(!$this->active)
		{
			// Activate it with a default value
			$this->setType('');
		}
		
		return parent::delete( $oid );
	}
	
	public function move( $change, $where = '' )
	{
		// Check the table activation status first
		if(!$this->active)
		{
			// Activate it with a default value
			$this->setType('');
		}
		
		return parent::move( $change, $where );
	}
	
	public function bind( $from, $ignore=array() )
	{
		// Check the table activation status first
		if(!$this->active)
		{
			// Activate it with a default value
			$this->setType('');
		}
		
		return parent::bind( $from, $ignore );
	}
	
	public function getAllowedTypes()
	{
		return $this->allowed_types;
	}
}
