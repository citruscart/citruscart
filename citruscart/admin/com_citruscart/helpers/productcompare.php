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

jimport( 'joomla.application.component.model' );
Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

class CitruscartHelperProductCompare extends CitruscartHelperBase
{	
	/**
	 * Method to check if we can still add a product to compare 
	 * @return boolean
	 */
	public function checkLimit()
	{
		$canAdd = true;
		$model = JModelLegacy::getInstance( 'ProductCompare', 'CitruscartModel');
		$user = JFactory::getUser();
        $model->setState( 'filter_user', $user->id ); 
        if (empty($user->id))
        {
        	$session = JFactory::getSession();
            $model->setState( 'filter_session', $session->getId() ); 
        }
       
        $total = $model->getTotal();
        $limit = Citruscart::getInstance()->get('compared_products', '5');
        
        if($total >= $limit)
        {
        	$canAdd = false;
        }        
        
        return $canAdd;
	}	
	
	public static function getComparedProducts()
	{
		$model = JModelLegacy::getInstance( 'ProductCompare', 'CitruscartModel');
		
	 	$user = JFactory::getUser();
        $model->setState( 'filter_user', $user->id ); 
        if (empty($user->id))
        {
        	$session = JFactory::getSession();
            $model->setState( 'filter_session', $session->getId() ); 
        }
             
       	$items = $model->getList();
       	
       	$itemsA = array();       	
       	foreach($items as $item)
       	{
       		$itemsA[] = $item->product_id;
       	}
       	
       	return $itemsA;	
	}	
	
    /**
     * Adds an item to the product compare
     * 
     * @param $item
     * @return unknown_type
     */
    public function addItem( $item )
    {
       	$session = JFactory::getSession();
        $user = JFactory::getUser();
        
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
        $table = JTable::getInstance( 'ProductCompare', 'CitruscartTable' );
        
        $keynames = array();
        $item->user_id = (empty($item->user_id)) ? $user->id : $item->user_id;
        $keynames['user_id'] = $item->user_id;
        if (empty($item->user_id))
        {
            $keynames['session_id'] = $session->getId();
        }
        $keynames['product_id'] = $item->product_id;
              
        if (!$table->load($keynames))
        {
        	foreach($item as $key=>$value)
            {
                if(property_exists($table, $key))
                {
                    $table->set($key, $value);
                }
            }
        }
              
        $date = JFactory::getDate();
        $table->last_updated = $date->toSql();
        $table->session_id = $session->getId();
        
        if (!$table->save())
        {
            JError::raiseNotice('updateProductCompare', $table->getError());
        }

        return $table;
    }
    	
	/**
	 * 
	 * @param unknown_type $user_id
	 * @param unknown_type $session_id
	 * @return unknown_type
	 */
	function updateUserProductComparedItemsSessionId( $user_id, $session_id )
	{
        $db = JFactory::getDbo();

        Citruscart::load( 'CitruscartQuery', 'library.query' );
        $query = new CitruscartQuery();
        
        $query->update( "#__citruscart_productcompare" );
        $query->set( "`session_id` = '$session_id' " );
        $query->where( "`user_id` = '$user_id'" );
        $db->setQuery( (string) $query );
        if (!$db->query())
        {
            $this->setError( $db->getErrorMsg() );
            return false;
        }
        return true;
	}
	
	/**
	 * 
	 * @param $session_id
	 * @return unknown_type
	 */
	function deleteSessionProductComparedItems( $session_id )
	{
        $db = JFactory::getDbo();

        Citruscart::load( 'CitruscartQuery', 'library.query' );
        $query = new CitruscartQuery();
        
        $query->delete();
        $query->from( "#__citruscart_productcompare" );
        $query->where( "`session_id` = '$session_id' " );
        $query->where( "`user_id` = '0'" );
        $db->setQuery( (string) $query );
        if (!$db->query())
        {
            $this->setError( $db->getErrorMsg() );
            return false;
        }
        return true;
	}
	
	/**
	 * 
	 * @param $session_id
	 * @param $user_id
	 * @return unknown_type
	 */
	function mergeSessionProductComparedWithUserProductCompared( $session_id, $user_id )
	{
	 	$date = JFactory::getDate();
	    $session = JFactory::getSession();
	    
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'ProductCompare', 'CitruscartModel' );
        $model->setState( 'filter_user', '0' );
        $model->setState( 'filter_session', $session_id );
        $session_compareditems = $model->getList();

		$this->deleteSessionProductComparedItems( $session_id );
        if (!empty($session_compareditems))
        {
            JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
           
            foreach ($session_compareditems as $session_compareditem)
            {      
            	$table = JTable::getInstance( 'ProductCompare', 'CitruscartTable' );
            	$keynames = array();
                $keynames['user_id'] = $user_id;
                $keynames['product_id'] = $session_compareditem->product_id;
                
            	if (!$table->load($keynames))
                {
                	$table->productcompare_id = '0';  
	                      
                }
                
                 $table->user_id = $user_id;
	             $table->product_id = $session_compareditem->product_id;
	             $table->session_id = $session->getId();
	             $table->last_updated = $date->toSql();
	                
	             if (!$table->save())
	             {
	             	JError::raiseNotice('updateCart', $table->getError());
	             } 
               
            }
        }        
	}	

	/**
	 * Remove the Item from product compare  
	 *
	 * @param  session id
	 * @param  user id
	 * @param  product id
	 * @return null
	 */
	function removeComparedItem( $session_id, $user_id=0, $product_id )
	{
		$db = JFactory::getDbo();

		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$query = new CitruscartQuery();
		$query->delete();
		$query->from( "#__citruscart_productcompare" );
		if (empty($user_id)) 
		{
			$query->where( "`session_id` = '$session_id' " );
		}
		$query->where( "`user_id` = '".$user_id."'" );
		
		$query->where( "`product_id` = '".$product_id."'" );
		
		$db->setQuery( (string) $query );

		// TODO Make this report errors and return boolean
		$db->query();

		return null;
	}
}
