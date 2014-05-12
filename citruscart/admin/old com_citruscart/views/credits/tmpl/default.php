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

	JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/');
	$state =$this->state;
	$form = $this->form;
	$items = $this->items;

	$date_format = Citruscart::getInstance()->get('date_format');
	if (version_compare(JVERSION, '1.6.0', 'ge'))
	{
		$date_format = Citruscart::getInstance()->get('date_format_act');
	}
?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip( JFactory::getApplication()->input->getString('view') ); ?>

   <?php echo CitruscartGrid::searchform($state->filter,JText::_('COM_CITRUSCART_SEARCH'), JText::_('COM_CITRUSCART_RESET') ) ?>


	<table class="table table-striped table-bordered" style="clear: both;">
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
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.credit_id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_USER', "u.name", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_TYPE', "tbl.credittype_code", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_AMOUNT', "tbl.credit_amount", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_CREATED', "tbl.created_date", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ENABLED', "tbl.credit_enabled", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_WITHDRAWABLE', "tbl.credit_withdrawable", $state->direction, $state->order ); ?>
                </th>
            </tr>
            <tr class="filterline">
                <th colspan="3">
                	<?php $attribs = array('class' => 'input-small', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
                	<div class="range">
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_FROM'); ?>" id="filter_id_from" name="filter_id_from" value="<?php echo $state->filter_id_from; ?>" class="input-small"/>
                        </div>
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_TO'); ?>" id="filter_id_to" name="filter_id_to" value="<?php echo $state->filter_id_to; ?>"  class="input-small" />
                        </div>
                    </div>
                </th>
                <th style="text-align: left;">
                    <input type="text" id="filter_user" name="filter_user" value="<?php echo $state->filter_user; ?>" class="input-small" />
                </th>
                <th>
                </th>
                <th>
                    <div class="range">
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span> <input id="filter_amount_from" type="text" name="filter_amount_from" value="<?php echo $state->filter_amount_from; ?>" class="input-small" />
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span> <input id="filter_amount_to" type="text" name="filter_amount_to" value="<?php echo $state->filter_amount_to; ?>" class="input-small" />
                        </div>
                    </div>
                </th>
                <th>
                    <div class="range">
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span>
                            <?php echo JHTML::calendar( $state->filter_date_from, "filter_date_from", "filter_date_from", '%Y-%m-%d %H:%M:%S' ,array('class'=>'input-small')); ?>
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span>
                            <?php echo JHTML::calendar( $state->filter_date_to, "filter_date_to", "filter_date_to", '%Y-%m-%d %H:%M:%S',array('class'=>'input-small') ); ?>
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_TYPE'); ?>:</span>
                            <?php echo CitruscartSelect::datetype( $state->filter_datetype, 'filter_datetype', array('class'=>'input-small'), 'datetype' ); ?>
                        </div>
                    </div>
                </th>
                <th>
                    <?php echo CitruscartSelect::booleans($state->filter_enabled, 'filter_enabled', array('class'=>'input-small'), 'filter_enabled', true ); ?>
                </th>
                <th>
                    <?php echo CitruscartSelect::booleans($state->filter_withdraw, 'filter_withdraw', array('class'=>'input-small'), 'filter_withdraw', true ); ?>
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
					<?php echo CitruscartGrid::checkedout( $item, $i, 'credit_id' ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->credit_id; ?>
					</a>
				</td>
                <td style="text-align: left;">
                    <?php if (!empty($item->user_name)) { ?>
                        <a href="<?php echo $item->link; ?>">
                        <?php echo $item->user_name .' [ '.$item->user_id.' ]'; ?>
                        </a>
                        <br/>
                        &nbsp;&nbsp;&bull;&nbsp;&nbsp;
                        <a href="<?php echo $item->link; ?>">
                        <?php echo $item->email .' [ '.$item->user_username.' ]'; ?>
                        </a>
                        <br/>
                    <?php } ?>

                    <?php
                    if (!empty($item->credit_enabled))
                    {
                        echo "<b>" . JText::_('COM_CITRUSCART_BALANCE_BEFORE') . ":</b> " . CitruscartHelperBase::currency( $item->credit_balance_before ). "<br/>";
                        echo "<b>" . JText::_('COM_CITRUSCART_BALANCE_AFTER') . ":</b> " . CitruscartHelperBase::currency( $item->credit_balance_after );
                    }
                    ?>

                    <?php
                    if (!empty($item->credit_withdrawable))
                    {
                        echo "<br/>";
                        echo "<b>" . JText::_('COM_CITRUSCART_WITHDRAWABLE_BALANCE_AFTER') . ":</b> " . CitruscartHelperBase::currency( $item->withdrawable_balance_after );
                    }
                    ?>
                </td>
                <td style="text-align: center;">
                    <a href="<?php echo $item->link; ?>">
                        <?php echo JText::_( $item->credittype_name ); ?>
                    </a>
                </td>
				<td style="text-align: center;">
					<h2><?php echo CitruscartHelperBase::currency( $item->credit_amount ); ?></h2>
				</td>
                <td style="text-align: center;">
                   <?php echo JHTML::_('date', $item->created_date, $date_format ); ?>
                </td>
                <td style="text-align: center;">
                    <?php echo CitruscartGrid::boolean( $item->credit_enabled ); ?>
                </td>
                <td style="text-align: center;">
                    <?php echo CitruscartGrid::boolean( $item->credit_withdrawable ); ?>
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