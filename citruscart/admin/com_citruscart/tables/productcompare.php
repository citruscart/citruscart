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

class CitruscartTableProductCompare extends CitruscartTableEav 
{
    /**
     * @param $db
     * @return unknown_type
     */
    function CitruscartTableProductCompare ( &$db ) 
    {
        $keynames = array();
        $keynames['user_id']    = 'user_id';
        $keynames['session_id'] = 'session_id';
        $keynames['product_id'] = 'product_id';
        
        // load the plugins (when loading this table outside of Citruscart, this is necessary)
        JPluginHelper::importPlugin( 'citruscart' );
       
        $this->setKeyNames( $keynames );
    	
        $tbl_key      = 'productcompare_id';
        $tbl_suffix   = 'productcompare';
        $name         = 'citruscart';
        
        $this->set( '_tbl_key', $tbl_key );
        $this->set( '_suffix', $tbl_suffix );
        
        $this->_linked_table = 'products';
        
        parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );    
    }
    
    function check()
    {        
        if (empty($this->user_id) && empty($this->session_id))
        {
            $this->setError( JText::_('COM_CITRUSCART_USER_OR_SESSION_REQUIRED') );
            return false;
        }
        
        if (empty($this->product_id))
        {
            $this->setError( JText::_('COM_CITRUSCART_PRODUCT_REQUIRED') );
            return false;
        }        
    
        return true;
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
        $query = new CitruscartQuery();
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
            $value = $db->q( $db->escape( trim( strtolower( $value ) ) ) );
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
    
	function store( $updateNulls=false )
	{
		$this->_linked_table_key = $this->product_id;
		return parent::store();
	}
}
