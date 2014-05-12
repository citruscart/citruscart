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

class CitruscartModelTaxclasses extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');

       	if ($filter) 
       	{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.tax_class_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.tax_class_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.tax_class_description) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
       	$id = $this->getState( 'tax_class_id' );
      if( strlen( $id ) )
      	$where []= ' tbl.tax_class_id = '.( int )$id; 
    }
    
    protected function _buildQueryFields(&$query)
    {
        $field = array();
        
        $field[] = "
            (
            SELECT 
                COUNT(rates.tax_rate_id)
            FROM
                #__citruscart_taxrates AS rates 
            WHERE 
                rates.tax_class_id = tbl.tax_class_id 
            ) 
        AS taxrates_assigned ";
        
        $query->select( $this->getState( 'select', 'tbl.*' ) );     
        $query->select( $field );
    }
        	
	public function getList($refresh = false)
	{
		//$list = parent::getList();		
		if (empty( $this->_list ))
		{
			$query = $this->getQuery(true);
				
			$this->_list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		
		$list = $this->_list;
						
		// If no item in the list, return an array()
        /*if( empty( $list ) ){
        	return array();
        }*/
		
		foreach($list as $item)
		{
			$item->link = 'index.php?option=com_citruscart&controller=taxclasses&view=taxclasses&task=edit&id='.$item->tax_class_id;
			$item->link_taxrates = 'index.php?option=com_citruscart&view=taxclasses&task=setrates&tmpl=component&id='.$item->tax_class_id;
		}
		return $list;
	}
}
