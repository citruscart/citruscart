<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
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
class CitruscartModelElementAddress extends JModel
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
		$db		=& $this->getDBO();
		$filter	= null;

		// Get some variables from the request
//		$sectionid			= JRequest::getVar( 'sectionid', -1, '', 'int' );
//		$redirect			= $sectionid;
//		$option				= JRequest::get( 'option' );
		$filter_order		= $mainframe->getUserStateFromRequest('userelement.filter_order',		'filter_order',		'',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest('userelement.filter_order_Dir',	'filter_order_Dir',	'',	'word');
		$limit				= $mainframe->getUserStateFromRequest('global.list.limit',					'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart			= $mainframe->getUserStateFromRequest('userelement.limitstart',			'limitstart',		0,	'int');
		$search				= $mainframe->getUserStateFromRequest('userelement.search',				'search',			'',	'string');
		$search				= JString::strtolower($search);

		if (!$filter_order) {
			$filter_order = 'c.address_id';
		}
		$order = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$all = 1;

		// Keyword filter
		if ($search) {
			$where[] = 'LOWER( c.address1 ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( c.address2 ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( co.country_name ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( s.state_name ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( z.zone_name ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
		}

		// Build the where clause of the query
		$where = (count($where) ? ' WHERE '.implode(' OR ', $where) : '');

		// Get the total number of records
		$query = 'SELECT COUNT(c.address_id)' .
				' FROM #__citruscart_addresses AS c' .
				$where;
		$db->setQuery($query);
		$total = $db->loadResult();

		// Create the pagination object
		jimport('joomla.html.pagination');
		$this->_page = new JPagination($total, $limitstart, $limit);

		// Get the products
		$query = 'SELECT c.*, at.* ' .
				' FROM #__citruscart_adresses AS c' .
				' LEFT JOIN #__citruscart_addresstypes at ON pp.address_type_id = c.address_type_id '.
				' LEFT JOIN #__citruscart_countries co ON co.country_id = c.country_id '.
				' LEFT JOIN #__citruscart_states s ON s.state_id = c.state_id '.
				' LEFT JOIN #__citruscart_zones z ON z.zone_id = c.zone_id '.
				$where .
				$order;
		$db->setQuery($query, $this->_page->limitstart, $this->_page->limit);
		$this->_list = $db->loadObjectList();

		//currency formatting
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

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

		$db			=JFactory::getDBO();
		$doc 		=JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name ? $control_name.'['.$name.']' : $name;

		$title = JText::_('COM_CITRUSCART_SELECT_ADDRESS');

		$js = "
		function jSelectProduct(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_citruscart&task=elementproduct&tmpl=component&object='.$name;

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.$title.'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.$title.'</a></div></div>'."\n";
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

		$db			= JFactory::getDBO();
		$doc 		= JFactory::getDocument();
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

		<a href="javascript::void();" onclick="resetElement( \''.$value.'\', \''.JText::_('COM_CITRUSCART_ADD_PRODUCTS').'\', \''.$name.'\' )">'.JText::_('COM_CITRUSCART_REMOVE_PRODUCTS').'</span>
		</div></div>'."\n";

		return $html;
	}

}

