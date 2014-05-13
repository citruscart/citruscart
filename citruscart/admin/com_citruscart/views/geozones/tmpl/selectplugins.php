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
 ?>
<?php JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>
<?php JHTML::_('script', 'core.js', 'media/system/js/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $row = $this->row; ?>
<?php $suffix = 'COM_CITRUSCART_' . strtoupper($this->suffix);?>

<h1 style="margin-left: 2%; margin-top: 2%;"><?php echo JText::sprintf('COM_CITRUSCART_SELECT_SUFFIX_PLUGINS_FOR', JText::_( $suffix ) ); ?>: <?php echo $row->geozone_name; ?></h1>


<div class="note_green" style="width: 95%; text-align: center; margin-left: auto; margin-right: auto;">
	<ul class="inline">
		<li>
			<button class="btn btn-success" onclick="document.getElementById('task').value='plugin_switch'; document.adminForm.submit();"> <?php echo JText::_('COM_CITRUSCART_CHANGE_STATUS'); ?></button><br />
		</li>
		<li>
			<?php echo JText::_('COM_CITRUSCART_FOR_CHECKED_ITEMS'); ?>:
			<button class="btn" onclick="document.adminForm.toggle.checked=true; checkAll(<?php echo count( $items ); ?>);document.getElementById('task').value='plugin_switch'; document.adminForm.submit();"> <?php echo JText::_('COM_CITRUSCART_TOGGLE_ALL_STATUS'); ?></button>
		</li>
   </ul>
<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data">

	<div style="text-align: right;">
			<input name="filter" size="40" value="<?php echo $state->filter; ?>" />
            <button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_('COM_CITRUSCART_SEARCH'); ?></button>
            <button class="btn btn-danger" onclick="CitruscartFormReset(this.form);"><?php echo JText::_('COM_CITRUSCART_RESET'); ?></button>
	</div>

	<table class="table table-striped table-bordered" style="clear: both;">
		<thead>
            <tr>
                <th style="width: 5px;">
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="width: 20px;">
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th style="width: 50px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.name", $state->direction, $state->order ); ?>
                </th>
                <th>
	                <?php echo JText::_('COM_CITRUSCART_STATUS'); ?>
                </th>
            </tr>
		</thead>
		</table>
		<table class="table table-striped table-bordered">
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td align="center">
					<?php echo $i + 1; ?>
				</td>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::checkedout( $item, $i, 'id' ); ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->id; ?>
				</td>
				<td style="text-align: left;">
					<?php echo JText::_($item->name); ?>
				</td>
				<td style="text-align: center;">
					<?php $found = in_array($row->geozone_id, $item->geozones) ? true : false;?>
					<?php echo CitruscartGrid::enable($found, $i, 'plugin_'); ?>
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
		<tfoot>
			<tr>
				<td colspan="20">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="task" id="task" value="selectplugins" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</form>
</div>