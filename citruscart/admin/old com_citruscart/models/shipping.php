<?php
/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelShipping extends CitruscartModelBase
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
        $filter_name    = $this->getState('filter_name');
        $filter_enabled    = $this->getState('filter_enabled');

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
        if (strlen($filter_enabled))
        {
            if(version_compare(JVERSION,'1.6.0','ge')) {
                // Joomla! 1.6+ code here
                $query->where('tbl.enabled = 1');
            } else {
                // Joomla! 1.5 code here
                $query->where('tbl.published = 1');
            }
            	
             
        }
        if ($filter_name)
        {
            $key    = $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.name) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }

        // force returned records to only be Citruscart shipping
        $query->where("tbl.folder = 'Citruscart'");
        $query->where("tbl.element LIKE 'shipping_%'");

    }
     
    public function getList($refresh = false)
    {
        //$list = parent::getList($refresh);
        if (empty( $this->_list ))
        {
        	$query = $this->getQuery(true);
        		
        	$this->_list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
        }
        $list = $this->_list;        
        
        foreach($list as $item)
        {
            if(version_compare(JVERSION,'1.6.0','ge')) {
                // Joomla! 1.6+ code here
                $item->id = $item->extension_id;
            }
            	
            $item->link = 'index.php?option=com_citruscart&view=shipping&task=view&id='.$item->id;
            $item->link_edit = 'index.php?option=com_citruscart&view=shipping&task=edit&id='.$item->id;
        }
        return $list;
    }

    public function getItem($pk=null, $refresh=false, $emptyState=true)
    {
        if ($item = parent::getItem($pk, $refresh, $emptyState))
        {
            // Convert the params field to an array.
            if (version_compare(JVERSION, '1.6.0', 'ge')) {
                $formdata = new JRegistry;
                $formdata -> loadString($item -> params);
                $item -> data = $formdata -> toArray('data');
            }
        }
        return $item;
    }
}
