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

if ( !class_exists('Citruscart') ) 
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

Citruscart::load( "CitruscartHelperBase", 'helpers._base' );

class CitruscartHelperSQL extends CitruscartHelperBase 
{
    /**
     * Processes a new order
     * 
     * @param $order_id
     * @return unknown_type
     */
    public function processOrder( $order_id ) 
    {
        // get the order
        $model = JModelLegacy::getInstance( 'Orders', 'CitruscartModel' );
        $model->setId( $order_id );
        $order = $model->getItem();
        $this->_orderFromModel = $order;
        
        $orderTable = $model->getTable();
        $orderTable->load( $order_id );
        $this->_order = $orderTable;
        
        $this->_date = JFactory::getDate();
        
		if ( $order->user_id < Citruscart::getGuestIdStart() ) {
			$this->_user = $order->user_id;
		} else {
	        $this->_user = JFactory::getUser( $order->user_id );
		}
        
        // find the products in the order that are integrated 
        foreach ($order->orderitems as $orderitem)
        {
            $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
            $product = $model->getTable();
            $product->load( $orderitem->product_id );

            $this->_product = $product;
            $this->_orderitem = $orderitem;
            
            if (!empty($product->product_sql))
            {
                $this->processSQL($product->product_sql);
            }
        }
    }
    
    /**
     * This method will convert the tags in the SQL string
     * and execute it
     * 
     * @param $sql
     * @return unknown_type
     */
    public function processSQL( $sql )
    {
        $regex = "#{orderitem.(.*?)}#s";
        $sql = preg_replace_callback( $regex, array($this, 'orderitem'), $sql );
        
        $regex = "#{order.(.*?)}#s";
        $sql = preg_replace_callback( $regex, array($this, 'order'), $sql );
        
        $regex = "#{user.(.*?)}#s";
        $sql = preg_replace_callback( $regex, array($this, 'user'), $sql );
        
        $regex = "#{product.(.*?)}#s";
        $sql = preg_replace_callback( $regex, array($this, 'product'), $sql );
        
        $regex = "#{date.(.*?)}#s";
        $sql = preg_replace_callback( $regex, array($this, 'date'), $sql );
        
        $regex = "#{request.(.*?)}#s";
        $sql = preg_replace_callback( $regex, array($this, 'request'), $sql );
        
        if (trim($sql)) 
        {
            $db = JFactory::getDBO();
            $db->setQuery($sql);
            if (!$db->query())
            {
                // TODO log error
                //JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'notice');
            }            
        }
    }
    
    /**
     * Process the order object strings
     * 
     * @param $match
     * @return unknown_type
     */
    protected function order( $match )
    {
        // regex returns this array:
        // $match[0] = {order.order_id}
        // $match[1] = order_id       

        $key = $match[1];

        if (isset($this->_order->$key))
        {
            $return = $this->_order->$key; 
        }
            else
        {
            $return = "{order.$key}";
        }
        
        return $return;
    }
    
    /**
     * Process the user object strings
     * 
     * @param $match
     * @return unknown_type
     */
    protected function user( $match )
    {
        // regex returns this array:
        // $match[0] = {user.id}
        // $match[1] = id       

        $key = $match[1];

        if (isset($this->_user->$key))
        {
            $return = $this->_user->$key; 
        }
            else
        {
            $return = "{user.$key}";
        }
        
        return $return;
    }
    
    /**
     * Process the product object strings
     * 
     * @param $match
     * @return unknown_type
     */
    protected function product( $match )
    {
        $key = $match[1];

        if (isset($this->_product->$key))
        {
            $return = $this->_product->$key; 
        }
            else
        {
            $return = "{product.$key}";
        }
        
        return $return;
    }
    
    /**
     * Process the orderitem object strings
     * 
     * @param $match
     * @return unknown_type
     */
    protected function orderitem( $match )
    {
        $key = $match[1];

        if (isset($this->_orderitem->$key))
        {
            $return = $this->_orderitem->$key; 
        }
            else
        {
            $return = "{orderitem.$key}";
        }
        
        return $return;
    }
    
    /**
     * Process the date strings
     * 
     * @param $match
     * @return unknown_type
     */
    protected function date( $match )
    {
        $key = $match[1];

        if (strpos($key, '(') && method_exists($this->_date, substr( $key, 0, -2 ) ))
        {
            $key = substr( $key, 0, -2 );
            $return = $this->_date->$key(); 
        }
            elseif (isset($this->_date->$key))
        {
            $return = $this->_date->$key; 
        }
            else
        {
            $return = "{date.$key}";
        }
        
        return $return;
    }
    
    /**
     * Process the request strings
     * 
     * @param $match
     * @return unknown_type
     */
    protected function request( $match )
    {
    	/* Get the applicaiton */
    	$app = JFactory::getApplication();
        $key = $match[1];

        $return = $app->input->get( $key );
        //$return = JRequest::getVar( $key );
        
        return $return;
    }
}
