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

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableUserInfo extends CitruscartTable 
{
	function CitruscartTableUserInfo( &$db ) 
	{
		$tbl_key 	= 'user_info_id';
		$tbl_suffix = 'userinfo';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{
		if ($this->credits_withdrawable_total > $this->credits_total)
	    {
	        $this->credits_withdrawable_total = $this->credits_total;
	    }
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		$notnew = isset( $this->user_info_id );
		
		$old_record = JTable::getInstance( 'UserInfo', 'CitruscartTable' );
		$old_record->load( $this->user_info_id );
		$changed_sub_num = $old_record->sub_number != $this->sub_number;
		
		if( $notnew && $app->isSite() && $changed_sub_num && !( $user->usertype == 'Super Administrator'  ) )
		{
				$this->setError( JText::_('COM_CITRUSCART_YOU_DO_NOT_HAVE_ENOUGH_RIGHTS_TO_PERFORM_THIS_TASK') );
				return false;
		}
		return true;
	}
}
