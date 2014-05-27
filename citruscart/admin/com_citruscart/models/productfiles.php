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

class CitruscartModelProductFiles extends CitruscartModelBase
{
	protected function _buildQueryWhere(&$query)
	{
		$filter          = $this->getState('filter');
		$filter_id	     = $this->getState('filter_id');
		$filter_enabled  = $this->getState('filter_enabled');
        $filter_product  = $this->getState('filter_product');
        $filter_product_name  = $this->getState('filter_product_name');
        $filter_file_name  = $this->getState('filter_file_name');
        $filter_date_from = $this->getState('filter_date_from');
        $filter_date_to = $this->getState('filter_date_to');
        $filter_purchaserequired  = $this->getState('filter_purchaserequired');

        $filter_productid  		= $this->getState('filter_productid');
        if (strlen($filter_productid)) {
            $filter_product = $filter_productid;
        }
                
        if ($filter) 
        {
        	$key    = $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter ) ) ).'%');
        	$where = array();
        	$where[] = 'LOWER(tbl.productfile_id) LIKE '.$key;
        	$where[] = 'LOWER(tbl.productfile_name) LIKE '.$key;
        	$where[] = 'LOWER(tbl.product_id) LIKE '.$key;
        	$where[] = 'LOWER(tbl_products.product_name) LIKE '.$key;
        	$query->where('('.implode(' OR ', $where).')');
        }
    
        if (strlen($filter_id))
        {
        	$query->where('tbl.productfile_id = '.(int) $filter_id);
        }
        if (strlen($filter_product))
        {
        	$query->where('tbl.product_id = '.(int) $filter_product);
        }
        if (strlen($filter_product_name))
        {
        	$key    = $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter_product_name ) ) ).'%');
        	$query->where('tbl_products.product_name LIKE '.$key );
        }
        if (strlen($filter_file_name))
        {
        	$key    = $this->_db->Quote('%'.$this->_db->escape( trim( strtolower( $filter_file_name ) ) ).'%');
        	$query->where('tbl.productfile_name LIKE '.$key );
        }
        if (strlen($filter_enabled))
        {
        	$query->where('tbl.productfile_enabled = '.(int) $filter_enabled);
        }
        if (strlen($filter_purchaserequired))
        {
        	$query->where('tbl.purchase_required = '.(int) $filter_purchaserequired);
        }
    
		if ( strlen( $filter_date_from ) )
		{
			$query->where( "tbl_downloads.productdownload_startdate >= '" . $filter_date_from . "'" );
		}
		
		if ( strlen( $filter_date_to ) )
		{
			$query->where( "tbl_downloads.productdownload_startdate <= '" . $filter_date_to . "'" );
		}
	}

    protected function _buildQueryJoins( &$query )
    {
        $query->join('LEFT', '#__citruscart_products AS tbl_products ON tbl.product_id = tbl_products.product_id');   
        $query->join('LEFT', '#__citruscart_productdownloads AS tbl_downloads ON tbl.productfile_id = tbl_downloads.productfile_id');   
    }
    
    protected function _buildQueryFields( &$query )
    {
    		$state = $this->getState();
    		$filter_date_from = $this->getState('filter_date_from');
    		$filter_date_to = $this->getState('filter_date_to');
    		$fields = array();
        $fields[] = " tbl.* ";
        // select the total downloads  
        
        $downloads = new CitruscartQuery();
        $downloads->select( 'SUM(tbl_downloads_tmp.`productdownload_max`)' );
        $downloads->from( '#__citruscart_productdownloads AS tbl_downloads_tmp' );
        $downloads->where( 'tbl_downloads_tmp.productfile_id = tbl.productfile_id' ); 
				if ( strlen( $filter_date_from ) )
				{
					$downloads->where( "tbl_downloads_tmp.productdownload_startdate >= '" . $filter_date_from . "'" );
				}
				if ( strlen( $filter_date_to ) )
				{
					$downloads->where( "tbl_downloads_tmp.productdownload_startdate <= '" . $filter_date_to . "'" );
				}
        
       	$fields[] = "
            ABS( (".$downloads.") ) 
        AS `file_downloads` ";    
      	 // select the product name
       	$fields[] = "tbl_products.product_name";
       	
        $query->select( $fields );
    }
    
    /**
     * Builds a GROUP BY clause for the query
     */
    protected function _buildQueryGroup(&$query)
    {
    	$query->group( 'tbl.productfile_id' );
    }

	public function getList($emptyState=true)
	{
		if( empty( $this->_list ) )
		{
			$list = parent::getList($emptyState);            
			// If no item in the list, return an array()
			if( empty( $list ) )
				return array();

			foreach( @$list as $item )
			{
				$item->productfile_path = $this->correctPath( $item->productfile_path );
			}
			$this->_list = $list;
		}
		return $this->_list;
	}
	
	public function getItem( $pk=null, $refresh=false, $emptyState=true )
	{
		if ( $item = parent::getItem( $pk, $refresh, $emptyState ) )
		{
			$item->productfile_path = $this->correctPath( $item->productfile_path );
		}

		return $item;
	}
	
	public function correctPath( $act_path )
	{
		$app = JFactory::getApplication();
		// relative paths start with DSDS
		if( strpos( $act_path, DS.DS ) === false ) // not a relative path
			return $act_path;
		else // relative path -> add JPATH_BASE before it
		{
			if( $app->isAdmin() ) //saving on admin site -> path_base contains "/Administrator"
				return substr( JPATH_BASE, 0, strlen( JPATH_BASE ) - 14 ).substr( $act_path, 1 );
			else
				return JPATH_BASE.substr( $act_path, 1 );
		}
	}
}
