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
<?php JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/'); ?>
<?php JHTML::_('script', 'core.js', 'media/system/js/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $row = $this->row; ?>

<h1 style="margin-left: 2%; margin-top: 2%;"><?php echo JText::_('COM_CITRUSCART_SELECT_ZONES_FOR'); ?>: <?php echo $row->geozone_name; ?></h1>

<div class="note_green" style="width: 95%; text-align: center; margin-left: auto; margin-right: auto;">

	<ul class="inline">
		<li>

			<button class="btn btn-success" onclick="document.getElementById('task').value='selected_switch'; document.adminForm.submit();"> <?php echo JText::_('COM_CITRUSCART_CHANGE_STATUS'); ?></button><br />
		</li>
		<li>
			<?php echo JText::_('COM_CITRUSCART_FOR_CHECKED_ITEMS'); ?>:
			<button class="btn" onclick="document.adminForm.toggle.checked=true; checkAll(<?php echo count( $items ); ?>);document.getElementById('task').value='savezipranges'; document.adminForm.submit();"> <?php echo JText::_('COM_CITRUSCART_SAVE_ALL_CHANGES_TO_ZIP_RANGES'); ?></button>
		</li>
	</ul>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data">

    <table class="table table-bordered">
        <tr>
            <td align="left" width="100%">
                <input name="filter" value="<?php echo $state->filter; ?>" />
                <button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_('COM_CITRUSCART_SEARCH'); ?></button>
                <button class="btn btn-danger" onclick="CitruscartFormReset(this.form);"><?php echo JText::_('COM_CITRUSCART_RESET'); ?></button>
            </td>
            <td>
                <?php $attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
                <?php
                echo CitruscartSelect::booleans( $state->filter_associated, 'filter_associated', $attribs, $idtag = null, false, '', 'COM_CITRUSCART_ASSOCIATED_ZONES_ONLY', 'COM_CITRUSCART_ALL_ZONES' );
                ?>
            </td>
            <td nowrap="nowrap">
                <?php echo CitruscartSelect::country( $state->filter_countryid, 'filter_countryid', $attribs, 'country_id', true ); ?>
            </td>
        </tr>
    </table>

	<table class="table table-striped table-bordered">
		<thead>
            <tr>
                <th style="width: 5px;">
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="width: 20px;">
                	<input type="checkbox" name="toggle"  value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th style="width: 50px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.zone_id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.zone_name", $state->direction, $state->order ); ?>
                </th>
                <th>
	                <?php echo JText::_('COM_CITRUSCART_STATUS'); ?>
                </th>
                <th style="width: 150px;">
	                <?php echo JText::_('COM_CITRUSCART_POSTAL_CODE_RANGE'); ?>
                </th>
            </tr>
		</thead>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach ($items as $a=>$item) : ?>
            <tr class='row<?php echo $a%2; ?>'>
				<td align="center">
					<?php echo $i + 1; ?>
				</td>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::checkedout( $item, $i, 'zone_id' ); ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->zone_id; ?>
				</td>
				<td style="text-align: left;">
					<?php echo JText::_($item->zone_name); ?>
				</td>
				<td style="text-align: center;">
					<?php $table = JTable::getInstance('ZoneRelations', 'CitruscartTable'); ?>
					<?php
                    $keynames = array();
                    $keynames['geozone_id'] = $row->geozone_id;
                    $keynames['zone_id'] = $item->zone_id;
					?>
					<?php $table->load( $keynames ); ?>
					<?php echo CitruscartGrid::enable(isset($table->geozone_id), $i, 'selected_'); ?>
				</td>
				<td style="text-align: center;">
					<?php if(isset($table->geozone_id)): ?>
					<input type="text" name="zip_range[<?php echo $table->zone_id;?>]" value="<?php echo $table->zip_range;?>" />
					<?php endif; ?>
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

	<input type="hidden" name="task" id="task" value="selectzones" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</form>
</div>