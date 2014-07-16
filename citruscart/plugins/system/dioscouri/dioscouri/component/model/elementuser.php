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

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');

/**
 * Content Component User Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since		1.5
 */
class SampleModelElementUser extends JModel
{
	/**
	 * Content data in category array
	 *
	 * @var array
	 */
	var $_list = null;

	var $_page = null;

	/**
	 * Method to get content article data for the frontpage
	 *
	 * @since 1.5
	 */
	function getList()
	{
		$where = array();
		$mainframe = JFactory::getApplication();

		if (!empty($this->_list)) {
			return $this->_list;
		}

		// Initialize variables
		$db		=$this->getDBO();
		$filter	= null;

		// Get some variables from the request
		$sectionid			= $mainframe->input->getInt( 'sectionid', -1);
		$redirect			= $sectionid;
		$option				= $mainframe->input->get( 'option' );
		$filter_order		= $mainframe->getUserStateFromRequest('userelement.filter_order',		'filter_order',		'',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest('userelement.filter_order_Dir',	'filter_order_Dir',	'',	'word');
		$limit				= $mainframe->getUserStateFromRequest('global.list.limit',					'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart			= $mainframe->getUserStateFromRequest('userelement.limitstart',			'limitstart',		0,	'int');
		$search				= $mainframe->getUserStateFromRequest('userelement.search',				'search',			'',	'string');
		$search				= JString::strtolower($search);

		if (!$filter_order) {
			$filter_order = 'id';
		}
		$order = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$all = 1;

		// Keyword filter
		if ($search) {
			$where[] = 'LOWER( c.id ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( c.name ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( c.username ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( c.email ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
		}
		// Build the where clause of the query
		$where = (count($where) ? ' WHERE '.implode(' OR ', $where) : '');

		// Get the total number of records
		$query = 'SELECT COUNT(*)' .
				' FROM #__users AS c' .
				$where;
		$db->setQuery($query);
		$total = $db->loadResult();

		// Create the pagination object
		jimport('joomla.html.pagination');
		$this->_page = new JPagination($total, $limitstart, $limit);

		// Get the users
		$query = 'SELECT c.*' .
				' FROM #__users AS c' .
				$where .
				$order;
		$db->setQuery($query, $this->_page->limitstart, $this->_page->limit);
		$this->_list = $db->loadObjectList();

		// If there is a db query error, throw a HTTP 500 and exit
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		return $this->_list;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function getPagination()
	{
		if (is_null($this->_list) || is_null($this->_page)) {
			$this->getList();
		}
		return $this->_page;
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function _fetchElement($name, $value='', $node='', $control_name='')
	{
		$mainframe = JFactory::getApplication();

		$db			= JFactory::getDbo();
		$doc 		= JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name ? $control_name.'['.$name.']' : $name;

		if ($value) {
			$user = JFactory::getUser( $value );
			$title = $user->username;
		} else {
			$title = JText::_('Select a User');
		}

		$js = "
		function jSelectUser(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_sample&task=elementUser&tmpl=component&object='.$name;

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('Select a User').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.JText::_('Select').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function _clearElement($name, $value='', $node='', $control_name='')
	{

		$mainframe = JFactory::getApplication();

		$db			=JFactory::getDBO();
		$doc 		=JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name ? $control_name.'['.$name.']' : $name;

		$js = "
		function resetElement(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
		}";
		$doc->addScriptDeclaration($js);

		$html = '<div class="button2-left">
		<div class="blank">

		<a href="javascript::void();" onclick="resetElement( \''.$value.'\', \''.JText::_( 'Select a User' ).'\', \''.$name.'\' )">'.JText::_( 'Clear Selection' ).'</span>
		</div></div>'."\n";

		return $html;
	}

}

