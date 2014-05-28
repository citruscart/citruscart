<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access'); ?>
<?php
	$input = JFactory::getApplication()->input;
	JHtml::_('script','media/citruscart/js/citruscart.js',false,false);?>
<?php $state = $vars->state; ?>
<?php $form = $vars->form; ?>
<?php $items = $vars->list;
?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip( $input->getString('view') ); ?>
	<table class="table table-bordered table-striped adminlist">
		<thead>
            <tr>
                <th>
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th>
                	<?php echo JHtmlGrid::checkall($name = 'cid', $tip = 'JGLOBAL_CHECK_ALL', $action = 'Joomla.checkAll(this)')?>
                </th>
                <th>
                	<?php echo JText::_('COM_CITRUSCART_ID'); ?>
                </th>
                <th>
                	<?php echo JText::_('COM_CITRUSCART_NAME'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_CITRUSCART_TAX_CLASS'); ?>
                </th>
                <th>
    	            <?php echo JText::_('COM_CITRUSCART_ENABLED'); ?>
                </th>
            </tr>
		</thead>
        <tfoot>
            <tr>
                <td colspan="20">
                    &nbsp;
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
                        <?php Citruscart::load( 'CitruscartHelperShipping', 'helpers.shipping' );
                        $id = $input->getInt('id', 0);
                        ?>
                        <span style="float: right;">[<?php
                        echo CitruscartUrl::popup( "index.php?option=com_citruscart&view=shipping&task=view&id={$id}&shippingTask=setRates&tmpl=component&sid={$item->shipping_method_id}",JText::_('Set Rates') ); ?>]</span>
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
					<?php echo CitruscartGrid::boolean( $item->shipping_method_enabled ); ?>
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
	<input type="hidden" name="sid" value=" <?php echo $vars->sid; ?>" />
	<input type="hidden" name="shippingTask" value="_default" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php //echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php //echo $state->direction; ?>" />

</form>