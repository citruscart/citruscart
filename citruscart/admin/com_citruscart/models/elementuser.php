<?php
/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.'/libraries/dioscouri/library/model/element.php');

class CitruscartModelElementUser extends DSCModelElement
{
    var $title_key = 'name';
    var $select_title_constant = 'COM_CITRUSCART_SELECT_A_USER';
    var $select_constant = 'COM_CITRUSCART_SELECT';
    var $clear_constant = 'COM_CITRUSCART_CLEAR_SELECTION';

    function getTable($name='', $prefix=null, $options = array())
    {
        $table = JTable::getInstance('User', 'DSCTable');
        return $table;
    }
    function fetchElement($name, $value='', $control_name='', $js_extra='', $fieldName='' )
    {
    	
    	$doc = JFactory::getDocument();
    
    	if (empty($fieldName)) {
    		$fieldName = $control_name ? $control_name.'['.$name.']' : $name;
    	}
    
    	if ($value)
    	{
    		$app = JFactory::getApplication();
    		 
    		$view = $app->input->getString('view');
    		   		 
    		$table = JTable::getInstance($view,'CitruscartTable');
    		 
    		//$table = $this->getTable();
    		$table->load($value);
    		$title_key = $this->title_key;
    		$title = $table->$title_key;
    	}
    	else
    	{
    		$title = JText::_($this->select_title_constant);
    	}
    
    	$close_window = '';
    	if(version_compare(JVERSION,'1.6.0','ge')) {
    		$close_window = "window.parent.SqueezeBox.close();";
    	} else {
    		$close_window = "document.getElementById('sbox-window').close();";
    	}
    
    	$js = "Dsc.select" . $this->getName() . " = function(id, title, object) {
    	document.getElementById(object + '_id').value = id;
    	document.getElementById(object + '_name').value = title;
    	document.getElementById(object + '_name_hidden').value = title;
    	$close_window
    	$js_extra
    }";
    $doc->addScriptDeclaration($js);
    
    if (!empty($this->option))
    {
    $option = $this->option;
    }
    else
    {
    $r = null;
    
    if (!preg_match('/(.*)Model/i', get_class($this), $r))
    	{
    	JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'));
    	}
    
    	$option = 'com_' . strtolower($r[1]);
    	}
    	$link = 'index.php?option='.$option.'&view='.$this->getName().'&tmpl=component&object='.$name;
    
    	JHTML::_('behavior.modal', 'a.modal');
    	$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
    	$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_($this->select_title_constant).'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.JText::_($this->select_constant).'</a></div></div>'."\n";
    	$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.$value.'" />';
    	$html .= "\n".'<input type="hidden" id="'.$name.'_name_hidden" name="'.$name.'_name_hidden" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" />';
       	return $html;
    }
}




