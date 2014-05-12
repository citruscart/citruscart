<?php
/**
 * @package	Citruscart
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2009 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelWishlists extends CitruscartModelBase
{
	protected function _buildQueryWhere(&$query)
	{
		$filter_user     = $this->getState('filter_user');
		$filter_session  = $this->getState('filter_session');
		$filter_date_from	= $this->getState('filter_date_from');
		$filter_date_to		= $this->getState('filter_date_to');
		$filter_name	= $this->getState('filter_name');
        $filter_ids	= $this->getState('filter_ids');
		$filter        = $this->getState('filter');
		$filter_any      = $this->getState('filter_any');
		$filter_all      = $this->getState('filter_all');
		$filter_privacy   = $this->getState( 'filter_privacy' );
		$filter_acccessible = $this->getState( 'filter_accessible', '' ); // all wishlist accessible to the user

		if( !empty( $filter ) ) {
			$key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $filter ) ) ) . '%' );
			
			$where = array( );
			$where[] = 'LOWER(tbl.wishlist_id) LIKE ' . $key;
			$where[] = 'LOWER(tbl.wishlist_name) LIKE ' . $key;
			
			$query->where( '(' . implode( ' OR ', $where ) . ')' );
		}
		
		if ( !empty( $filter_all ) )
		{
			$words = explode( ' ', $filter_all );
			foreach ($words as $word)
			{
				$key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $word ) ) ) . '%' );
				$where = array( );
				$where[] = 'LOWER(tbl.wishlist_id) LIKE ' . $key;
				$where[] = 'LOWER(tbl.wishlist_name) LIKE ' . $key;
				
				$query->where( '(' . implode( ' OR ', $where ) . ')' );                 
			}
		}
		
		if ( !empty( $filter_any ) )
		{
			$words = explode( ' ', $filter_any );
			$wheres = array( );
			foreach ($words as $word)
			{
				$key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $word ) ) ) . '%' );
				$where = array( );
				$where[] = 'LOWER(tbl.wishlist_id) LIKE ' . $key;
				$where[] = 'LOWER(tbl.wishlist_name) LIKE ' . $key;
				
				$wheres[] = '(' . implode( ' OR ', $where ) . ')';
			}
			
			if (!empty($wheres)) 
			{
				$stmt = '(' . implode( ' OR ', $wheres ) . ')';
				$query->where($stmt);
			}
		}
		
		if (strlen($filter_user))
		{
			$query->where('tbl.user_id = '.$this->_db->q($filter_user));
		}

		if (strlen($filter_session))
		{
			$query->where( "tbl.session_id = ".$this->_db->q($filter_session));
		}

		if (strlen($filter_date_from))
		{
			$query->where("tbl.last_updated >= '".$filter_date_from."'");
		}

		if (strlen($filter_date_to))
		{
			$query->where("tbl.last_updated <= '".$filter_date_to."'");
		}

		if (strlen($filter_name))
		{
			$key	= $this->_db->q('%'.$this->_db->escape( trim( strtolower( $filter_name ) ) ).'%');
			$query->where('LOWER(tbl.wishlist_name) LIKE '.$key);
		}

		if (!empty($filter_ids) && is_array($filter_ids))
        {
        	$query->where('tbl.wishlist_id IN('.implode(",", $filter_ids).')' );
        }

		if( !empty( $filter_acccessible ) ) {
		
		if( empty( $filter_user ) ) {
			$query->where( 'tbl.privacy = 1 ' );
			} else {
				$query->where( 
					'( ( tbl.privacy = 1 ) '.
					' OR ( tbl.user_id = '.$this->_db->q( (int)$filter_user ).' ) )'
				);
			}
		}
	}
	
	public function addItem( $data )
	{
	    $table = $this->getTable('wishlistitems');
	    $table->bind($data);
	    if (!$table->save()) 
	    {
	        $this->setError( $table->getError() );
	        return false;
	    }
	    
	    return $table;
	}
	
	public function getItemid( $id, $fallback=null, $allow_null=false )
	{
	    Citruscart::load( 'CitruscartHelperRoute', 'helpers.route' );
        $this->router = new CitruscartHelperRoute();
	    
	    $return = $this->router->findItemid(array('view'=>'wishlists', 'task'=>'view', 'id'=>$id));
	    if (!$return) {
	        $return = $this->router->findItemid(array('view'=>'wishlists', 'task'=>'view'));
	        if (!$return) {
	            $return = $this->router->findItemid(array('view'=>'wishlists'));
	            if (!$return) {

	                if ($fallback) {
	                    $return = $fallback;
	                }

	                if (!$allow_null)
	                {
                        $return = JRequest::getInt('Itemid');

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
	    }

	    return $return;
	}
}