<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items;

?>

<!-- Get the application -->
<?php $app = JFactory::getApplication(); ?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip($app->input->getString('view') );

	//CitruscartGrid::pagetooltip( JRequest::getVar('view') );
	?>

    <table class="table">
        <tr>
            <td align="left" width="100%">
            </td>
            <td nowrap="nowrap">
                <input type="text" name="filter" value="<?php echo $state->filter; ?>" />
                <button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_('COM_CITRUSCART_SEARCH'); ?></button>
                <button class="btn btn-danger"onclick="CitruscartFormReset(this.form);"><?php echo JText::_('COM_CITRUSCART_RESET'); ?></button>
            </td>
        </tr>
    </table>

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
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.shipping_method_id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.shipping_method_name", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_TAX_CLASS', "tbl.tax_class_id", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
    	            <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ENABLED', "tbl.shipping_method_enabled", $state->direction, $state->order ); ?>
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
                <th style="text-align: left;">
                	<input id="filter_name" name="filter_name" value="<?php echo $state->filter_name; ?>" size="15"/>
                	<?php echo CitruscartSelect::shippingtype( $state->filter_shippingtype, 'filter_shippingtype', $attribs, 'shippingtype', true ); ?>
                </th>
                <th>
                    <?php echo CitruscartSelect::taxclass( $state->filter_taxclass, 'filter_taxclass', $attribs, 'taxclass', true, false ); ?>
                </th>
                <th>
    	            <?php echo CitruscartSelect::booleans( $state->filter_enabled, 'filter_enabled', $attribs, 'enabled', true, 'COM_CITRUSCART_ENABLED_STATE' ); ?>
                </th>
            </tr>
			<tr>
				<th colspan="20" style="font-weight: normal;">
					<div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
					<div style="float: left;"><?php echo $this->pagination->getListFooter(); ?></div>
				</th>
			</tr>
		</thead>
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
					<?php echo CitruscartGrid::checkedout( $item, $i, 'shipping_method_id' ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->shipping_method_id; ?>
					</a>
				</td>
				<td style="text-align: left;">
                    <a href="<?php echo $item->link; ?>">
                        <?php echo $item->shipping_method_name; ?>
                    </a>
                    <div class="shipping_rates">
                        <?php Citruscart::load( 'CitruscartUrl', 'library.url' ); ?>
                        <?php Citruscart::load( 'CitruscartHelperShipping', 'helpers.shipping' ); ?>
                        <span style="float: right;">[<?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&controller=shippingmethods&task=setrates&id=".$item->shipping_method_id."&tmpl=component", "Set Rates" ); ?>]</span>
                        <?php
                        if ($shipping_method_type = CitruscartHelperShipping::getType($item->shipping_method_type))
                        {
                        	echo "<b>".JText::_('COM_CITRUSCART_TYPE')."</b>: ".$shipping_method_type->title;
                        }
                        if ($item->subtotal_minimum > '0')
                        {
                        	echo "<br/><b>".JText::_('COM_CITRUSCART_MINIMUM_ORDER_REQUIRED')."</b>: ".CitruscartHelperBase::currency( $item->subtotal_minimum );
                        }
                        if( $item->subtotal_maximum > '-1' )
                        {
                        	echo "<br/><b>".JText::_('COM_CITRUSCART_SHIPPING_METHODS_SUBTOTAL_MAX')."</b>: ".CitruscartHelperBase::currency( $item->subtotal_maximum );
                        }
                        ?>
                    </div>
				</td>
				<td style="text-align: center;">
				    <?php echo $item->tax_class_name; ?>
				</td>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::enable($item->shipping_method_enabled, $i, 'shipping_method_enabled.' ); ?>
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