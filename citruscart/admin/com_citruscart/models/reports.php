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

class CitruscartModelReports extends CitruscartModelBase 
{	
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
        $filter_name    = $this->getState('filter_name');
        
       	if ($filter) 
       	{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
			$where = array();
			$where[] = 'LOWER(tbl.id) LIKE '.$key;
			$where[] = 'LOWER(tbl.name) LIKE '.$key;
			$query->where('('.implode(' OR ', $where).')');
       	}
        if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
            {
                $query->where('tbl.id >= '.(int) $filter_id_from);
            }
                else
            {
                $query->where('tbl.id = '.(int) $filter_id_from);
            }
        }
        
        if (strlen($filter_id_to))
        {
            $query->where('tbl.id <= '.(int) $filter_id_to);
        }
        
        if ($filter_name) 
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.name) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        
        // force returned records to only be Citruscart reports 
        $query->where("tbl.folder = 'Citruscart'");
        $query->where("tbl.element LIKE 'report_%'");
    }
    	
	public function getList($refresh=false)
	{
		$list = parent::getList($refresh);
		foreach($list as $item)
		{
			if(version_compare(JVERSION,'1.6.0','ge')) {$item->id = $item->extension_id; }
			$item->link = 'index.php?option=com_citruscart&view=reports&task=view&id='.$item->id;
		}
		return $list;
	}
	
	/**
	 * Gets an item for displaying (as opposed to saving, which requires a JTable object)
	 * using the query from the model and the tbl's unique identifier
	 *
	 * @return database->loadObject() record
	 */
	public function getItem( $pk=null, $refresh=false, $emptyState=true )
	{
		if (empty( $this->_item ))
		{
		    if ($emptyState)
		    {
		        $this->emptyState();
		    }
			$query = $this->getQuery();
			// TODO Make this respond to the model's state, so other table keys can be used
			// perhaps depend entirely on the _buildQueryWhere() clause?
			$keyname = $this->getTable()->getKeyName();
			$value	= $this->_db->q( $this->getId() );
			$query->where( "tbl.$keyname = $value" );
			$this->_db->setQuery( (string) $query );
			$this->_item = $this->_db->loadObject();
		}
		
		$overridden_methods = $this->getOverriddenMethods( get_class($this) );
		if (!in_array('getItem', $overridden_methods))  
		{
			
			JFactory::getApplication()->triggerEvent( 'onPrepare'.$this->getTable()->get('_suffix'), array( &$this->_item ) );
		}
		// adding this in the model so we don't have to have if statemnets all over the views and controllers'
		
		if(version_compare(JVERSION,'1.6.0','ge')) {
			if(!empty($this->_item->extension_id)) {
			$this->_item->id =	$this->_item->extension_id;
			}
		}
		
		return $this->_item;
	}
	
}
