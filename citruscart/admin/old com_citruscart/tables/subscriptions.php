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

class CitruscartTableSubscriptions extends CitruscartTable 
{
	function CitruscartTableSubscriptions ( &$db ) 
	{
		
		$tbl_key 	= 'subscription_id';
		$tbl_suffix = 'subscriptions';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
    function check()
    {
        $nullDate   = $this->_db->getNullDate();

        if (empty($this->created_datetime) || $this->created_datetime == $nullDate)
        {
            $date = JFactory::getDate();
            $this->created_datetime = $date->toSql();
        }       
        return true;
    }
    
    function save($src='', $orderingFilter = '', $ignore = '')
    {
        $prev = clone( $this );
        if (!empty($this->id)) { $prev->load( $this->id ); }
        
        if ($save = parent::save($src,$orderingFilter,$ignore))
        {
            if ($prev->subscription_enabled && empty($this->subscription_enabled))
            {
                // if it was previously enabled and now is disabled
                Citruscart::load( 'CitruscartHelperJuga', 'helpers.juga' );
                $helper = new CitruscartHelperJuga();
                $helper->doExpiredSubscription( $this );
            }
        }
        
        return $save;
    }
}
