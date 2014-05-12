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

class CitruscartModelEav extends CitruscartModelBase
{
    public $_getEav = true;
    public $_getEavOptions = array();

	function __construct($config = array())
	{
		//set the model state
		if (array_key_exists('state', $config))
		{
			// Search for eav special states
			if (!property_exists($config['state'], '_eav'))
			{
				$config['state']->_eav = new JObject();
			}
		}
		else
		{
			// Add the _eav state
			$config['state'] = new JObject();
			$config['state']->_eav = new JObject();
		}
		parent::__construct($config);
	}

	/**
	 * Sets a Eav special state
	 *
	 * @param $property
	 * @param $value
	 */
	public function setEavState($property, $value = null)
	{
		return $this->_state->_eav->set($property, $value);
	}

	/**
	 * Gets a Eav special state
	 * @param $property
	 */
	public function getEavState($property = null, $default=null, $return_type='default' )
	{
		$return = $property === null ? $this->_state->_eav : $this->_state->_eav->get($property, $default);
		return $this->_filterinput->clean( $return, $return_type );
	}

	/**
	 * Builds a generic SELECT query
	 * Adds a $this->_buildQueryEav()
	 *
	 * @return  string  SELECT query
	 */
	protected function _buildQuery( $refresh=false )
	{
		if (!empty($this->_query) && !$refresh)
		{
			return $this->_query;
		}

		$query = new CitruscartQuery();

		$this->_buildQueryFields($query);
		$this->_buildQueryFrom($query);
		$this->_buildQueryJoins($query);
		$this->_buildQueryWhere($query);
		$this->_buildQueryGroup($query);
		$this->_buildQueryHaving($query);
		$this->_buildQueryOrder($query);

		// Eav States
		$this->_buildQueryEav($query);

		// Allow plugins to edit the query object
		$suffix = ucfirst( $this->getName() );
		
		JFactory::getApplication()->triggerEvent( 'onAfterBuildQuery'.$suffix, array( &$query, $this->getState() ) );

		return $query;
	}

	/**
	 * Add joins for eav field if needed for ordeding
	 */
	protected function _buildQueryOrder(&$query)
	{
		$order      = $this->_db->escape( $this->getState('order') );

		// Ordering prefix for table column
		$prefix = 'value_';

		// if order is requested on an eav column
		if((strpos($order, $prefix) === 0) )
		{
			// Different table name for different joins!
			$attribute_alias = substr($order, strlen($prefix));
			$tbl_key = $this->getTable()->getKeyName();
			$eav_tbl_name = 'eav_'.$attribute_alias;
			$value_tbl_name = 'value_'.$attribute_alias;

			// we haven't already joined the tables, join them
			if ($this->getEavState($order, '') == '')
			{
				// Join the table based on the type of the value
				Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
				$eav_helper = CitruscartHelperBase::getInstance( 'Eav' );
				$table_type = $eav_helper->getType( $attribute_alias );

				// Join!
				$query->join('LEFT', '#__citruscart_eavattributes AS '.$eav_tbl_name.' ON tbl.'.$tbl_key.' = '.$eav_tbl_name.'.eaventity_id');
				$query->join('LEFT', '#__citruscart_eavvalues'.$table_type.' AS '.$value_tbl_name.' ON '.$eav_tbl_name.'.eavattribute_id = '.$value_tbl_name.'.eavattribute_id');
			}

			$direction  = $this->_db->escape( strtoupper( $this->getState('direction') ) );
			// Order field is the eavvalue_value field
			$order = $value_tbl_name.'.eavvalue_value';

			$query->order("$order $direction");

		}
		else
		{
			// normal ordering, not on eav column
			parent::_buildQueryOrder($query);
		}
	}

