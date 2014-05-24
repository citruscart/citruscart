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
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelEavAttributes extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
        $filter_name    = $this->getState('filter_name');
        $filter_enabled  = $this->getState('filter_enabled');
        $filter_entitytype  = $this->getState('filter_entitytype');
        $filter_entityid  = $this->getState('filter_entityid');
        $filter_editable = $this->getState( 'filter_editable' );

       	if ($filter)
       	{
       	    $key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
       	    $where = array();
       	    $where[] = 'LOWER(tbl.eavattribute_id) LIKE '.$key;
       	    $where[] = 'LOWER(tbl.eavattribute_label) LIKE '.$key;
       	    $query->where('('.implode(' OR ', $where).')');
       	}
        if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
            {
                $query->where('tbl.eavattribute_id >= '.(int) $filter_id_from);
            }
            else
            {
                $query->where('tbl.eavattribute_id = '.(int) $filter_id_from);
            }
        }
        if (strlen($filter_id_to))
        {
            $query->where('tbl.eavattribute_id <= '.(int) $filter_id_to);
        }
        if ($filter_name)
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.eavattribute_label) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        if ($filter_entitytype)
        {
            $key    = $this->_db->q($this->_db->escape( trim( strtolower( $filter_entitytype ) ) ));
            echo $key;
            $where = array();
            $where[] = 'LOWER(tbl.eaventity_type) LIKE '.$key;
            $where[] = 'LOWER(a2e.eaventity_type) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        if (strlen($filter_entityid))
        {
            $where = array();
            $where[] = 'tbl.eaventity_id = '.$this->_db->q($filter_entityid);
            $where[] = 'a2e.eaventity_id = '.(int) $filter_entityid;
            $query->where('('.implode(' OR ', $where).')');
        }
        if (strlen($filter_enabled))
        {
            $query->where('tbl.enabled = '.$this->_db->q($filter_enabled));
        }

        if( strlen( $filter_editable ) )
        {
            $query->where('tbl.editable_by IN ('.$filter_editable.' )' );
        }
    }

    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__citruscart_eavattributeentityxref AS a2e ON tbl.eavattribute_id = a2e.eavattribute_id');
    }
        
    protected function _buildQueryGroup(&$query)
    {
    	$query->group('tbl.eavattribute_id');
    }
    
    protected function _buildQueryFields(&$query)
    {
    	$field = array();
    	$field[] = "
    	(
    	SELECT
    	COUNT(xref.eaventity_id)
    	FROM
    	#__citruscart_eavattributeentityxref AS xref
    	WHERE
    	xref.eavattribute_id = tbl.eavattribute_id
    	)
    	AS entity_count ";
    	$query->select( $this->getState( 'select', 'tbl.*' ) );
    	$query->select( $field );
    	
    }
    

    public function getList($refresh = false)
    {
        //$list = parent::getList($refresh);
        
        if (empty( $this->_list ) || $reload)
        {
        	$query = $this->getQuery(true);
        		
        	$this->_list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
        }
        $list = $this->_list;
            
        foreach($list as $item)
        {
            $item->link = 'index.php?option=com_citruscart&controller=eavattributes&view=eavattributes&task=edit&id='.$item->eavattribute_id;
        }
        return $list;
    }

}
