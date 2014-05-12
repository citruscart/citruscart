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

if ( !class_exists('Citruscart') ) 
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );



if(!class_exists('JFakeElementBase')) {
	if(version_compare(JVERSION,'1.6.0','ge')) {
		class JFakeElementBase extends JFormField {
			// This line is required to keep Joomla! 1.6/1.7 from complaining
			public function getInput() {
			}
		}
	} else {
		class JFakeElementBase extends JElement {}
	}
}

class JFakeElementCitruscartTaxClass extends JFakeElementBase
{
	var	$_name = 'CitruscartTaxClass';

	public function getInput() 
	{
		
		$list = Citruscart::getClass( 'CitruscartSelect', 'library.select' )->taxclass($this->value, $this->options['control'].$this->name, '', $this->options['control'].$this->name, false, false, 'COM_CITRUSCART_SELECT_TAX_CLASS', '', true );
		return $list;
	}
	
	public function fetchElement($name, $value, &$node, $control_name)
	{
		
	    $list = Citruscart::getClass( 'CitruscartSelect', 'library.select' )->taxclass($value, $control_name.'['.$name.']', '', $control_name.$name, false, false, 'COM_CITRUSCART_SELECT_TAX_CLASS', '', true );
		return $list;
	}
	
	
	
}

if(version_compare(JVERSION,'1.6.0','ge')) {
	class JFormFieldCitruscartTaxClass extends JFakeElementCitruscartTaxClass {}
} else {
	class JElementCitruscartTaxClass extends JFakeElementCitruscartTaxClass {}
}
