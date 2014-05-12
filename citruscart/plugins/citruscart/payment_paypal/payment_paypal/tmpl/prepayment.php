<?php 
/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

 ?>

<form action='<?php echo $vars->post_url; ?>' method='post'>

<!--USER INFO-->
    <input type='hidden' name='first_name' value='<?php echo $vars->first_name; ?>'>
    <input type='hidden' name='last_name' value='<?php echo $vars->last_name; ?>'>
    <input type='hidden' name='email' value='<?php echo $vars->email; ?>'>

<!--SHIPPING ADDRESS PROVIDED-->
    <input type='hidden' name='address1' value='<?php echo $vars->address_1; ?>'>
    <input type='hidden' name='address2' value='<?php echo $vars->address_2; ?>'>
    <input type='hidden' name='city' value='<?php echo $vars->city; ?>'>
    <input type='hidden' name='country' value='<?php echo $vars->country; ?>'>
    <input type='hidden' name='state' value='<?php echo $vars->region; ?>'>
    <input type='hidden' name='zip' value='<?php echo $vars->postal_code; ?>'>

    <?php
    switch ($vars->cmd) {
        case "_xclick-subscriptions":
            ?>
            <!--ORDER INFO-->
            <?php // for cart Paypal payments, custom is the product_id? ?>
            <input type='hidden' name='custom' value='<?php echo $vars->orderpayment_id; ?>'>
            <input type='hidden' name='invoice' value='<?php echo $vars->orderpayment_id; ?>'>
            <input type='hidden' name='item_name' value='<?php echo JText::_('COM_CITRUSCART_ORDER_NUMBER').": ".$vars->order_id; ?>'>
            <input type='hidden' name='item_number' value='<?php echo $vars->order_id; ?>'>

            <!--SUB INFO-->
            <input type="hidden" name="sra" value="1" />
            <input type="hidden" name="src" value="1" />
            <input type="hidden" name="no_shipping" value="1" />
            <input type="hidden" name="srt" value="<?php echo $vars->order->recurring_payments; ?>" />
            <input type="hidden" name="a3" value="<?php echo CitruscartHelperBase::number( $vars->order->recurring_amount , array( 'thousands' =>'', 'decimal'=> '.' ) ); ?>" />
            <input type="hidden" name="p3" value="<?php echo $vars->order->recurring_period_interval; ?>" />
            <input type="hidden" name="t3" value="<?php echo $vars->order->recurring_period_unit; ?>" />

            <?php if ($vars->order->recurring_trial): ?>
                <input type="hidden" name="a1" value="<?php if( (float) $vars->order->recurring_trial_price == (float) '0.00' ) echo '0'; else echo CitruscartHelperBase::number( $vars->order->recurring_trial_price, array( 'thousands' =>'', 'decimal'=> '.' ) ); ?>" />
                <input type="hidden" name="p1" value="<?php echo $vars->order->recurring_trial_period_interval; ?>" />
                <input type="hidden" name="t1" value="<?php echo $vars->order->recurring_trial_period_unit; ?>" />

            <?php if ($vars->order->recurring_trial_period_interval2): ?>
                <input type="hidden" name="a2" value="<?php if( (float) $vars->order->recurring_trial_price2 == (float) '0.00' ) echo '0'; else echo CitruscartHelperBase::number( $vars->order->recurring_trial_price2, array( 'thousands' =>'', 'decimal'=> '.' ) ); ?>" />
                <input type="hidden" name="p2" value="<?php echo $vars->order->recurring_trial_period_interval2; ?>" />
                <input type="hidden" name="t2" value="<?php echo $vars->order->recurring_trial_period_unit2; ?>" />
            <?php endif;?>
            <?php endif; ?>

            <div id="payment_paypal">
                <div class="prepayment_message">
                    <?php echo JText::_('COM_CITRUSCART_CITRUSCART_PAYPAL_PAYMENT_STANDARD_PREPARATION_MESSAGE_RECURRING_ONLY'); ?>
                </div>
                <div class="prepayment_action">
                    <div style="float: left; padding: 10px;"><input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but20.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" /></div>
                    <div style="float: left; padding: 10px;"><?php echo "<b>".JText::_('COM_CITRUSCART_CHECKOUT_AMOUNT').":</b> ".CitruscartHelperBase::currency( $vars->orderpayment_amount ); ?></div>
                    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
                    <div style="clear: both;"></div>
                </div>
            </div>

            <?php
            break;
        case "_cart":
        default:
            ?>
            <!--CART INFO AGGREGATED-->
            <!--ORDER INFO-->
            <?php // for cart Paypal payments, custom is the orderpayment_id ?>
            <input type='hidden' name='custom' value='<?php echo $vars->orderpayment_id; ?>'>
            <?php
            $i =1;
            foreach ($vars->orderitems as $item) {

             //TODO
             /* CitruscartHelperBase::number( @$item->orderitem_final_price / @$item->orderitem_quantity, array( 'thousands' =>'', 'decimal'=> '.' ) );
              * CitruscartHelperBase::number( @$item->orderitem_price + @$item->orderitem_attributes_price, array( 'thousands' =>'', 'decimal'=> '.' ) );
              *  THis doesn't work because it doesn't take into account pricing discounts from coupons
              * */

                ?>
              <input type='hidden' name='amount_<?php echo $i;?>' value='<?php echo CitruscartHelperBase::number( $item->orderitem_final_price / $item->orderitem_quantity, array( 'thousands' =>'', 'decimal'=> '.' ) ); ?>'>
                <input type='hidden' name='item_name_<?php echo $i;?>' value='<?php echo $item->_description;?>'>
                <input type='hidden' name='item_number_<?php echo $i;?>' value='<?php echo $item->product_id; ?>'>
                <input type='hidden' name='quantity_<?php echo $i;?>' value='<?php echo $item->orderitem_quantity; ?>'>
                <?php
                $i++;
            }
            ?>
            <input type='hidden' name='tax_cart' value='<?php echo $vars->order->order_tax; ?>'>
            <input type='hidden' name='handling_cart' value='<?php echo $vars->order->order_shipping + $vars->order->order_shipping_tax; ?>'>
            <input type='hidden' name='discount_amount_cart' value='<?php echo $vars->order->order_discount;?>'>
            <input type="hidden" name="upload" value="1">
            <input type='hidden' name='invoice' value='<?php echo $vars->orderpayment_id; ?>'>

            <?php if ($vars->mixed_cart) { ?>
                <div class="note_green">
                    <span class="alert"><?php echo JText::_('COM_CITRUSCART_PLEASE_NOTE') ?></span>
                    <?php echo JText::_('COM_CITRUSCART_MIXED_CART_MESSAGE'); ?>
                </div>

                <div id="payment_paypal">
                    <div class="prepayment_message">
                        <?php echo JText::_('COM_CITRUSCART_PAYPAL_PAYMENT_STANDARD_PREPARATION_MESSAGE_MIXED_CART'); ?>
                    </div>
                    <div class="prepayment_action">
                        <div style="float: left; padding: 10px;"><input type="image" src="<?php echo $vars->img_url_mixed; ?>" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" /></div>
                        <div style="float: left; padding: 10px;"><?php echo "<b>".JText::_('COM_CITRUSCART_FIRST_CHECKOUT_AMOUNT').":</b> ".CitruscartHelperBase::currency( $vars->orderpayment_amount ); ?></div>
                        <div style="float: left; padding: 10px;"><?php echo "<b>".JText::_('COM_CITRUSCART_SECOND_CHECKOUT_AMOUNT').":</b> ".CitruscartHelperBase::currency( $vars->amount ); ?></div>
                        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
                        <div style="clear: both;"></div>
                    </div>
                </div>
            <?php } else { ?>
                <div id="payment_paypal">
                    <div class="prepayment_message">
                        <?php echo JText::_('COM_CITRUSCART_PAYPAL_PAYMENT_STANDARD_PREPARATION_MESSAGE'); ?>
                    </div>
                    <div class="prepayment_action">
                        <div style="float: left; padding: 10px;"><input type="image" src="<?php echo $vars->img_url_std; ?>" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" /></div>
                        <div style="float: left; padding: 10px;"><?php echo "<b>".JText::_('COM_CITRUSCART_CHECKOUT_AMOUNT').":</b> ".CitruscartHelperBase::currency( $vars->orderpayment_amount ); ?></div>
                        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
                        <div style="clear: both;"></div>
                    </div>
                </div>
            <?php } ?>
            <?php
            break;
    }
    ?>

<!--PAYPAL VARIABLES-->
    <input type='hidden' name='cmd' value='<?php echo $vars->cmd; ?>'>
    <input type='hidden' name='rm' value='2'>
    <input type="hidden" name="business" value="<?php echo $vars->merchant_email; ?>" />
    <input type='hidden' name='return' value='<?php echo JRoute::_( $vars->return_url ); ?>'>
    <input type='hidden' name='cancel_return' value='<?php echo JRoute::_( $vars->cancel_url ); ?>'>
    <input type="hidden" name="notify_url" value="<?php echo JRoute::_( $vars->notify_url ); ?>" />
    <input type='hidden' name='currency_code' value='<?php echo $vars->currency_code; ?>'>
    <input type='hidden' name='no_note' value='1'>
</form>