	/**
	 * Dedicated function for eav fields filtering
	 * @param CitruscartQuery $query
	 */
	protected function _buildQueryEav(&$query)
	{
		$eavStates = $this->getEavState()->getProperties();

		// If there are eav states set
		if(count($eavStates))
		{
			// Loop through the filters
			foreach($eavStates as $k => $v)
			{
				$filter_prefix = 'filter_';

				// Is it a filter?
				if(strpos($k, $filter_prefix) === 0)
				{
					// Different table name for different joins!
					// alias on which we want to filter
					$attribute_alias = substr($k, strlen($filter_prefix));
					$tbl_key = $this->getTable()->getKeyName();
					$eav_tbl_name = 'eav_'.$attribute_alias;
					$value_tbl_name = 'value_'.$attribute_alias;

					// Join the table based on the type of the value
					Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
					$eav_helper = CitruscartHelperBase::getInstance( 'Eav' );
					$table_type = $eav_helper->getType( $attribute_alias );

					// Join the tables
					$query->join('LEFT', '#__citruscart_eavattributes AS '.$eav_tbl_name.' ON tbl.'.$tbl_key.' = '.$eav_tbl_name.'.eaventity_id');
					$query->join('LEFT', '#__citruscart_eavvalues'.$table_type.' AS '.$value_tbl_name.' ON '.$eav_tbl_name.'.eavattribute_id = '.$value_tbl_name.'.eavattribute_id');

					// Filter using '='
					$query->where($eav_tbl_name.".eavattribute_alias = '{$attribute_alias}'");
					$query->where($value_tbl_name.".eavvalue_value = '{$v}'");
				}
			}
		}
	}

	/**
	 * Get the item with the related attributes (if $getEav is true)
	 * @param	boolean	$emptyState
	 * @param	boolean	$getEav
	 */
	public function getItem( $refresh = true, $getEav = true, $emptyState=true )
	{
	    $this->_getEav = $getEav;
	    $this->_getEavOptions = array();

		return parent::getItem( null, $refresh, $emptyState );
	}

	/**
	 * Get the list of items. If needed, loads the attributes for each item
	 *
	 * @param	boolean	$refresh
	 * @param	boolean	$getEav
	 * @param	array	$options; keys: include, exclude; includes or excludes eav attributes from loading. Use their alias
	 */
	public function getList($refresh = false, $getEav = true, $options = array())
	{
	    $this->_getEav = $getEav;
	    $this->_getEavOptions = $options;

	    return parent::getList($refresh);
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
		$input= JFactory::getApplication()->input;
		$getEav = $this->_getEav;
	    $options = $this->_getEavOptions;

	    $eavStates = count($this->getEavState()->getProperties());

	    if (!empty($getEav) || ($eavStates > 0) )
	    {
	        $app = JFactory::getApplication();
	        $editable_by = $app->isAdmin() ? 1 : 2;
			$view = $input->get( 'view', '' );
			if( $app->isAdmin() && $view == 'pos' ) {
				$editable_by = array( 1, 2 );
			}

	        Citruscart::load('CitruscartModelEavAttributes', 'models.eavattributes');
	        Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
	        $eav_helper = CitruscartHelperBase::getInstance( 'Eav' );

	        $entity = $this->getTable()->get('_suffix');

	        $tbl_key = $this->getTable()->getKeyName();
	        $entity_id = $item->$tbl_key;

	        // add the custom fields as properties
	        $eavs = $eav_helper->getAttributes( $entity, $entity_id, false, $editable_by );

	        // Mirrored table?
	        if (!count($eavs) && strlen($this->getTable()->getLinkedTable()))
	        {
	            $entity = $this->getTable()->getLinkedTable();
	            $entity_id = $item->{$this->getTable()->getLinkedTableKeyName()};
	            $eavs = $eav_helper->getAttributes( $entity, $entity_id, false, $editable_by );
	        }

	        foreach($eavs as $eav)
	        {
	            $key = $eav->eavattribute_alias;
	            $add = true;

	            // Include Mode: Fetch only these fields
	            if(array_key_exists('include', $options))
	            {
	                foreach($options['include'] as $k)
	                {
	                    if($key != $k)
	                    {
	                        $add = false;
	                    }
	                }
	            }
	            else
	            {
	                // Exclude Mode: Fetch everything except these fields
	                if(array_key_exists('exclude', $options))
	                {
	                    foreach($options['exclude'] as $k)
	                    {
	                        if($key == $k)
	                        {
	                            $add = false;
	                        }
	                    }
	                }

	                // Default Mode: Fetch Everything
	            }

	            if($add)
	            {
	                $value = $eav_helper->getAttributeValue($eav, $this->getTable()->get('_suffix'), $item->$tbl_key, false, true );

	                // Do NOT override properties
	                if(!property_exists($item, $key))
	                {
	                    $item->$key = $value;
	                }
	            }
	        }
	    }
	    parent::prepareItem( $item, $key, $refresh );
	}
}