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

<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $state = $this->state; ?>

<?php
$form = $this->form; ?>
<?php $items = $this->items; ?>

<!-- Get the application -->
<?php $app = JFactory::getApplication(); ?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<?php

	/* Get view string */
	echo CitruscartGrid::pagetooltip( $app->input->getString('view') );

	?>

    <?php echo CitruscartGrid::searchform($state->filter,JText::_('COM_CITRUSCART_SEARCH'), JText::_('COM_CITRUSCART_RESET') ) ?>

	<table class="table table-striped table-bordered" style="clear: both;">
		<thead>
            <tr>
                <th>
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th>
                	<?php echo JHtmlGrid::checkall($name = 'cid', $tip = 'JGLOBAL_CHECK_ALL', $action = 'Joomla.checkAll(this)')?>
            	</th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.coupon_id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.coupon_name", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_CODE', "tbl.coupon_code", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_VALUE', "tbl.coupon_value", $state->direction, $state->order ); ?>
                </th>
                <th >
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_TYPE', "tbl.coupon_value_type", $state->direction, $state->order ); ?>
                </th>
                <th>
    	            <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ENABLED', "tbl.coupon_enabled", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_USES', "tbl.coupon_uses", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_CITRUSCART_DETAILS'); ?>
                </th>
            </tr>
            <tr class="filterline">
                <th colspan="3">
                	<?php $attribs = array('class' => 'input-small', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
                	 <div class="range">
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_FROM'); ?>" id="filter_id_from" name="filter_id_from" value="<?php echo $state->filter_id_from; ?>" class="input-mini" />
                        </div>
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_TO'); ?>" id="filter_id_to" name="filter_id_to" value="<?php echo $state->filter_id_to; ?>" class="input-mini" />
                        </div>
                    </div>
                </th>
                <th>
                	<input id="filter_name" name="filter_name" type="text" value="<?php echo $state->filter_name; ?>" class="input"/>
                </th>
                <th style="text-align: center;">
                    <input id="filter_code" name="filter_code" type="text" value="<?php echo $state->filter_code; ?>" class="input"/>
                </th>
                <th>
                    <input id="filter_value" name="filter_value" type="text" value="<?php echo $state->filter_value; ?>" class="input-small"/>
                </th>
                <th>
                    <?php echo CitruscartSelect::booleans( $state->filter_type, 'filter_type', $attribs, 'filter_type', true, 'COM_CITRUSCART_SELECT_TYPE', 'COM_CITRUSCART_PERCENTAGE', 'COM_CITRUSCART_FLAT_RATE' ); ?>
                </th>
                <th>
    	            <?php echo CitruscartSelect::booleans( $state->filter_enabled, 'filter_enabled', $attribs, 'enabled', true, 'COM_CITRUSCART_ENABLED_STATE' ); ?>
                </th>
                <th>
                </th>
                <th>
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
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td align="center">
					<?php echo $i + 1; ?>
				</td>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::checkedout( $item, $i, 'coupon_id' ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>" class="badge badge-warning">
						<?php echo $item->coupon_id; ?>
					</a>
				</td>
				<td style="text-align: left;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->coupon_name; ?>
					</a>
				</td>
                <td style="text-align: center;">
                    <a href="<?php echo $item->link; ?>">
                        <?php echo $item->coupon_code; ?>
                    </a>
                </td>
                <td style="text-align: center;">
                    <span class="badge badge-success"><?php echo $item->coupon_value; ?></span>
                </td>
                 <td style="text-align: center;">
                    <span class="badge badge-info"><?php echo JText::_('COM_CITRUSCART_COUPON_VALUE_TYPE_'.$item->coupon_value_type); ?></span>
                </td>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::enable($item->coupon_enabled, $i, 'coupon_enabled.' ); ?>
				</td>
                <td style="text-align: center;">
                    <?php echo $item->coupon_uses; ?>
                </td>
                <td style="text-align: center;">

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

	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</form>