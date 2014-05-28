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

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Content Component User Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since		1.5
 */

require_once JPATH_SITE.'/libraries/dioscouri/library/model/element.php';

class CitruscartModelElementproduct extends DSCModelElement
{
	var $_list = null;

	var $_page = null;

	var $select_title_constant = 'COM_CITRUSCART_SELECT_PRODUCT';
	public $select_constant = 'COM_CITRUSCART_SELECT_PRODUCT';
	public $clear_constant = 'COM_CITRUSCART_CLEAR_SELECTION';

	public $title_key = "product_name";

	function getTable($name = '', $prefix = null, $options = array()) {
	    JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
		$table = JTable::getInstance('Products', 'CitruscartTable');
		return $table;
	}

	/**
	 * Method to get content article data for the frontpage
	 *
	 * @since 1.5
	 */
	function getList( $refresh = false )
	{
		$where = array();
		$mainframe = JFactory::getApplication();

		if (!empty($this->_list)) {
			return $this->_list;
		}

		// Initialize variables
		$db		= $this->getDBO();
		$filter	= null;

		// Get some variables from the request
		$filter_order		= $mainframe->getUserStateFromRequest('productelement.filter_order',		'filter_order',		'',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest('productelement.filter_direction',	'filter_direction',	'',	'word');
		$limit				= $mainframe->getUserStateFromRequest('global.list.limit',					'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart			= $mainframe->getUserStateFromRequest('productelement.limitstart',			'limitstart',		0,	'int');
		$search				= $mainframe->getUserStateFromRequest('productelement.filter',				'filter',			'',	'string');
		$search				= JString::strtolower($search);
 		$filter_state    = $mainframe->getUserStateFromRequest('productelement.filter_state', 'product_state', '', 'string');

		$valid_ordering_options = array(
		        'tbl.product_id', 'tbl.product_name', 'tbl.product_description'
        );
		if (!$filter_order || !in_array($filter_order, $valid_ordering_options)) {
			$filter_order = 'tbl.product_name';
			$mainframe->setUserState('userelement.filter_order', $filter_order);
		}
		$this->setState('order', $filter_order);

		$order = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$all = 1;

		// Keyword filter
		if ($search) {
			$where[] = 'LOWER( tbl.product_id ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( tbl.product_name ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( tbl.product_sku ) LIKE '.$db->q( '%'.$db->escape( $search, true ).'%', false );
		}
		// Build the where clause of the query
		if( strlen( $filter_state ) ) {
	    	$where = count( $where ) ?  " WHERE (".implode(' OR ', $where)  .')  AND ( tbl.product_enabled = '.$db->q( $db->escape( $filter_state, true ), false ). ' )' :
            ' WHERE  tbl.product_enabled = '.$db->q( $db->escape( $filter_state, true ), false );
   		} else {
      		$where = (count($where) ? ' WHERE '.implode(' OR ', $where) : '');
	    }

		// Get the total number of records
		$query = 'SELECT COUNT(tbl.product_id)' .
				' FROM #__citruscart_products AS tbl' .
				$where;
		$db->setQuery($query);
		$total = $db->loadResult();

		// Create the pagination object
		jimport('joomla.html.pagination');
		$this->_page = new JPagination($total, $limitstart, $limit);

		// Get the products
		$query = 'SELECT tbl.*, pp.* ' .
				' FROM #__citruscart_products AS tbl' .
				' LEFT JOIN #__citruscart_productprices pp ON pp.product_id = tbl.product_id '.
				$where .
				' GROUP BY tbl.product_id '.
				$order;
		$db->setQuery($query, $this->_page->limitstart, $this->_page->limit);
		$this->_list = $db->loadObjectList();

		//currency formatting
		Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
		foreach($this->_list as $item)
		{
			$item->product_price = CitruscartHelperBase::currency($item->product_price);
		}

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

	function fetchElement($name, $value = '', $control_name = '', $js_extra = '', $fieldName = '', $clear = NULL)
	{
		$app = JFactory::getApplication();

		$doc = JFactory::getDocument();

		if (empty($fieldName)) {
			$fieldName = $control_name ? $control_name . '[' . $name . ']' : $name;
		}

		if ($value) {
			$table = $this -> getTable();
			$table -> load($value);
			$title_key = $this -> title_key;
			$title = $table -> $title_key;
		} else {
			$title = JText::_($this -> select_title_constant);
		}

		$close_window = '';
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			$close_window = "window.parent.SqueezeBox.close();";
		} else {
			$close_window = "document.getElementById('sbox-window').close();";
		}

		$js = "Dsc.select" . $this -> getName() . " = function(id, title, object) {
                        document.getElementById(object + '_id').value = id;
                        document.getElementById(object + '_name').value = title;
                        document.getElementById(object + '_name_hidden').value = title;
        $close_window
        $js_extra
                   }";
		$doc -> addScriptDeclaration($js);

		if (!empty($this -> option)) {
			$option = $this -> option;
		} else {
			$r = null;

			if (!preg_match('/(.*)Model/i', get_class($this), $r)) {
				JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'));
			}

			$option = 'com_' . strtolower($r[1]);
		}

		$link = 'index.php?option=' . $option . '&view=' . $this -> getName() . '&tmpl=component&object=' . $name;

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n" . '<div class="pull-left"><input type="text" style="background: #ffffff;" type="text" id="' . $name . '_name" value="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" disabled="disabled" /></div>';
		$html .= '<a class="modal btn btn-primary" style="color:#fff;"  title="' . JText::_($this -> select_title_constant) . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">' . JText::_($this -> select_constant) . '</a>' . "\n";
		$html .= "\n" . '<input type="hidden" id="' . $name . '_id" name="' . $fieldName . '" value="' . $value . '" />';
		$html .= "\n" . '<input type="hidden" id="' . $name . '_name_hidden" name="' . $name . '_name_hidden" value="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" />';

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
	function clearElement($name, $value = '', $control_name = '') {
		$doc = JFactory::getDocument();
		$fieldName = $control_name ? $control_name . '[' . $name . ']' : $name;

		$js = "
            Dsc.reset" . $this -> getName() . " = function(id, title, object) {
                document.getElementById(object + '_id').value = id;
                document.getElementById(object + '_name').value = title;
            }";
		$doc -> addScriptDeclaration($js);

		$html = '<a class="btn btn-danger"  style="color:#fff;" href="javascript:void(0);" onclick="Dsc.reset' . $this -> getName() . '( \'' . $value . '\', \'' . JText::_($this -> select_title_constant) . '\', \'' . $name . '\' )">' . JText::_($this -> clear_constant) . '
                    </a>';

		return $html;
	}

}
