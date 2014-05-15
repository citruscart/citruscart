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

class JFakeElementCitruscartProduct extends JFakeElementBase
{
var	$_name = 'CitruscartProduct';

	public function getInput()
	{
		return JFakeElementCitruscartProduct::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
	}


	public function fetchElement($name, $value, &$node, $control_name)
	{

		$html = "";
		$doc 		= JFactory::getDocument();
		$fieldName	= $control_name ? $control_name.'['.$name.']' : $name;
		$title = JText::_('COM_CITRUSCART_SELECT_PRODUCTS');
		if ($value) {
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_citruscart/tables');
			$table = JTable::getInstance('Products', 'CitruscartTable');
			$table->load($value);
			$title = $table->product_name;
		}
		else
		{
			$title=JText::_('COM_CITRUSCART_SELECT_A_PRODUCT');
		}

 		$js = "
			Dsc.selectelementproduct = function(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;";
		if(version_compare(JVERSION,'1.6.0','ge')) {
			$js .= 'window.parent.SqueezeBox.close()';
		}
		else {
			$js .= 'document.getElementById(\'sbox-window\').close()';
		}
	$js.=	"}";

		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_citruscart&controller=elementproduct&view=elementproduct&tmpl=component&object='.$name;

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('COM_CITRUSCART_SELECT_A_PRODUCT').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.JText::_('COM_CITRUSCART_SELECT').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}



}

if(version_compare(JVERSION,'1.6.0','ge')) {
	class JFormFieldCitruscartProduct extends JFakeElementCitruscartProduct {}
} else {
	class JElementCitruscartProduct extends JFakeElementCitruscartProduct {}
}
