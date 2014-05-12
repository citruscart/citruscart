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

$app = JFactory::getApplication();
$view = $app->input->getString('view');
?>
<?php JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php Citruscart::load( 'CitruscartHelperCategory', 'helpers.category' ); ?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip( $view ); ?>

   <?php echo CitruscartGrid::searchform($state->filter,JText::_('COM_CITRUSCART_SEARCH'), JText::_('COM_CITRUSCART_RESET') ) ?>


	<table class="table table-striped table-bordered">
		<thead>
            <tr>
                <th style="width: 5px;">
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="width: 20px;">
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th style="width: 50px;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.category_id", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 50px;">
                </th>
                <th style="text-align: left;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.category_name", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                </th>
                <th style="width: 100px;">
    	            <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ORDER', "tbl.lft", $state->direction, $state->order ); ?>
    	            <?php echo JHTML::_('grid.order', $items ); ?>
                </th>
                <th style="width: 100px;">
    	            <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ENABLED', "tbl.category_enabled", $state->direction, $state->order ); ?>
                </th>
            </tr>
            <tr class="filterline">
                <th colspan="3">
	                <?php $attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
                	<div class="range">
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_FROM'); ?>" id="filter_id_from" name="filter_id_from" value="<?php echo $state->filter_id_from; ?>" size="5" class="input input-tiny" />
                        </div>
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_TO'); ?>" id="filter_id_to" name="filter_id_to" value="<?php echo $state->filter_id_to; ?>" size="5" class="input input-tiny" />
                        </div>
                    </div>
                </th>
                <th>
                </th>
                <th style="text-align: left;">
                	<input type="text" id="filter_name" name="filter_name" value="<?php echo $state->filter_name; ?>" size="25"/>
                	<?php echo CitruscartSelect::category( $state->filter_parentid, 'filter_parentid', $attribs, 'parentid', true ); ?>
                </th>
                <th>
                </th>
                <th>
                </th>
                <th>
    	            <?php echo CitruscartSelect::booleans( $state->filter_enabled, 'filter_enabled', $attribs, 'enabled', true ); ?>
                </th>
            </tr>
			<tr>
				<th colspan="20" style="font-weight: normal;">
					<div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
					<div style="float: left;"><?php echo $this->pagination->getListFooter(); ?></div>
				</th>
			</tr>
		</thead>
		</table>
	<table class="table table-striped table-bordered">
		<tfoot>
			<tr>
				<td colspan="20">
					<div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
					<?php echo $this->pagination->getPagesLinks(); ?>
				</td>
			</tr>
		</tfoot>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td align="center">
					<?php echo $i + 1; ?>
				</td>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::checkedout( $item, $i, 'category_id' ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->category_id; ?>
					</a>
				</td>
                <td style="text-align: center;">
                    <?php echo CitruscartHelperCategory::getImage($item->category_full_image, '', JText::_($item->category_name) ); ?>
                </td>
				<td style="text-align: left;">
					<a href="<?php echo $item->link; ?>">

						<?php
						#TODO  use dash or hypen instead of dot
						//echo str_repeat( '_',$item->level -1 ).JText::_($item->name); ?>
						<?php echo str_repeat( '.&nbsp;',$item->level -1 ).JText::_($item->name); ?>
					</a>
					<br/>
					<?php
					$layout = Citruscart::getClass( 'CitruscartHelperCategory', 'helpers.category' )->getLayout( $item->category_id );
					if ($layout != 'default')
					{
					    echo "<b>".JText::_('COM_CITRUSCART_LAYOUT_OVERRIDE')."</b>: ".$layout;
					}
					?>
				</td>
                <td style="text-align: center;">
                    <?php Citruscart::load( 'CitruscartUrl', 'library.url' ); ?>
                    <?php echo $item->products_count." ".JText::_('COM_CITRUSCART_PRODUCTS'); ?>
                    <br/>
                    <?php $select_url = "index.php?option=com_citruscart&controller=categories&task=selectproducts&id=".$item->category_id."&tmpl=component"; ?>
                    [<?php echo CitruscartUrl::popup( $select_url, JText::_('COM_CITRUSCART_SELECT_PRODUCTS'), array('update' => true) ); ?>]
                </td>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::order($item->category_id); ?>
					<?php echo CitruscartGrid::ordering($item->category_id, $item->ordering ); ?>
				</td>
				<td style="text-align: center;">
					<?php // echo CitruscartGrid::enable($item->category_enabled, $i, 'category_enabled.' ); ?>
				</td>
			</tr>
			<?php $i=$i+1; $k = (1 - $k); ?>
			<?php endforeach; ?>

			<?php if (!count($items)) : ?>
			<tr>
				<td colspan="10" align="center">
					<?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</form>