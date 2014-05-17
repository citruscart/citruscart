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
JHtml::_('stylesheet', 'media/citruscart/css/menu.css');
JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
JHtml::_('script', 'joomla.javascript.js', 'includes/js/');
Citruscart::load( 'CitruscartGrid', 'library.grid' );
Citruscart::load( 'CitruscartHelperOrder', 'helpers.order' );
Citruscart::load( 'CitruscartHelperCurrency', 'helpers.currency' );
$state = $this->state;
$order = $this->order;
$items = $this->orderitems;
$coupons = $this->coupons;
$display_credits = $this->defines->get( 'display_credits', '0' );
$currency_helper = new CitruscartHelperCurrency();
$default_currency = $this->defines->get('default_currencyid', '1');
?>

<form id="opc-review-form" name="opc-review-form" action="" method="post">

    <div id="opc-order-summary">

    	<table class="table table-bordered table-hover">
    	<thead>
    		<tr>
    			<th class="product-name"><?php echo JText::_('COM_CITRUSCART_PRODUCT'); ?></th>
    			<th class="product-quantity"><?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?></th>
    			<th class="product-total"><?php echo JText::_('COM_CITRUSCART_TOTAL'); ?></th>
    		</tr>
        </thead>
        <tbody>
            <?php $i=0; $k=0; ?>
            <?php foreach ($items as $item) : ?>
                <tr>
                    <td class="product-name">
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
                    </td>
                    <td class="product-quantity">
                        <?php echo $item->orderitem_quantity;?>
                    </td>
                    <td class="product-total">
                    	<div class="inner">
                    		<?php echo $currency_helper->format($item->orderitem_final_price, $default_currency ); ?>
                    	</div>
                    </td>
                </tr>
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="2">
            	<span class="inner">
            		<?php echo JText::_('COM_CITRUSCART_SUBTOTAL'); ?>
            	</span>
            </td>
            <td>
            	<span class="inner">
            		<?php echo $currency_helper->format($order->order_subtotal,$default_currency); ?>
            	</span>
            </td>
        </tr>

        <?php if (!empty($order->_coupons['order_price'])) : ?>
        <tr>
            <td colspan="2">
            	<span class="inner">
            		<?php echo JText::_('COM_CITRUSCART_DISCOUNT'); ?>
            	</span>
            </td>
            <td>
            	<span class="inner">
            		<?php echo $currency_helper->format( $order->order_discount, $default_currency ); ?>
            	</span>
            </td>
        </tr>
        <?php endif; ?>

        <?php echo $this->displayTaxes(); ?>

        <?php if ( $display_credits && (float) $order->order_credit > (float) '0.00' ): ?>
        <tr>
        	<td colspan="2">
        		<span class="inner">
        			 <?php echo JText::_('COM_CITRUSCART_STORE_CREDIT'); ?>
        		</span>
            </td>
            <td>
            	<span class="inner">
            		- <?php echo $currency_helper->format( $order->order_credit, $default_currency ); ?>
            	</span>
            </td>
        </tr>
        <?php endif; ?>

        <tr>
        	<td colspan="2">
        		<span class="inner">
        			<?php echo JText::_('COM_CITRUSCART_TOTAL'); ?>
        		</span>
            </td>
            <td>
            	<span class="inner">
            		<?php echo $currency_helper->format( $order->order_total, $default_currency ); ?>
            	</span>
            </td>
        </tr>

        </tfoot>
    </table>

    <?php if ($this->defines->get('coupons_enabled') && $this->coupons_present) { ?>
    <?php if (!$this->defines->get('multiple_usercoupons_enabled') && $order->getUserCoupons()) { /*stop*/ } else { ?>
    <fieldset id="opc-coupon-form">
        <div id="opc-coupon-validation"></div>
        <div class="input-append" id="opc-coupon-input">
            <input class="span2" id="coupon_code" type="text" name="coupon_code" />
            <button id="opc-coupon-button" class="btn" type="button"><?php echo JText::_( "COM_CITRUSCART_ADD_COUPON_TO_ORDER" ); ?></button>
        </div>
        <div id="opc-coupons"></div>
    </fieldset>
    <?php } } ?>

    <?php if ( $this->defines->get('display_credits', '0') && (float) $this->userinfo->credits_total > (float) '0.00' ) { ?>

        <fieldset id="opc-credit-form">
            <div id="opc-credit-validation"></div>

            <div id="credits_form">
                <label for="apply_credit_amount"><?php echo JText::_('COM_CITRUSCART_STORE_CREDIT'); ?></label>
                <div class="help-block"><?php echo sprintf( JText::_('COM_CITRUSCART_YOU_HAVE_STORE_CREDIT'), CitruscartHelperBase::currency( $this->userinfo->credits_total, $this->defines->get( 'default_currencyid', '1' ) ) ); ?></div>
                <div class="input-append" id="opc-credit-input">
                    <input class="span2" type="text" id="apply_credit_amount" name="apply_credit_amount" />
                    <button id="opc-credit-button" class="btn" type="button"><?php echo JText::_( "COM_CITRUSCART_APPLY_CREDIT_TO_ORDER" ); ?></button>
                </div>

            </div>
            <div id='opc-credits'></div>
        </fieldset>

    <?php } ?>

    <fieldset id="opc-notes">
        <label><?php echo JText::_( "COM_CITRUSCART_NOTES" ); ?></label>
        <textarea name="customer_note"></textarea>
    </fieldset>

    <?php
    if( $this->defines->get('require_terms', '0') )
    {
        $terms_article = $this->defines->get('article_terms');
        $terms_link = JRoute::_('index.php?option=com_content&view=article&id='.$terms_article );
        ?>
        <fieldset>
        <label for="terms-conditions" class="checkbox">
            <input type="checkbox" name="terms-conditions" value="1" id="terms-conditions" />
            <?php
            if ($terms_article) {
                if( $this->defines->get('require_terms_modal', '0') ) {
                    echo CitruscartUrl::popup( $terms_link, JText::_('COM_CITRUSCART_ACCEPT_TERMS_AND_CONDITIONS') );
                } else {
                    echo '<a href="'.$terms_link.'" target="_blank">'.JText::_('COM_CITRUSCART_ACCEPT_TERMS_AND_CONDITIONS').'</a>';
                }
            } else {
                echo JText::_('COM_CITRUSCART_ACCEPT_TERMS_AND_CONDITIONS');
            }
            ?>
        </label>
        </fieldset>
        <?php
    } ?>
    <div id="opc-review-validation"></div>
    <a id="opc-review-button" class="btn btn-success btn-large"><?php echo JText::_('COM_CITRUSCART_PLACE_ORDER') ?></a>
</form>
