<?php
/**
 * @version		$Id: view.php 10710 2008-08-21 10:08:12Z eddieajau $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML User Element View class for the Content component
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class CitruscartViewElementProductMultiple extends JView
{
	function display()
	{
		$mainframe = JFactory::getApplication();

		// Initialize variables
		$db			= JFactory::getDBO();
		$nullDate	= $db->getNullDate();

		$document	= JFactory::getDocument();
		$document->setTitle('Product Selection');

		JHTML::_('behavior.modal');

		$template = $mainframe->getTemplate();
		$document->addStyleSheet("templates/$template/css/general.css");
		$document->addScript( 'media/citruscart/js/citruscart.js' );

		$limitstart = JRequest::getVar('limitstart', '0', '', 'int');

		$lists = $this->_getLists();

		//Ordering allowed ?
		// $ordering = ($lists['order'] == 'section_name' && $lists['order_Dir'] == 'ASC');

		$rows = &$this->get('List');
		$page = &$this->get('Pagination');
		JHTML::_('behavior.tooltip');

		$object = JRequest::getVar( 'object' );
		$link = 'index.php?option=com_citruscart&task=elementproductmultiple&tmpl=component&object='.$object;

		Citruscart::load( 'CitruscartGrid', 'library.grid' );
		?>
		<?php JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>

<form action="<?php echo $link; ?>" method="post" name="adminForm">

<table>
	<tr>
		<td width="100%"><?php echo JText::_('COM_CITRUSCART_FILTER'); ?>: <input
			type="text" name="search" id="search"
			value="<?php echo $lists['search'];?>" class="text_area"
			onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_('COM_CITRUSCART_GO'); ?></button>
		<button
			onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('COM_CITRUSCART_RESET'); ?></button>
		</td>
		<td nowrap="nowrap">
    		<button onclick="CitruscartSetItemsToOrder(<?php echo count( $rows ); ?>, '<?php echo JText::_('COM_CITRUSCART_UNABLE_TO_RETRIEVE_PRODUCT_SELECTION'); ?>');return false;"><?php echo JText::_('COM_CITRUSCART_ADD_SELECTED_PRODUCTS_TO_ORDER'); ?></button>
		</td>
	</tr>
</table>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_('COM_CITRUSCART_NUM'); ?></th>
			<th style="width: 20px;"><input type="checkbox" name="toggle"
				value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
			<th width="2%" class="title"><?php echo JHTML::_('grid.sort',   'ID', 'tbl.product_id', @$lists['order_Dir'], @$lists['order'] ); ?>
			</th>
			<th style="width:50px;"><?php echo JText::_('COM_CITRUSCART_IMAGE'); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort',   'Name', 'tbl.product_name', @$lists['order_Dir'], @$lists['order'] ); ?>
			</th>
			<th class="title"><?php echo JHTML::_('grid.sort',   'Price', 'pp.product_price', @$lists['order_Dir'], @$lists['order'] ); ?>
			</th>
			<th class="title"><?php echo JText::_('COM_CITRUSCART_QTY'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="15"><?php echo $page->getListFooter(); ?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $rows ); $i < $n; $i++)
	{
		$row = &$rows[$i];

		$onclick = "
					window.parent.jSelectProducts(
					'{$row->product_id}', '".str_replace(array("'", "\""), array("\\'", ""), $row->product_name)."', '".JRequest::getVar('object')."'
					);";
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $page->getRowOffset( $i ); ?></td>
			<td style="text-align: center;"><?php echo CitruscartGrid::checkedout( $row, $i, 'product_id' ); ?>
			</td>
			<td style="text-align: center;"><a style="cursor: pointer;"
				onclick="<?php echo $onclick; ?>"> <?php echo $row->product_id;?> </a>
			</td>
			<td>
			<?php
				jimport('joomla.filesystem.file');
				if (!empty($row->product_thumb_image) && JFile::exists( Citruscart::getPath( 'products_thumbs').DS.$row->product_thumb_image ))
				{
					?>
					<img src="<?php echo Citruscart::getURL( 'products_thumbs').$row->product_thumb_image; ?>" style="display: block;" />
					<?php	
				}
			?>	
			</td>				
			<td><a style="cursor: pointer;"
				onclick="<?php echo $onclick; ?>"> <?php echo htmlspecialchars($row->product_name, ENT_QUOTES, 'UTF-8'); ?>
			</a></td>
			<td style="text-align: center;"><a style="cursor: pointer;"
				onclick="<?php echo $onclick; ?>"> <?php echo $row->product_price;?>
			</a></td>
			<td style="text-align: center;"><input id="<?php echo "qty$i"; ?>" name="<?php echo "qty$i"; ?>" type="text" value="1" style="width: 30px;" /></td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
</table>

<input type="hidden" name="boxchecked" value="0" /> <input type="hidden"
	name="filter_order" value="<?php echo $lists['order']; ?>" /> <input
	type="hidden" name="filter_order_Dir"
	value="<?php echo $lists['order_Dir']; ?>" /></form>
	<?php
	}

	function _getLists()
	{
		$mainframe = JFactory::getApplication();

		// Initialize variables
		$db		= JFactory::getDBO();

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

		// get list of sections for dropdown filter
		$javascript = 'onchange="document.adminForm.submit();"';

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search'] = $search;

		return $lists;
	}
}