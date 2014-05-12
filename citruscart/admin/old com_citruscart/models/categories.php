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

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelCategories extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
        $filter     	= $this->getState('filter');
        $filter_id_from	= $this->getState('filter_id_from');
        $filter_id_to	= $this->getState('filter_id_to');
        $filter_name	= $this->getState('filter_name');
       	$enabled		= $this->getState('filter_enabled');
       	$parentid		= $this->getState('filter_parentid');
       	$level          = $this->getState('filter_level');

       	if ($filter)
       	{
			$key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.category_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.category_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.category_description) LIKE '.$key;

			$query->where('('.implode(' OR ', $where).')');
       	}
        if (strlen($filter_id_from))
        {
        	if (strlen($filter_id_to))
        	{
        		$query->where('tbl.category_id >= '.(int) $filter_id_from);
        	}
        		else
        	{
        		$query->where('tbl.category_id = '.(int) $filter_id_from);
        	}
       	}
		if (strlen($filter_id_to))
        {
        	$query->where('tbl.category_id <= '.(int) $filter_id_to);
       	}
    	if (strlen($filter_name))
        {
        	$key	= $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
        	$query->where('LOWER(tbl.category_name) LIKE '.$key);
       	}
    	if (strlen($enabled))
        {
        	$query->where('tbl.category_enabled = '.$this->_db->Quote($enabled));
       	}
        if (strlen($parentid))
        {
        	$parent = $this->getTable();
        	$parent->load( $parentid );
        	if (!empty($parent->category_id))
        	{
        		$query->where('tbl.lft BETWEEN '.$parent->lft.' AND '.$parent->rgt );
        	}
       	}

       	if (strlen($level))
       	{
       	    $query->where("tbl.parent_id = '$level'");
       	    if ($level > 1)
       	    {
       	        $query->where("parent.category_id = '$level'");
       	    }
       	}

       	$query->where('tbl.isroot != 1');
       	$query->where('tbl.lft BETWEEN parent.lft AND parent.rgt');
    }

	/**
     * Builds FROM tables list for the query
     */
    protected function _buildQueryFrom(&$query)
    {
    	$name = $this->getTable()->getTableName();
    	$query->from($name.' AS tbl');
    	$query->from($name.' AS parent');
    }

	protected function _buildQueryFields(&$query)
	{
       	$level = $this->getState('filter_level');

		$field = array();
		$field[] = " COUNT(parent.category_id)-1 AS level ";
		$field[] = " CONCAT( REPEAT(' ', COUNT(parent.category_name) - 1), tbl.category_name) AS name ";

        if ($level > 1)
        {
            $field[] = " parent.category_id AS parent_category_id ";
            $field[] = " parent.category_name AS parent_category_name ";
        }

        $field[] = "
            (
            SELECT
                COUNT(xref.category_id)
            FROM
                #__citruscart_productcategoryxref AS xref
            WHERE
                xref.category_id = tbl.category_id
            )
        AS products_count ";

		$query->select( $this->getState( 'select', 'tbl.*' ) );
		$query->select( $field );
	}

    /**
     * Builds a GROUP BY clause for the query
     */
    protected function _buildQueryGroup(&$query)
    {
    	$query->group('tbl.category_id');
    }

	/**
     * Builds a generic SELECT COUNT(*) query
     */
    protected function _buildResultQuery()
    {
    	$grouped_query = new CitruscartQuery();
		$grouped_query->select( $this->getState( 'select', 'COUNT(*)' ) );

        $this->_buildQueryFrom($grouped_query);
        $this->_buildQueryJoins($grouped_query);
        $this->_buildQueryWhere($grouped_query);
        $this->_buildQueryGroup($grouped_query);
        $this->_buildQueryHaving($grouped_query);

        $query = new CitruscartQuery( );
		$query->select( 'COUNT(*)' );
		$query->from( '(' . $grouped_query . ') as grouped_count' );

        // Allow plugins to edit the query object
        $suffix = ucfirst( $this->getName() );

		JFactory::getApplication()->triggerEvent( 'onAfterBuildResultQuery'.$suffix, array( &$query ) );

        return $query;
    }

	public function getList($refresh = false)
	{
 		/* $db = JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select("* ,category_name as name,parent_id as level");
		$query->from("#__citruscart_categories");
		$db->setQuery($query);
		$list = $db->loadObjectList(); */
		$list = parent::getList($refresh);

		// If no item in the list, return an array()
        if( empty( $list ) ){
        	return array();
        }

		foreach($list as $item)
		{
		    $item->slug = $item->category_alias ? ":$item->category_alias" : "";
			$item->link = 'index.php?option=com_citruscart&view=categories&task=edit&id='.$item->category_id;
		}
		return $list;
	}

	public function getItemid( $id, $fallback=null, $allow_null=false )
	{
		$input= JFactory::getApplication()->input;
	    Citruscart::load( 'CitruscartHelperRoute', 'helpers.route' );

	    $return = CitruscartHelperRoute::findItemid(array('view'=>'products', 'task'=>'', 'filter_category'=>$id));
	    if (!$return) {
	        $return = CitruscartHelperRoute::findItemid(array('view'=>'products', 'task'=>''));
	        if (!$return) {
	            if ($fallback) {
	                $return = $fallback;
	            }

	            if (!$allow_null)
	            {
	                if (!$return)
	                {
	                    $return = $input->getInt('Itemid');
	                }

	                if (!$return) {
	                    $menu	= JFactory::getApplication()->getMenu();
	                    if ($default = $menu->getDefault() && !empty($default->id))
	                    {
	                        $return = $default->id;
	                    }
	                }
	            }
	        }
	    }

	    return $return;
	}

	public function getAlias($id, $refresh=false)
	{
	    $cache_key = $id;

	    $classname = strtolower( get_class($this) );
	    $cache = JFactory::getCache( $classname . '.alias', '' );
	    $cache->setCaching($this->cache_enabled);
	    $cache->setLifeTime($this->cache_lifetime);
	    $item = $cache->get($cache_key);

	    if (!$item || $refresh)
	    {
	        $item = $this->_getAlias( $id );
	        $cache->store($item, $cache_key);
	    }

	    return $item;
	}

	private function _getAlias( $id )
	{
	    $db = JFactory::getDbo();
	    $query = $db->setQuery($db->getQuery(true)
	            ->select('category_alias')
	            ->from('#__citruscart_categories')
	            ->where('category_id='.(int) $id)
	    );
	    $alias = $db->loadResult();

	    return $alias;
	}

}
