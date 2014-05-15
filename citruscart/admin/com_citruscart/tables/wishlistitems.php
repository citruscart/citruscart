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

Citruscart::load( 'CitruscartTableEav', 'tables._baseeav' );

class CitruscartTableWishlistItems extends CitruscartTableEav 
{	
    function CitruscartTableWishlistItems ( &$db ) 
    {
        $keynames = array();
        $keynames['user_id']    = 'user_id';
        $keynames['product_id'] = 'product_id';
        $keynames['product_attributes'] = 'product_attributes';

        // load the plugins (when loading this table outside of Citruscart, this is necessary)
        JPluginHelper::importPlugin( 'citruscart' );
        
        //trigger: onGetAdditionalCartKeys
        
        $results = JFactory::getApplication()->triggerEvent( "onGetAdditionalCartKeys");
        if (!empty($results))
        {
        	foreach($results as $additionalKeys)
        	{
	        	foreach($additionalKeys as $key=>$value)
	        	{
					$keynames[$key] = $value;
		        }
        	}
		}
        
        $this->setKeyNames( $keynames );
    	
        $tbl_key      = 'wishlistitem_id';
        $tbl_suffix   = 'wishlistitems';
        $name         = 'citruscart';
        
        $this->set( '_tbl_key', $tbl_key );
        $this->set( '_suffix', $tbl_suffix );
        
        $this->_linked_table = 'products';
        $this->_linked_table_key_name = 'product_id';
        
        parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );    
    }
    
    function check()
    {        
        if (empty($this->product_id))
        {
            $this->setError( JText::_('COM_CITRUSCART_PRODUCT_REQUIRED') );
            return false;
        }
        
        // be sure that product_attributes is sorted numerically
        if ($product_attributes = explode( ',', $this->product_attributes ))
        {
            sort($product_attributes);
            $this->product_attributes = implode(',', $product_attributes);
        }
        
        return true;
    }

    /**
     * Loads a row from the database and binds the fields to the object properties
     * If $load_eav is true, binds also the eav fields linked to this entity
     *
     * @access	public
     * @param	mixed	Optional primary key.  If not specifed, the value of current key is used
     * @param	bool	reset the object values?
     * @param	bool	load the eav values for this object
     *
     * @return	boolean	True if successful
     */
    function load( $oid=null, $reset=true, $load_eav = true )
    {
        $this->_linked_table_key = $this->product_id;
        return parent::load( $oid, $reset, $load_eav );
    }
    
    /**
     * (non-PHPdoc)
     * @see Citruscart/admin/tables/CitruscartTable#delete($oid)
     */
    function delete( $oid='' )
    {
        if (empty($oid))
        {
            // if empty, use the values of the current keys
            $keynames = $this->getKeyNames();
            foreach ($keynames as $key=>$value)
            {
                $oid[$key] = $this->$key; 
            }
            if (empty($oid))
            {
                // if still empty, fail
                $this->setError( JText::_('COM_CITRUSCART_CANNOT_DELETE_WITH_EMPTY_KEY') );
                return false;
            }
        }
        
        if (!is_array($oid))
        {
            $keyName = $this->getKeyName();
            $arr = array();
            $arr[$keyName] = $oid; 
            $oid = $arr;
        }

        
        $before = JFactory::getApplication()->triggerEvent( 'onBeforeDelete'.$this->get('_suffix'), array( $this, $oid ) );
        if (in_array(false, $before, true))
        {
            return false;
        }
        
        $db = $this->getDBO();
        
        // initialize the query
        $query = new DSCQuery();
        $query->delete();
        $query->from( $this->getTableName() );
        
        foreach ($oid as $key=>$value)
        {
            // Check that $key is field in table
            if ( !in_array( $key, array_keys( $this->getProperties() ) ) )
            {
                $this->setError( get_class( $this ).' does not have the field '.$key );
                return false;
            }
            // add the key=>value pair to the query
            $value = $db->Quote( $db->escape( trim( strtolower( $value ) ) ) );
            $query->where( $key.' = '.$value);
        }

        $db->setQuery( (string) $query );

        if ($db->query())
        {
            
            JFactory::getApplication()->triggerEvent( 'onAfterDelete'.$this->get('_suffix'), array( $this, $oid ) );
            return true;
        }
        else
        {
            $this->setError($db->getErrorMsg());
            return false;
        }
    }
    
	function store($updateNulls = false)
	{
		$this->_linked_table_key = $this->product_id;
		return parent::store($updateNulls);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $values
	 * @param unknown_type $files
	 * @return return_type
	 */
	function addToCart( $values=array(), $files=array() )
	{
		// create cart object out of item properties
		$item = new JObject;
		$item->user_id = $this->user_id;
		$item->product_id = ( int ) $this->product_id;
		$item->product_qty = !empty($this->product_quantity) ? $this->product_quantity : '1';
		$item->product_attributes = $this->product_attributes;
		$item->vendor_id = $this->vendor_id;
		$item->cartitem_params = $this->wishlistitem_params;
		
		// onAfterCreateItemForAddToCart: plugin can add values to the item before it is being validated /added
		// once the extra field(s) have been set, they will get automatically saved
		$dispatcher = JDispatcher::getInstance( );
		$results = JFactory::getApplication()->triggerEvent( "onAfterCreateItemForAddToCart", array( $item, $values, $files ) );
		foreach ( $results as $result )
		{
			foreach ( $result as $key => $value )
			{
				$item->set( $key, $value );
			}
		}

		if (!$this->isAvailable())
		{
		    return false;
		}
		
		Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
		$product_helper = new CitruscartHelperProduct();
		$availableQuantity = $product_helper->getAvailableQuantity( $this->product_id, $this->product_attributes );
		if ( $availableQuantity->product_check_inventory && $item->product_qty > $availableQuantity->quantity )
		{
			$this->setError( JText::_( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $availableQuantity->product_name, $item->product_qty ) ) );
			return false;
		}
		
		Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
		$carthelper = new CitruscartHelperCarts( );
		
		// does the user/cart match all dependencies?
		$canAddToCart = $carthelper->canAddItem( $item, $this->user_id, 'user_id' );
		if ( !$canAddToCart )
		{
			$this->setError( JText::_('COM_CITRUSCART_CANNOT_ADD_ITEM_TO_CART') . " - " . $carthelper->getError( ) );
			return false;
		}
		
		// no matter what, fire this validation plugin event for plugins that extend the checkout workflow
		$results = array( );
		$dispatcher = JDispatcher::getInstance( );
		$results = JFactory::getApplication()->triggerEvent( "onBeforeAddToCart", array( &$item, $values ) );
		for ( $i = 0; $i < count( $results ); $i++ )
		{
			$result = $results[$i];
			if ( !empty( $result->error ) )
			{
    			$this->setError( JText::_('COM_CITRUSCART_CANNOT_ADD_ITEM_TO_CART') . " - " . $result->message );
    			return false;
			}
		}
		
		// if here, add to cart
		
		// After login, session_id is changed by Joomla, so store this for reference
		$session = JFactory::getSession( );
		$session->set( 'old_sessionid', $session->getId( ) );
		
		// add the item to the cart
		$cartitem = $carthelper->addItem( $item );
		
		// fire plugin event
		$dispatcher = JDispatcher::getInstance( );
		JFactory::getApplication()->triggerEvent( 'onAfterAddToCart', array( $cartitem, $values ) );
		
		return $cartitem;
	}
	
	public function isAvailable()
	{
		// create cart object out of item properties
		$item = new JObject;
		$item->user_id = $this->user_id;
		$item->product_id = ( int ) $this->product_id;
		$item->product_qty = !empty($this->product_quantity) ? $this->product_quantity : '1';
		$item->product_attributes = $this->product_attributes;
		$item->vendor_id = $this->vendor_id;
		$item->cartitem_params = $this->wishlistitem_params;
		
		DSCTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
		$product = DSCTable::getInstance( 'Products', 'CitruscartTable' );
		$product->load( array( 'product_id' => $this->product_id ) );
		
		if ( empty( $product->product_enabled ) || empty( $product->product_id ) )
		{
			$this->setError( JText::_('COM_CITRUSCART_INVALID_PRODUCT') );
			return false;
		}
		
		if ( $product->product_notforsale )
		{
			$this->setError( JText::_('COM_CITRUSCART_PRODUCT_NOT_FOR_SALE') );
			return false;
		}
		
        Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
	    $product_helper = new CitruscartHelperProduct();
		$availableQuantity = $product_helper->getAvailableQuantity( $item->product_id, $item->product_attributes );
		if ( $availableQuantity->product_check_inventory && $item->product_qty > $availableQuantity->quantity )
		{
		    $this->setError( JText::sprintf("COM_CITRUSCART_NOT_AVAILABLE_QUANTITY", $availableQuantity->product_name, $item->product_qty ) );
			return false;
		}
		
		$results = array( );
		$dispatcher = JDispatcher::getInstance( );
		$results = JFactory::getApplication()->triggerEvent( "onIsWishlistItemAvailable", array( &$item ) );
		for ( $i = 0; $i < count( $results ); $i++ )
		{
			$result = $results[$i];
			if ( !empty( $result->error ) )
			{
    			$this->setError( $result->message );
    			return false;
			}
		}
		
		return true;
	}
}
