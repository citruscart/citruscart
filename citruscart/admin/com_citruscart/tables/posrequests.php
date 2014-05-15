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

class CitruscartTablePosRequests extends CitruscartTable 
{
	function CitruscartTablePosRequests ( &$db ) 
	{
		
		$tbl_key 	= 'pos_id';
		$tbl_suffix = 'posrequests';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{
		$nullDate	= $this->_db->getNullDate();
		if (empty($this->created_date) || $this->created_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->created_date = $date->toSql();
		}
		
		if (empty($this->salt))
		{
			$this->salt = $this->GenerateSalt( 12 );
		}
		
		$this->token = $this->CalculateHash($this);
		return true;
	}
	
	public function CalculateHash($item)
	{
		$sw = Citruscart::getInstance()->get("secret_word");
		return sha1($sw.$item->order_id.$item->pos_id.$item->token.$item->user_id.$item->mode);
	}
	
	public function GenerateSalt($len)
	{
		jimport("joomla.user.helper");
		return JUserHelper::genRandomPassword($len);
	}
}
