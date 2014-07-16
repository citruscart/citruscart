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

require_once( JPATH_SITE.'/libraries/joomla/html/html/grid.php' );

class DSCGrid extends JHTMLGrid
{
	/**
	 * @param	string	The link title
	 * @param	string	The order field for the column
	 * @param	string	The current direction
	 * @param	string	The selected ordering
	 * @param	string	An optional task override
	 */
	public function sort( $title, $order, $direction = 'asc', $selected = 0, $form='document.adminForm' )
	{
		$direction	= strtolower( $direction );
		$images		= array( 'sort_asc.png', 'sort_desc.png' );
		$alts       = array( '&#9650;', '&#9660;' );
		$index		= intval( $direction == 'desc' );
		$direction	= ($direction == 'desc') ? 'asc' : 'desc';

		$html = '<a href="javascript:Dsc.gridOrdering(\''.$order.'\',\''.$direction.'\', '.$form.' );" title="'.JText::_( 'LIB_DSC_CLICK_TO_SORT_BY_THIS_COLUMN' ).'">';
		$html .= JText::_( $title );
		if ($order == $selected ) {
		    $html .= '<img src="'. DSC::getURL('images'). $images[$index] .'" border="0" alt="'. $alts[$index] .'" class="dsc-grid-sort" />';
		}
		$html .= '</a>';
		return $html;
	}

	/**
	 * @param   integer State Value
	 * @param   string Search button value
	 * @param   string Reset Button Vaue
	 * @param   string The name of the form element
	 *
	 * @return  HTML
	 */
	public static function searchform($value = '', $search = "Search", $reset = "Reset", $class = "unstyled dsc-flat pad-left")
	{
		$html = '<ul class="'.$class.'">
            <li>
                <input class="search-query" type="text" name="filter" value="'.$value.'" />
            </li>
            <li>
                <button class="btn btn-warning" onclick="this.form.submit();">'.$search.'</button>
            </li>
            <li>
                <button class="btn btn-danger" onclick="Dsc.resetFormFilters(this.form);">'.$reset.'</button>
            </li>
        </ul>';

	    return $html;
	}
	/**
	 * @param   integer The row index
	 * @param   integer The record id
	 * @param   boolean
	 * @param   string The name of the form element
	 *
	 * @return  string
	 */
	public function id($rowNum, $recId, $checkedOut=false, $name='cid')
	{
		if ($checkedOut) {
			return '';
		}
		else {
			return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="isChecked(this.checked);" title="'.JText::sprintf('JGRID_CHECKBOX_ROW_N', ($rowNum + 1)).'" />';
		}
	}

	/**
	 *
	 * @param $id
	 * @return unknown_type
	 */
	public function order($id, $image = 'filesave.png', $task = 'saveorder', $form='document.adminForm')
	{
		$up   = 'uparrow.png'; $up_title = JText::_("Move Up");
		$down = 'downarrow.png'; $down_title = JText::_("Move Down");

		$result =
			'<a href="javascript:Dsc.gridOrder('.$id.', -1, '.$form.')" >'
			.'<img src="'. DSC::getURL('images'). $up .'" border="0" alt="'. $up_title .'" />'
			.'</a>'
			.'<a href="javascript:Dsc.gridOrder('.$id.', 1, '.$form.')" >'
			.'<img src="'. DSC::getURL('images'). $down .'" border="0" alt="'. $down_title .'" />'
			.'</a>';

		return $result;
	}

	/**
	 *
	 * @param $id
	 * @param $value
	 * @return unknown_type
	 */
	public function ordering( $id, $value)
	{
		$result =
			 '
			 <input type="text"
			 name="ordering['.$id.']"
			 size="5"
			 value="'.$value.'"
			 class="text_area"
			 style="text-align: center"
			 />
			 ';

		return $result;
	}

	/**
	 * Shows a true/false graphics
	 *
	 * @param	bool	Value
	 * @param 	string	Image for true
	 * @param 	string	Image for false
	 * @param 	string 	Text for true
	 * @param 	string	Text for false
	 * @return 	string	Html img
	 */
	public function boolean( $bool, $true_img = null, $false_img = null, $true_text = null, $false_text = null)
	{
		$true_img 	= $true_img 	? $true_img 	: 'tick.png';
		$false_img 	= $false_img	? $false_img	: 'publish_x.png';
		$true_text 	= $true_text 	? $true_text 	: 'Yes';
		$false_text = $false_text 	? $false_text 	: 'No';

		return '<img src="'. DSC::getURL('images'). ($bool ? $true_img : $false_img) .'" border="0" alt="'. JText::_($bool ? $true_text : $false_text) .'" />';
	}

