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

Citruscart::load( 'CitruscartTableXref', 'tables._basexref' );

class CitruscartTableProductCategories extends CitruscartTableXref
{

	/**
	 * @param $db
	 * @return unknown_type
	 */
	function __construct( $db )
	{
		$keynames = array();
		$keynames['product_id']  = 'product_id';
        $keynames['category_id'] = 'category_id';
        $this->setKeyNames( $keynames );

        $this->_keynames = $keynames;

		$tbl_key 	= 'product_id';
		$tbl_suffix = 'productcategoryxref';
		$name 		= 'citruscart';
		$this->set( '_tbl_key', $tbl_key );
		$this->set( '_suffix', $tbl_suffix );

		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );

	}

	function check()
	{
		if (empty($this->category_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_CATEGORY_REQUIRED') );
			return false;
		}
		if (empty($this->product_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_PRODUCT_REQUIRED') );
			return false;
		}

		return true;
	}
}
