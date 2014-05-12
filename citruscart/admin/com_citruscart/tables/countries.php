<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/


/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTable', 'tables._base' );
require_once JPATH_ADMINISTRATOR.'/components/com_citruscart/library/query.php';
class CitruscartTableCountries extends CitruscartTable 
{
	function __construct ( &$db ) 
	{
		
		$tbl_key 	= 'country_id';
		$tbl_suffix = 'countries';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{		
		return true;
	}
	
    public function reorder($where = '')
    {
        $k = $this->_tbl_key;

        $query = new CitruscartQuery();
        $query->select( $this->_tbl_key );
        $query->select( 'ordering' );
        $query->from( $this->_tbl );
        $query->order( 'ordering ASC' );
        $query->order( 'country_name ASC' );

        $this->_db->setQuery( (string) $query );
        if (!($orders = $this->_db->loadObjectList()))
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        
        // correct all the ordering numbers
        for ($i=0, $n=count( $orders ); $i < $n; $i++)
        {
            if ($orders[$i]->ordering >= 0)
            {
                if ($orders[$i]->ordering != $i+1)
                {
                    $orders[$i]->ordering = $i+1;
                    
                    $query = new CitruscartQuery();
                    $query->update( $this->_tbl );
                    $query->set( 'ordering = '. (int) $orders[$i]->ordering );
                    $query->where( $k .' = '. $this->_db->Quote($orders[$i]->$k) );

                    $this->_db->setQuery( (string) $query );
                    $this->_db->query();
                }
            }
        }
        return true;
    }
}
