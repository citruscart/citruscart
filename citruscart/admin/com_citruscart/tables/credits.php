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

class CitruscartTableCredits extends CitruscartTable
{
	function __construct( &$db )
	{
		$tbl_key 	= 'credit_id';
		$tbl_suffix = 'credits';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';

		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}

    function check()
    {
        $nullDate   = $this->_db->getNullDate();
        if (empty($this->created_date) || $this->created_date == $nullDate)
        {
            $date = JFactory::getDate();
            $this->created_date = $date->toSql();
        }
        
        $date = JFactory::getDate();
        $this->modified_date = $date->toSql();
        
        return true;
    }
    
    function save($src='', $orderingFilter = '', $ignore = '')
    {
        $isNew = false;
        if (empty($this->credit_id))
        {
            $isNew = true;
        }
        
        $prev = clone( $this );
        $prev->load( $this->id );
        $wasWithdrawableBefore = $prev->credit_withdrawable;
        $wasEnabledBefore = $prev->credit_enabled;
        
        if ($save = parent::save($src, $orderingFilter, $ignore))
        {
            if (
               (($isNew && $this->credit_enabled) // if this is a new credit, and it is enabled 
               || (!$isNew && $this->credit_enabled)) // or if this is an existing credit that's now being enabled
               && empty($this->credits_updated) // and the credits haven't already been updated
               )
            {
                // add the credits to the total
                $userdata = JTable::getInstance( 'UserInfo', 'CitruscartTable' );
                $userdata->load( array( 'user_id' => $this->user_id ) );
                $userdata->user_id = $this->user_id;
                
                $credit_balance_before = $userdata->credits_total;
                $withdrawable_balance_before = $userdata->credits_withdrawable_total;                
                $userdata->credits_total = $userdata->credits_total + $this->credit_amount;
                if ($this->credit_withdrawable)
                {
                    $userdata->credits_withdrawable_total = $userdata->credits_withdrawable_total + $this->credit_amount;
                }
                
                if (!$userdata->save())
                {
                    $this->setError( $userdata->getError() );
                }
                    else
                {
                    $this->credits_updated = '1';
                    $this->credit_balance_before = $credit_balance_before;
                    $this->credit_balance_after = $userdata->credits_total;
                    $this->withdrawable_balance_before = $withdrawable_balance_before;
                    $this->withdrawable_balance_after = $userdata->credits_withdrawable_total;
                    parent::store();
                }
            }
                elseif ( !$isNew && !$this->credit_enabled && !empty($this->credits_updated) )
            {
                // if this is an existing credit that is now being disabled,
                // remove the credits from the total
                $userdata = JTable::getInstance( 'UserInfo', 'CitruscartTable' );
                $userdata->load( array( 'user_id' => $this->user_id ) );
                $userdata->user_id = $this->user_id;
                $userdata->credits_total = $userdata->credits_total - $this->credit_amount;
                if ($this->credit_withdrawable)
                {
                    $userdata->credits_withdrawable_total = $userdata->credits_withdrawable_total - $this->credit_amount;
                }
                
                if (!$userdata->save())
                {
                    $this->setError( $userdata->getError() );
                }
                    else
                {
                    $this->credits_updated = '0';
                    $this->credit_balance_before = 0;
                    $this->credit_balance_after = 0;
                    $this->withdrawable_balance_before = 0;
                    $this->withdrawable_balance_after = 0;
                    parent::store();
                }
            }
            
            if ( !$isNew && $this->credit_enabled && !empty($this->credits_updated) && $this->credit_withdrawable && !$wasWithdrawableBefore )
            {
                // if this is an existing credit that is enabled
                // and it has already updated the credits_total for the user
                // but is now being changed to a withdrawable amount,
                // then we need to adjust the user's withdrawable total

                $userdata = JTable::getInstance( 'UserInfo', 'CitruscartTable' );
                $userdata->load( array( 'user_id' => $this->user_id ) );
                $userdata->user_id = $this->user_id;
                
                $withdrawable_balance_before = $userdata->credits_withdrawable_total;
                $userdata->credits_withdrawable_total = $userdata->credits_withdrawable_total + $this->credit_amount;
                
                if ($userdata->save())
                {
                    $this->withdrawable_balance_before = $withdrawable_balance_before;
                    $this->withdrawable_balance_after = $userdata->credits_withdrawable_total;
                    parent::store();
                }
            }
            
            if ( !$isNew && !empty($this->credits_updated) && !$this->credit_withdrawable && $wasWithdrawableBefore && $wasEnabledBefore)
            {
                // if this is an existing credit
                // and it has already updated the credits_total for the user
                // but is now being changed to a non-withdrawable amount,
                // then we need to adjust the user's withdrawable total

                $userdata = JTable::getInstance( 'UserInfo', 'CitruscartTable' );
                $userdata->load( array( 'user_id' => $this->user_id ) );
                $userdata->user_id = $this->user_id;
                
                $withdrawable_balance_before = $userdata->credits_withdrawable_total;
                $userdata->credits_withdrawable_total = $userdata->credits_withdrawable_total - $this->credit_amount;
                
                if ($userdata->save())
                {
                    $this->withdrawable_balance_before = $withdrawable_balance_before;
                    $this->withdrawable_balance_after = $userdata->credits_withdrawable_total;
                    parent::store();
                }
            }
        }
        return $save;
    }
    
    /**
     * 
     * @param $oid
     * @return unknown_type
     */
    function delete( $oid=null )
    {
        // if this record changed the user credits, then change them back
        $this->load( $oid );
        if (!empty($this->credits_updated))
        {
            // this record cause a credit change for the user
            // so deduct their value from the users total
            $userdata = JTable::getInstance( 'Userdata', 'CitruscartTable' );
            $userdata->load( array( 'user_id' => $this->user_id ) );
            $userdata->user_id = $this->user_id;
            $userdata->credits_total = $userdata->credits_total - $this->credit_amount;
            if ($this->credit_withdrawable)
            {
                $userdata->credits_withdrawable_total = $userdata->credits_withdrawable_total - $this->credit_amount;
            }
                        
            if (!$userdata->save())
            {
                $this->setError( $userdata->getError() );
            }
        }
        
        return parent::delete( $oid );
    }
}
