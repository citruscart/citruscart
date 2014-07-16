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

class SampleTableTools extends SampleTable
{
	/**
	 * Could this be abstracted into the base?
	 *
	 * @param $db
	 * @return unknown_type
	 */
	function SampleTableTools ( &$db )
	{
	    if(version_compare(JVERSION,'1.6.0','ge')) {
            // Joomla! 1.6+ code here
            $tbl_key 	= 'extension_id';
            $tbl_suffix = 'extensions';
        } else {
            // Joomla! 1.5 code here
            $tbl_key 	= 'id';
            $tbl_suffix = 'plugins';
        }

		$this->set( '_suffix', $tbl_suffix );
		$name 		= "sample";

		parent::__construct( "#__{$tbl_suffix}", $tbl_key, $db );
	}

	function check()
	{
		return true;
	}

}
