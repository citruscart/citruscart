<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Sample::load( 'SampleTable', 'tables._base' );

class SampleTableConfig extends SampleTable
{

	function SampleTableConfig( &$db )
	{
		$tbl_key 	= 'config_name';
		$tbl_suffix = 'config';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= "sample";

		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}

	function store( $updateNulls = true)
	{
		$k = 'config_id';

        if (intval( $this->$k) > 0 )
        {
            $ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key );
        }
        else
        {
            $ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
        }
        if( !$ret )
        {
            $this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
            return false;
        }
        else
        {
            return true;
        }
	}

}