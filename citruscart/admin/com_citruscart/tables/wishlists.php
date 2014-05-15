<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableWishlists extends CitruscartTable 
{
	function CitruscartTableWishlists ( &$db ) 
	{
		$tbl_key 	= 'wishlist_id';
		$tbl_suffix = 'wishlists';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
    
    function check()
    {        
        if (empty($this->user_id))
        {
            $this->setError( JText::_('COM_CITRUSCART_USER_REQUIRED') );
        }

        if (empty($this->wishlist_name))
        {
            // count the number of lists for this user
            $query = "SELECT COUNT(*) FROM #__citruscart_wishlists WHERE user_id = '" . (int) $this->user_id . "'";
            $db = $this->getDBO();
            $db->setQuery( $query );
            $count = $db->loadResult();
            
            $this->wishlist_name = "List " . ($count+1);
        }
        
        return parent::check();
    }
    
    function delete( $oid=null, $doReconciliation=true )
    {
        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = intval( $oid );
        }
        
        $id = $this->$k; 
        
        if ($return = parent::delete( $oid ))
        {
            if ($id) 
            {
                $query = "UPDATE #__citruscart_wishlistitems SET wishlist_id = '0' WHERE wishlist_id = '". $id . "'";
                $db = $this->getDBO();
                $db->setQuery( $query );
                $db->query();                
            }
        }
        
        return parent::check();
    }
}
