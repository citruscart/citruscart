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

defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>
<?php JHTML::_('script', 'core.js', 'media/system/js/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $row = $this->row; ?>

<h1 style="margin-left: 2%; margin-top: 2%;"><?php echo JText::_('COM_CITRUSCART_SELECT_USERS_FOR'); ?>: <?php echo $row->group_name; ?></h1>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" class="adminform" name="adminForm" id="adminForm">
<div class="note_green" style="width: 96%; text-align: center; margin-left: auto; margin-right: auto;">
    <?php echo JText::_('COM_CITRUSCART_FOR_CHECKED_ITEMS'); ?>:
    <button class="btn btn-success" onclick="document.getElementById('task').value='selected_switch'; document.adminForm.submit();"> <?php echo JText::_('COM_CITRUSCART_CHANGE_STATUS'); ?></button>
    <table class="adminlist table table-striped table-bordered">
		<thead>
            <tr>
                <th style="width: 5px;">
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="width: 20px;">
                	<!-- <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                	-->
                	<?php echo JHtmlGrid::checkall($name = 'cid', $tip = 'JGLOBAL_CHECK_ALL', $action = 'Joomla.checkAll(this)')?>
                </th>
                <th style="width: 50px;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.name", $state->direction, $state->order ); ?>
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_USERNAME', "tbl.username", $state->direction, $state->order ); ?>
                </th>
				<th>
					<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_EMAIL', 'tbl.email', $state->direction, $state->order); ?>
				</th>
				<th>
				</th>
            </tr>
            <tr class="filterline">
                <th colspan="3">
                    <?php $attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
                    <div class="range">
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span> <input id="filter_id_from" name="filter_id_from" value="<?php echo $state->filter_id_from; ?>" class="input-small" />
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span> <input id="filter_id_to" name="filter_id_to" value="<?php echo $state->filter_id_to; ?>" class="input-small" />
                        </div>
                    </div>
                </th>
                <th style="text-align: left;">
                    <input id="filter_name" name="filter_name" value="<?php echo $state->filter_name; ?>" size="25"/>
                    <input type="button" name="filter-search" onclick="document.getElementById('task').value='selectusers'; document.adminForm.submit();" value="<?php echo JText::_('COM_CITRUSCART_FILTER');?>" />
                </th>
                <th>
                    <input id="filter_username" name="filter_username" value="<?php echo $state->filter_username; ?>" size="25"/>
                    <input type="button" name="filter-search" onclick="document.getElementById('task').value='selectusers'; document.adminForm.submit();" value="<?php echo JText::_('COM_CITRUSCART_FILTER');?>" />
                </th>
                <th>
                    <input id="filter_email" name="filter_email" value="<?php echo $state->filter_email; ?>" size="25"/>
                    <input type="button" name="filter-search" onclick="document.getElementById('task').value='selectusers'; document.adminForm.submit();" value="<?php echo JText::_('COM_CITRUSCART_FILTER');?>" />
                </th>
                 <th>
	                <?php echo JText::_('COM_CITRUSCART_STATUS'); ?>
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
                    <div class="pagination pagination-toolbar">
                    <?php echo $this->pagination->getPagesLinks(); ?>
                    </div>
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
					<?php echo CitruscartGrid::checkedout( $item, $i, 'id' ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->id; ?>
					</a>
				</td>
				<td style="text-align: left;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->name; ?>
					</a>
				</td>
				<td style="text-align: center;">
					<?php echo $item->username; ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->email; ?>
				</td>
				<td style="text-align: center;">
					<?php $table = JTable::getInstance('UserGroups', 'CitruscartTable'); ?>
					<?php

                    $keynames = array();
                    $keynames['user_id'] = $item->id;
                    $keynames['group_id'] = $row->group_id;
					?>
					<?php $table->load( $keynames );
					?>
					<?php echo CitruscartGrid::enable(isset($table->user_id), $i, 'selected_'); ?>
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

	<input type="hidden" name="task" id="task" value="selectusers" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</div>
</form>