	public function published( $row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= $row->published ? $imgY : $imgX;
		$task 	= $row->published ? 'unpublish' : 'publish';
		$alt 	= $row->published ? JText::_( 'Published' ) : JText::_( 'Unpublished' );
		$action = $row->published ? JText::_( 'Unpublish Item' ) : JText::_( 'Publish item' );

		$href = '
		<a href="javascript:void(0);" onclick="return Dsc.listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
		<img src="'. DSC::getURL('images').$img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}

	public function enable( $enable, $i, $prefix = '', $imgY = 'tick.png', $imgX = 'publish_x.png' )
	{

		$img 	= $enable ? $imgY : $imgX;
		$task 	= $enable ? 'disable' : 'enable';
		$alt 	= $enable ? JText::_( 'Enabled' ) : JText::_( 'Disabled' );
		$action = $enable ? JText::_( 'Disable Item' ) : JText::_( 'Enable Item' );

		$href = '
		<a href="javascript:void(0);" onclick="return Dsc.listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
		<img src="'. DSC::getURL('images').$img .'" border="0" alt="'. $alt .'" />
		</a>'
		;

		return $href;
	}

	public function checkedout( &$row, $i, $identifier = 'id' )
	{
		$user   = JFactory::getUser();
		$userid = $user->get('id');

		$result = false;
		if (!isset($row->checked_out))
		{
			$result = false;
		}
			elseif (is_a($row, 'JTable'))
		{
			$result = $row->isCheckedOut($userid);
		}
			else
		{
			$result = JTable::isCheckedOut($userid, $row->checked_out);
		}

		$checked = '';
		if ( $result )
		{
			if (isset($row->editor))
			{
				$checked = self::_checkedOut( $row );
			}
				else
			{
				$text = JFactory::getUser($row->checked_out)->username;
				$date = JHTML::_('date',  $row->checked_out_time, JText::_('DATE_FORMAT_LC1') );
				$time = JHTML::_('date',  $row->checked_out_time, '%H:%M' );
				$hover = '<span class="editlinktip hasTip" title="'. JText::_( 'Checked Out by' ) .' '. $text .' '.JText::_("on").' '. $date .' '.JText::_("at").' '. $time .'">';
				$checked = $hover .'<img src="'. DSC::getURL('images') . 'checked_out.png"/></span>';
			}

		}
			else
		{
			$checked = JHTML::_('grid.id', $i, $row->$identifier );
		}

		return $checked;
	}

	public function pagetooltip( $key, $title='Tip', $id='page_tooltip', $app=null )
	{

		$input = JFactory::getApplication()->input;
		$href = '';

		$constant = 'page_tooltip_'.$key;

		$app = $input->get( 'option' );
		$disabled = DSC::getApp( $app )->get( $constant."_disabled", '0');

		$lang = JFactory::getLanguage();
		if ($lang->hasKey($constant) && !$disabled)
		{
			$option = strtolower( $app );
			$view = strtolower( $input->getString('view') );
			$task = "page_tooltip_disable";
			$url = JRoute::_("index.php?option={$option}&controller={$view}&view={$view}&task={$task}&key={$key}");
			$link = "<a href='{$url}'>".JText::_("Hide This")."</a>";

			$href = '
				<fieldset class="'.$id.'">
					<legend class="'.$id.'">'.JText::_($title).'</legend>
					'.JText::_($constant).'
					<span class="'.$id.'" style="float: right;">'.$link.'</span>
				</fieldset>
			';
		}

		return $href;
	}

	public function checkoutnotice( $row, $title='Item', $lock_task='edit' )
	{
		if (!isset($row->checked_out))
		{
			return null;
		}

		if (JFactory::getUser()->id == $row->checked_out)
		{
			$html = "
			<div class='note'>
				".JText::_( "$title Checked Out By You" )."
				<button onclick='document.getElementById(\"task\").value=\"release\"; this.form.submit();'>".JText::_( "Release $title")."</button>
			</div>
			";
		}
			elseif (!empty($row->checked_out))
		{
			$html = "
			<div class='note'>
				".sprintf( JText::_( "$title Checked Out By Another" ), JFactory::getUser( $row->checked_out )->username )."
			</div>
			";
		}
			else
		{
			$html = "
			<div class='note'>
				".JText::_( "$title Checked Out By Nobody" )."
				<button onclick='document.getElementById(\"task\").value=\"$lock_task\"; this.form.submit();'>".JText::_( "Lock $title" )."</button>
			</div>
			";
		}

		return $html;
	}

	public function _checkedOut( &$row, $overlib = 1 )
	{
		$hover = '';
		if ( $overlib )
		{
			$text = addslashes(htmlspecialchars($row->editor));

			$date 	= JHTML::_('date',  $row->checked_out_time, JText::_('DATE_FORMAT_LC1') );
			$time	= JHTML::_('date',  $row->checked_out_time, '%H:%M' );

			$hover = '<span class="editlinktip hasTip" title="'. JText::_( 'Checked Out' ) .'::'. $text .'<br />'. $date .'<br />'. $time .'">';
		}
		$checked = $hover .'<img src="'. DSC::getURL('images') . 'checked_out.png"/></span>';

		return $checked;
	}

	public static function required( $text ='', $css_suffix = '' )
	{
		$css_class = 'dsc-required';
		if( strlen( $css_suffix ) )
			$css_class .= $css_suffix;

		$txt = 'LIB_DSC_REQUIRED';
		if( strlen( $text ) )
			$txt = $text;

	    $html = '<div class="'.$css_class.'" title="'.JText::_( $txt ).'"></div>';
        return $html;
	}
}