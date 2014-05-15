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


	JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
	$form = $this->form;
	$row = $this->row;
	$order = $this->order;
	$items = $order->getItems();
	$histories = $row->orderhistory ? $row->orderhistory : array();
	$config = Citruscart::getInstance();
	$guest = $row->user_id < Citruscart::getGuestIdStart();
?>


<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" enctype="multipart/form-data" >

    <?php
        // fire plugin event here to enable extending the form
        JDispatcher::getInstance()->trigger('onBeforeDisplayOrderPrint', array( $row ) );
    ?>

    <table style="width: 100%;">
    <tr>
    	<td colspan="2" style="vertical-align:top;">

    	 <fieldset>
            <legend><?php echo JText::_('COM_CITRUSCART_SHOP_INFORMATION'); ?></legend>

            <table class="admintable" style="float:left; width:50%;">
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_SHOP_NAME'); ?>
                </td>
                <td>
                    <?php echo $config->get('shop_name', ''); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('CCOM_CITRUSCART_COMPANY_NAME'); ?>
                </td>
                <td>
                    <?php echo $config->get('shop_company_name', ''); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_SHOP_OWNER'); ?>
                </td>
                <td>
                    <?php echo $config->get('shop_owner_name', '') ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_PHONE'); ?>
                </td>
                <td>
                    <?php echo $config->get('shop_phone', '') ?>
                </td>
            </tr>
            </table>

            <table class="admintable" style="float:right; width:50%;">
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_ADDRESS'); ?>
                </td>
                <td>
                    <?php echo $config->get('shop_address_1', '') ?>
                    <?php
                    	$address_2 = $config->get('shop_address_2', '');
						if (!empty($address_2))
						{
						    echo "<br/>".$address_2."<br />";
						}

						echo $config->get('shop_city', ''). " ";
						echo $row->shop_zone_name. " ";
						echo $config->get('shop_zip', ''). "<br/>";
						echo $row->shop_country_name;
                    ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_TAX_NUMBER_1'); ?>
                </td>
                <td>
                    <?php echo $config->get('shop_tax_number_1', ''); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_TAX_NUMBER_2'); ?>
                </td>
                <td>
                    <?php echo $config->get('shop_tax_number_2', ''); ?>
                </td>
            </tr>
            </table>

         </fieldset>
    	</td>
    </tr>
    <tr>
        <td style="width: 50%; vertical-align: top;">

            <fieldset>
            <legend><?php echo JText::_('COM_CITRUSCART_ORDER_INFORMATION'); ?></legend>

            <table class="admintable" style="clear: both;">
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_ORDER_ID'); ?>
                </td>
                <td>
                    <?php echo $row->order_id; ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_ORDER_DATE'); ?>
                </td>
                <td>
                    <?php echo $row->created_date; ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_ORDER_STATUS'); ?>
                </td>
                <td>
                    <?php echo $row->order_state_name; ?>
                </td>
            </tr>
            </table>
            </fieldset>

            <fieldset>
            <legend><?php echo JText::_('COM_CITRUSCART_CUSTOMER_INFORMATION'); ?></legend>

            <table class="admintable" style="clear: both;">
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_NAME'); ?>
                </td>
                <td>
                	<?php
                	if( $guest )
                		echo JText::_('COM_CITRUSCART_GUEST');
                	else
                		echo $row->user_name." [".$row->user_id."]";
                	?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_EMAIL'); ?>
                </td>
                <td>
                	<?php
                		if( $guest )
                		{
                			if( $config->get( 'obfuscate_guest_email', 0 ) ) // obfuscate guest email
                				echo '*****';
                			else
	                			echo $row->userinfo_email;
                		}
                		else
											echo $row->email;
                	?>
                </td>
            </tr>
            <?php if ($row->customer_note) : ?>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_NOTE'); ?>
                </td>
                <td>
                    <?php echo $row->customer_note; ?>
                </td>
            </tr>
            <?php endif; ?>
            </table>

            </fieldset>

        </td>
        <td style="width: 50%; vertical-align: top;">

            <?php if ($order->order_ships) { ?>
            <fieldset>
            <legend><?php echo JText::_('COM_CITRUSCART_SHIPPING_INFORMATION'); ?></legend>

            <table class="admintable" style="clear: both;">
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_SHIPPING_METHOD'); ?>
                </td>
                <td>
                    <?php echo JText::_( $row->ordershipping_name ); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_SHIPPING_ADDRESS'); ?>
                </td>
                <td>
                    <?php
                    echo $row->shipping_first_name." ".$row->shipping_last_name."<br/>";
                    echo $row->shipping_address_1.", ";
                    echo $row->shipping_address_2 ? $row->shipping_address_2.", " : "";
                    echo $row->shipping_city.", ";
                    echo $row->shipping_zone_name." ";
                    echo $row->shipping_postal_code." ";
                    echo $row->shipping_country_name;
                    ?>
                </td>
            </tr>
            </table>
            </fieldset>
            <?php } ?>

            <fieldset>
            <legend><?php echo JText::_('COM_CITRUSCART_PAYMENT_INFORMATION'); ?></legend>

            <table class="admintable" style="clear: both;">
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_PAYMENT_AMOUNT'); ?>
                </td>
                <td>
                    <?php echo CitruscartHelperBase::currency( $row->order_total, $row->currency ); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_ASSOCIATED_PAYMENT_RECORDS'); ?>
                </td>
                <td>
                    <?php
                    if (!empty($row->orderpayments))
                    {
                        foreach ($row->orderpayments as $orderpayment)
                        {
                            echo JText::_('COM_CITRUSCART_PAYMENT_ID').": ".$orderpayment->orderpayment_id."<br/>";
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_BILLING_ADDRESS'); ?>
                </td>
                <td>
                    <?php
                    echo $row->billing_first_name." ".$row->billing_last_name."<br/>";
                    echo $row->billing_address_1.", ";
                    echo $row->billing_address_2 ? $row->billing_address_2.", " : "";
                    echo $row->billing_city.", ";
                    echo $row->billing_zone_name." ";
                    echo $row->billing_postal_code." ";
                    echo $row->billing_country_name .'<br/>';
					echo $row->billing_tax_number.'<br />';
					echo $row->billing_phone_1;
                    ?>
                </td>
            </tr>
            </table>

            </fieldset>

        </td>
    </tr>
    </table>

    <?php
        // fire plugin event here to enable extending the form
        JDispatcher::getInstance()->trigger('onBeforeDisplayOrderPrintOrderItems', array( $row ) );
    ?>

    <div id="orderitems">
    <fieldset>
        <legend><?php echo JText::_('COM_CITRUSCART_ITEMS_IN_ORDER'); ?></legend>

        <table class="adminlist" style="clear: both;">
        <thead>
            <tr>
                <th style="text-align: left;"><?php echo JText::_('COM_CITRUSCART_ITEM'); ?></th>
                <th style="width: 150px; text-align: center;"><?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?></th>
                <th style="width: 150px; text-align: right;"><?php echo JText::_('COM_CITRUSCART_AMOUNT'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
                <td>
                    <?php echo JText::_( $item->orderitem_name ); ?>
                    <br/>

                    <?php if (!empty($item->orderitem_attribute_names)) : ?>
                        <?php echo $item->orderitem_attribute_names; ?>
                        <br/>
                    <?php endif; ?>

                    <?php if (!empty($item->orderitem_sku)) : ?>
                        <b><?php echo JText::_('COM_CITRUSCART_SKU'); ?>:</b>
                        <?php echo $item->orderitem_sku; ?>
                        <br/>
                    <?php endif; ?>

                    <?php if ($item->orderitem_recurs) : ?>
                        <?php $recurring_subtotal = $item->recurring_price; ?>
                        <?php echo JText::_('COM_CITRUSCART_RECURRING_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_price); ?>
                        (<?php echo $item->recurring_payments . " " . JText::_('COM_CITRUSCART_PAYMENTS'); ?>, <?php echo $item->recurring_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIODS'); ?>)
                        <?php if ($item->recurring_trial) : ?>
                            <br/>
                            <?php echo JText::_('COM_CITRUSCART_TRIAL_PERIOD_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_trial_price); ?>
                            (<?php echo "1 " . JText::_('COM_CITRUSCART_PAYMENT'); ?>, <?php echo $item->recurring_trial_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIOD'); ?>)
                        <?php endif; ?>
                    <?php else : ?>
                        <b><?php echo JText::_('COM_CITRUSCART_PRICE'); ?>:</b>
                        <?php echo CitruscartHelperBase::currency( $item->orderitem_price, $row->currency ); ?>
                    <?php endif; ?>

                    <!-- onDisplayOrderItem event: plugins can extend order item information -->
				    <?php if (!empty($this->onDisplayOrderItem) && (!empty($this->onDisplayOrderItem[$i]))) : ?>
				        <div class='onDisplayOrderItem_wrapper_<?php echo $i?>'>
				        <?php echo $this->onDisplayOrderItem[$i]; ?>
				        </div>
				    <?php endif; ?>
                </td>
                <td style="text-align: center;">
                    <?php echo $item->orderitem_quantity; ?>
                </td>
                <td style="text-align: right;">
                    <?php echo CitruscartHelperBase::currency( $item->orderitem_final_price, $row->currency ); ?>
                </td>
            </tr>
        <?php $i=$i+1; $k = (1 - $k); ?>
        <?php endforeach; ?>

        <?php if (empty($items)) : ?>
            <tr>
                <td colspan="10" align="center">
                    <?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="2" style="text-align: right;">
            <?php echo JText::_('COM_CITRUSCART_SUBTOTAL'); ?>
            </th>
            <th style="text-align: right;">
            <?php echo CitruscartHelperBase::currency($order->order_subtotal, $row->currency); ?>
            </th>
        </tr>

        <?php if (!empty($row->order_discount)) : ?>
        <tr>
            <th colspan="2" style="text-align: right;">
                <?php echo JText::_('COM_CITRUSCART_DISCOUNT'); ?>
            </th>
            <td colspan="3" style="text-align: right;">
                <?php echo CitruscartHelperBase::currency($row->order_discount); ?>
            </td>
        </tr>
        <?php endif;
	              $display_tax_checkout = Citruscart::getInstance()->get('show_tax_checkout', '1');

	              switch( $display_tax_checkout )
	              {
	              	case 1 : // Tax Rates in Separate Lines
		                	foreach ( $row->ordertaxrates as $taxrate)
		                  {
		                   	$tax_desc = $taxrate->ordertaxrate_description ? $taxrate->ordertaxrate_description : 'Tax';
		                   	$amount = $taxrate->ordertaxrate_amount;
		                   	if ( $amount )
		                   	{
		                  ?>
		      <tr>
            <th colspan="2" style="text-align: right;">
							<?php echo JText::_( $tax_desc ).":"; ?>
						</th>
            <th style="text-align: right;">
							<?php echo CitruscartHelperBase::currency( $amount, $row->currency); ?>
						</th>
					</tr>
  	                  <?php
		                    }
		                  }
	              		break;
	              	case 2 : // Tax Classes in Separate Lines
		                	foreach ( $row->ordertaxclasses as $taxclass)
		                  {
		                   	$tax_desc = $taxclass->ordertaxclass_description ? $taxclass->ordertaxclass_description : 'Tax';
		                   	$amount = $taxclass->ordertaxclass_amount;
		                   	if ( $amount )
		                   	{
		                  ?>
		      <tr>
            <th colspan="2" style="text-align: right;">
							<?php echo JText::_( $tax_desc ).":"; ?>
						</th>
            <th style="text-align: right;">
							<?php echo CitruscartHelperBase::currency( $amount , $row->currency); ?>
						</th>
					</tr>
  	                  <?php
		                    }
		                  }
	              		break;
	              	case 3 : // Tax Classes and Tax Rates in Separate Lines
		                	foreach ( $row->ordertaxclasses as $taxclass)
		                  {
		                   	$tax_desc = $taxclass->ordertaxclass_description ? $taxclass->ordertaxclass_description : 'Tax';
		                   	$amount = $taxclass->ordertaxclass_amount;
		                   	if ( $amount )
		                   	{
		                  ?>
		      <tr>
            <th colspan="2" style="text-align: right;">
							<?php echo JText::_( $tax_desc ).":"; ?>
						</th>
            <th style="text-align: right;">
							<?php echo CitruscartHelperBase::currency( $amount , $row->currency); ?>
						</th>
				</tr>
  	                  <?php
		                     }
		                     foreach( $row->ordertaxrates as $taxrate )
		                     {
				                   	$tax_desc = $taxrate->ordertaxrate_description ? $taxrate->ordertaxrate_description : 'Tax';
				                   	$amount = $taxrate->ordertaxrate_amount;
				                   	if ( $amount && $taxrate->ordertaxclass_id == $taxclass->tax_class_id )
				                   	{
				                  ?>
				  <tr>
            <th colspan="2" style="text-align: right;">
							<?php echo JText::_( $tax_desc )." &nbsp;&nbsp; :"; ?></span>
						</th>
            <th style="text-align: right;">
							<?php echo CitruscartHelperBase::currency( $amount, $row->currency); ?>
						</th>
					</tr>
		  	                  <?php
		                     		}
		                     }
		                  }
	              		break;
	              	case 4 : // All in One Line
	                	if( $row->order_tax )
	                    {
	                    	?>
            <th colspan="2" style="text-align: right;">
	            	<?php
                    	if (!empty($this->show_tax)) { echo JText::_('COM_CITRUSCART_PRODUCT_TAX_INCLUDED').":"; }
                    	else { echo JText::_('COM_CITRUSCART_PRODUCT_TAX').":"; }
	            	?>
	          </th>
            <th style="text-align: right;">
							 <?php echo CitruscartHelperBase::currency($row->order_tax) ?>
						</th>
							            <?php
	                    }
	              		break;
	              }
                ?>
        <tr>
            <th colspan="2" style="text-align: right;">
            <?php echo JText::_('COM_CITRUSCART_SHIPPING'); ?>
            </th>
            <th style="text-align: right;">
            <?php echo CitruscartHelperBase::currency($row->order_shipping, $row->currency); ?>
            </th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: right;">
            <?php echo JText::_('COM_CITRUSCART_SHIPPING_TAX'); ?>
            </th>
            <th style="text-align: right;">
            <?php echo CitruscartHelperBase::currency($row->order_shipping_tax, $row->currency); ?>
            </th>
        </tr>
        <?php if ((float) $row->order_credit > (float) '0.00') : ?>
        <tr>
            <th colspan="2" style="text-align: right;">
                <?php echo JText::_('COM_CITRUSCART_STORE_CREDIT'); ?>
            </th>
            <th style="text-align: right;">
                - <?php echo CitruscartHelperBase::currency($row->order_credit, $row->currency); ?>
            </th>
        </tr>
        <?php endif; ?>
        <tr>
            <th colspan="2" style="font-size: 120%; text-align: right;">
            <?php echo JText::_('COM_CITRUSCART_TOTAL'); ?>
            </th>
            <th style="font-size: 120%; text-align: right;">
            <?php echo CitruscartHelperBase::currency($row->order_total, $row->currency); ?>
            </th>
        </tr>
        </tfoot>
        </table>
        </fieldset>
    </div>

    <?php
        // fire plugin event here to enable extending the form
        JDispatcher::getInstance()->trigger('onAfterDisplayOrderPrintOrderItems', array( $row ) );
    ?>

    <?php
        // fire plugin event here to enable extending the form
        JDispatcher::getInstance()->trigger('onAfterDisplayOrderPrint', array( $row ) );
    ?>

    <input type="hidden" name="prev" value="<?php echo intval($surrounding["prev"]); ?>" />
    <input type="hidden" name="next" value="<?php echo intval($surrounding["next"]); ?>" />
    <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
    <input type="hidden" name="task" id="task" value="" />

</form>

<script type="text/javascript">
    window.onload = CitruscartPrintPage();
    function CitruscartPrintPage()
    {
        self.print();
    }
</script>