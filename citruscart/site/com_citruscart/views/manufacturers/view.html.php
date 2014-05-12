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
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base', array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_citruscart' ) );
Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
Citruscart::load( 'CitruscartUrl', 'library.url' );

class CitruscartViewManufacturers extends CitruscartViewBase  
{
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function display($tpl=null) 
	{
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "view":
				$this->_form($tpl);
			  break;
			case "form":
				$this->_form($tpl);
			  break;
			case "default":
			default:
				$this->_default($tpl);
			  break;
		}
		parent::display($tpl);
	}
}
