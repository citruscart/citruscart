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
defined('_JEXEC') or die('Restricted access');?>
<?php JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php Citruscart::load( 'CitruscartHelperBase', 'helpers._base' ); ?>
<?php $display_subnum = Citruscart::getInstance()->get( 'display_subnum', 0 );


?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip( JFactory::getApplication()->input->getString('view') ); ?>

    <?php echo CitruscartGrid::searchform($state->filter,JText::_('COM_CITRUSCART_SEARCH'), JText::_('COM_CITRUSCART_RESET') ) ?>


	<table class="table table-striped table-bordered" style="clear: both;">
		<thead>
            <tr>
                <th>
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th>
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.subscription_id", $state->direction, $state->order ); ?>
                </th>
                <th>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_TYPE', "p.product_name", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_CREATED', "tbl.created_datetime", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ORDER', "tbl.order_id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;" <?php if( $display_subnum ) echo 'nowrap'; ?>>
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_CUSTOMER', "u.name", $state->direction, $state->order ); ?>
                <?php if( $display_subnum ) : ?>
                    + <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_SUB_NUM', "tbl.sub_number", $state->direction, $state->order ); ?>
                <?php endif; ?>

                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_EXPIRES', "tbl.expires_datetime", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_TRANSACTION_ID', "tbl.transaction_id", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ENABLED', "tbl.subscription_enabled", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_LIFETIME', "tbl.lifetime_enabled", $state->direction, $state->order ); ?>
                </th>
            </tr>
            <tr class="filterline">
                <th colspan="3">
	                <?php $attribs = array('class' => 'input-mini', 'onchange' => 'document.adminForm.submit();'); ?>
                	<div class="range">
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_FROM'); ?>" id="filter_id_from" name="filter_id_from" value="<?php echo $state->filter_id_from; ?>" class="input-mini"/>
                        </div>
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_TO'); ?>" id="filter_id_to" name="filter_id_to" value="<?php echo $state->filter_id_to; ?>" class="input-mini" />
                        </div>
                    </div>
                </th>
                <th>
                </th>
                <th>
                    <input id="filter_type" name="filter_type" value="<?php echo $state->filter_type; ?>"  class="input-small"/>
                </th>
                <th>
                    <div class="range">
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span>
                            <?php echo JHTML::calendar( $state->filter_date_from, "filter_date_from", "filter_date_from", '%Y-%m-%d %H:%M:%S',array('class'=>'input-small')  ); ?>
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span>
                            <?php echo JHTML::calendar( $state->filter_date_to, "filter_date_to", "filter_date_to", '%Y-%m-%d %H:%M:%S',array('class'=>'input-small')  ); ?>
                        </div>
                    </div>
                </th>
                <th>
                    <input id="filter_orderid" name="filter_orderid" value="<?php echo $state->filter_orderid; ?>"  class="input-small"/>
                </th>
                <th style="text-align: left;">
                	<?php if( $display_subnum ) : ?>
                	<div class="range">
                    <div class="rangeline">
		                		<span class="label"><?php echo JText::_('COM_CITRUSCART_NAME_OR_ID')?></span>:
                	<?php endif; ?>
                	<input id="filter_user" name="filter_user" value="<?php echo $state->filter_user; ?>" class="input-small" size="<?php echo $display_subnum ? '10' : '25' ?>"/>
                	<?php if( $display_subnum ) : ?>
  		              </div>
                    <div class="rangeline">
	                		<span class="label"><?php echo JText::_('COM_CITRUSCART_SUB_NUM')?></span>:
  		              	<input id="filter_subnum" name="filter_subnum" value="<?php echo $state->filter_subnum; ?>" class="input-small" />
  		              </div>
  		            </div>
                	<?php endif; ?>
                </th>
                <th>
                    <div class="range">
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span>
                            <?php echo JHTML::calendar( $state->filter_date_from_expires, "filter_date_from_expires", "filter_date_from_expires", '%Y-%m-%d %H:%M:%S',array('class'=>'input-small') ); ?>
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span>
                            <?php echo JHTML::calendar( $state->filter_date_to_expires, "filter_date_to_expires", "filter_date_to_expires", '%Y-%m-%d %H:%M:%S' ,array('class'=>'input-small')); ?>
                        </div>
                    </div>
                </th>
                <th>
                    <input id="filter_transaction" name="filter_transactionid" value="<?php echo $state->filter_transactionid; ?>" class="input-small"/>
                </th>
                <th>
                    <?php echo CitruscartSelect::booleans( $state->filter_enabled, 'filter_enabled', $attribs, 'enabled', true, 'COM_CITRUSCART_ENABLED_STATE' ); ?>
                </th>
                <th>
                    <?php echo CitruscartSelect::booleans( $state->filter_lifetime, 'filter_lifetime', $attribs, 'lifetime', true, 'COM_CITRUSCART_LIFETIME_STATE' ); ?>
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
					<?php echo CitruscartGrid::checkedout( $item, $i, 'subscription_id' ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link_view; ?>">
						<?php echo $item->subscription_id; ?>
					</a>
				</td>
                <td style="text-align: center; width: 50px;">
                    <a href="<?php echo $item->link; ?>">
                        <img src="<?php echo Citruscart::getURL('images').'page_edit.png' ?>" title="<?php echo JText::_('COM_CITRUSCART_EDIT'); ?>"/>
                    </a>
                </td>
                <td style="text-align: center;">
                    <a href="<?php echo $item->link_view; ?>">
                        <?php echo $item->product_name; ?>
                    </a>
                </td>
                <td style="text-align: center;">
                    <a href="<?php echo $item->link_view; ?>">
                        <?php echo JHTML::_('date', $item->created_datetime, Citruscart::getInstance()->get('date_format')); ?>
                    </a>
                </td>
                <td style="text-align: center;">
                    <a href="<?php echo $item->link_view; ?>">
                        <?php echo $item->order_id; ?>
                    </a>
                </td>
				<td style="text-align: left;">
            <?php if( $display_subnum && strlen( $item->sub_number ) ) : ?>
            	<?php Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' ); ?>
            	<b><?php echo JText::_('COM_CITRUSCART_SUB_NUM'); ?>:</b> <?php echo CitruscartHelperSubscription::displaySubNum( $item->sub_number ); ?><br />
            <?php endif; ?>
				    <?php if (!empty($item->user_name)) { ?>
    					<?php echo $item->user_name .' [ '.$item->user_id.' ]'; ?>
    					<br/>
    					&nbsp;&nbsp;&bull;&nbsp;&nbsp;<?php echo $item->email .' [ '.$item->user_username.' ]'; ?>
    					<br/>
					<?php } ?>
				</td>
                <td style="text-align: center;">
                    <a href="<?php echo $item->link_view; ?>">
                        <?php echo JHTML::_('date', $item->expires_datetime, Citruscart::getInstance()->get('date_format')); ?>
                    </a>
                </td>
                <td style="text-align: center;">
                    <?php echo $item->transaction_id; ?>
                </td>
                <td style="text-align: center;">
                    <?php echo CitruscartGrid::enable($item->subscription_enabled, $i, 'subscription_enabled.' ); ?>
                </td>
                <td style="text-align: center;">
                    <?php echo CitruscartGrid::enable($item->lifetime_enabled, $i, 'lifetime_enabled.' ); ?>
                </td>
			</tr>
			<?php $i=$i+1; $k = (1 - $k); ?>
			<?php endforeach; ?>

			<?php if (!count($items)) : ?>
			<tr>
				<td colspan="20" align="center">
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
