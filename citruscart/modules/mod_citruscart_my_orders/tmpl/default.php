<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
$currency_helper = CitruscartHelperBase::getInstance( 'Currency' );

// Add CSS
$document->addStyleSheet( JURI::root(true).'/modules/mod_citruscart_my_orders/tmpl/mod_citruscart_my_orders.css');

if (!empty($orders))
{
    $count=0;
    foreach (@$orders as $order) : ?>
        <div class="mod_citruscart_my_orders_item">
            <?php if ($params->get('display_date')) { ?>
                <span class="mod_citruscart_my_orders_item_date"><a href="<?php echo $order->link ?>"><?php echo JHTML::_('date', $order->created_date, Citruscart::getInstance()->get('date_format')); ?></a></span><br/>
            <?php } ?>
            <?php if ($params->get('display_amount')) { ?>
                <span class="mod_citruscart_my_orders_item_amount"><b><?php echo JText::_('COM_CITRUSCART_AMOUNT'); ?>:</b> <?php echo $currency_helper->_($order->order_total); ?></span>
            <?php } ?>
            <?php if ($params->get('display_id')) { ?>
                <span class="mod_citruscart_my_orders_item_id">(#<?php echo $order->order_id; ?>)</span><br/>
            <?php } ?>
            <?php if ($params->get('display_state')) { ?>
                <span class="mod_citruscart_my_orders_item_status"><b><?php echo JText::_('COM_CITRUSCART_STATUS'); ?>:</b> <?php echo JText::_( $order->order_state_name ); ?></span><br/>
            <?php } ?>
        </div>
        <?php
    endforeach;
}
    elseif ($display_null == '1')
{
    echo JText::_( $null_text );
}