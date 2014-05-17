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

defined('_JEXEC') or die('Restricted access');
JHtml::_('stylesheet','media/citruscart/css/menu.css');
JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
JHtml::_('script', 'joomla.javascript.js', 'includes/js/');
Citruscart::load( 'CitruscartGrid', 'library.grid' );
Citruscart::load( 'CitruscartHelperOrder', 'helpers.order' );
Citruscart::load( 'CitruscartHelperCurrency', 'helpers.currency' );
$state = $this->state;
$order = $this->order;
$items = $this->orderitems;
$coupons = $this->coupons;
$display_credits = Citruscart::getInstance()->get( 'display_credits', '0' );
$currency_helper = new CitruscartHelperCurrency();
$default_currency = Citruscart::getInstance()->get('default_currencyid', '1');
?>
<div class="cartitems">
	<div class="adminlist">
		<div id="cartitems_header" class="floatbox">
			<span class="left50"><?php echo JText::_('COM_CITRUSCART_PRODUCT'); ?></span>
			<span class="left20 center"><?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?></span>
			<span class="left30 right"><?php echo JText::_('COM_CITRUSCART_TOTAL'); ?></span>
		</div>
            <?php $i=0; $k=0; ?>
            <?php foreach ($items as $item) : ?>
                <div class="row<?php echo $k; ?> floatbox cart_item_list">
                    <div class="left50">
                    	<div class="inner">
	                        <a href="<?php echo JRoute::_("index.php?option=com_citruscart&controller=products&view=products&task=view&id=".$item->product_id); ?>">
	                            <?php echo $item->orderitem_name; ?>
	                        </a>
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
	                            <?php echo JText::_('COM_CITRUSCART_RECURRING_PRICE'); ?>: <?php echo $currency_helper->format($item->recurring_price, $default_currency ); ?>
	                            (<?php echo $item->recurring_payments . " " . JText::_('COM_CITRUSCART_PAYMENTS'); ?>, <?php echo $item->recurring_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIODS'); ?>)
											            <?php if( $item->subscription_prorated ) : ?>
	                                <br/>
			                                <?php echo JText::_('COM_CITRUSCART_INITIAL_PERIOD_PRICE'); ?>: <?php echo $currency_helper->format( $item->recurring_trial_price, $default_currency ); ?>
			                                (<?php echo "1 " . JText::_('COM_CITRUSCART_PAYMENT'); ?>, <?php echo $item->recurring_trial_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIOD'); ?>)
											            <?php else : ?>
				                            <?php if ($item->recurring_trial) : ?>
			                                <br/>
			                                <?php echo JText::_('COM_CITRUSCART_TRIAL_PERIOD_PRICE'); ?>: <?php echo $currency_helper->format($item->recurring_trial_price, $default_currency ); ?>
			                                (<?php echo "1 " . JText::_('COM_CITRUSCART_PAYMENT'); ?>, <?php echo $item->recurring_trial_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIOD'); ?>)
											            <?php endif;?>
	                            <?php endif; ?>
	                        <?php else : ?>
	                            <?php echo JText::_('COM_CITRUSCART_PRICE'); ?>:
	                            <?php echo $currency_helper->format( $item->price, $default_currency ); ?>
	                        <?php endif; ?>

						    <?php if (!empty($this->onDisplayOrderItem) && (!empty($this->onDisplayOrderItem[$i]))) : ?>
						        <div class='onDisplayOrderItem_wrapper_<?php echo $i?>'>
						        <?php echo $this->onDisplayOrderItem[$i]; ?>
						        </div>
						    <?php endif; ?>

	                        <?php if( in_array($item->product_id, $coupons) ){ ?>
	                        	<span style="float: right;"><?php echo JText::_('COM_CITRUSCART_COUPON_DISCOUNT_APPLIED'); ?></span>
	                        <?php } ?>
                    	</div>
                    </div>
                    <div class="left20 center">
                        <?php echo $item->orderitem_quantity;?>
                    </div>
                    <div class="left30 right">
                    	<div class="inner">
                    		<?php echo $currency_helper->format($item->orderitem_final_price, $default_currency ); ?>
                    	</div>
                    </div>
                </div>
              	<div class="marginbot"></div>
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
            <div class="marginbot"></div>
                <div class="floatbox">
                    <span class="left50 header">
                    	<span class="inner">
                    		<?php echo JText::_('COM_CITRUSCART_SUBTOTAL'); ?>
                    	</span>
                    </span>
                    <span class="right">
                    	<span class="inner">
                    		<?php echo $currency_helper->format($order->order_subtotal,$default_currency); ?>
                    	</span>
                    </span>
                </div>

                <?php if (!empty($order->_coupons['order_price'])) : ?>
                <div class="floatbox">
                    <span class="left50 header">
                    	<span class="inner">
                    		<?php echo JText::_('COM_CITRUSCART_DISCOUNT'); ?>
                    	</span>
                    </span>
                    <span class="left50 right">
                    	<span class="inner">
                    		<?php echo $currency_helper->format( $order->order_discount, $default_currency ); ?>
                    	</span>
                    </span>
                </div>
                <?php endif; ?>
        </div>
        <div class="floatbox">
					<?php echo $this->displayTaxes(); ?>
        </div>

        <?php if( $display_credits ): ?>
        <div class="marginbot"></div>
        <div class="floatbox">
        	<span class="left50 header">
        		<span class="inner">
        			 <?php echo JText::_('COM_CITRUSCART_STORE_CREDIT'); ?>
        		</span>
            </span>
            <span class="left50 right">
            	<span class="inner">
            		- <?php echo $currency_helper->format( $order->order_credit, $default_currency ); ?>
            	</span>
            </span>
        </div>
        <?php endif; ?>

        <div class="marginbot"></div>
        <div class="floatbox">
        	<span class="left50 header">
        		<span class="inner">
        			<?php echo JText::_('COM_CITRUSCART_TOTAL'); ?>
        		</span>
            </span>
            <span class="left50 right">
            	<span class="inner">
            		<?php echo $currency_helper->format( $order->order_total, $default_currency ); ?>
            	</span>
            </span>
        </div>
</div>
