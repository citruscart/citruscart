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
	$form = $this->form;
	$row = $this->row; JFilterOutput::objectHTMLSafe( $row );
	$histories = Citruscart::getClass( 'CitruscartHelperSubscription', 'helpers.subscription' )->getHistory( $row->subscription_id );
	Citruscart::load( 'CitruscartGrid', 'library.grid' );
	$menu = CitruscartMenu::getInstance();
?>

<div class='componentheading'>
	<span><?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_DETAILS'); ?></span>
</div>

    <?php
    	if ($menu ) { $menu->display(); }
	    echo "<< <a href='".JRoute::_("index.php?option=com_citruscart&view=subscriptions")."'>".JText::_('COM_CITRUSCART_RETURN_TO_LIST')."</a>";

	    // fire plugin event here to enable extending the form
      JDispatcher::getInstance()->trigger('onBeforeDisplaySubscriptionViewSubscriptionInfo', array( $row ) );
    ?>

    <div id="subscription_info">
        <h3><?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_INFORMATION'); ?></h3>
        <strong><?php echo JText::_('COM_CITRUSCART_PRODUCT'); ?></strong>: <?php echo $row->product_name; ?><br/>
        <strong><?php echo JText::_('COM_CITRUSCART_STATUS'); ?></strong>: <?php echo CitruscartGrid::boolean( $row->subscription_enabled ); ?><br/>
        <strong><?php echo JText::_('COM_CITRUSCART_CREATED'); ?></strong>: <?php echo JHTML::_('date', $row->created_datetime, Citruscart::getInstance()->get('date_format')); ?><br/>
        <strong><?php echo JText::_('COM_CITRUSCART_EXPIRES'); ?></strong>: <?php echo JHTML::_('date', $row->expires_datetime, Citruscart::getInstance()->get('date_format')); ?><br/>
    </div>

    <div id="order_info">
        <h3><?php echo JText::_('COM_CITRUSCART_ORDER_INFORMATION'); ?></h3>
        <strong><?php echo JText::_('COM_CITRUSCART_ORDER_ID'); ?></strong>:
           <a href="<?php echo JRoute::_( "index.php?option=com_citruscart&view=orders&task=view&id=".$row->order_id ); ?>">
           <?php echo $row->order_id; ?>
           </a>
           <br/>
    </div>

    <?php
        // fire plugin event here to enable extending the form
        JDispatcher::getInstance()->trigger('onAfterDisplaySubscriptionViewSubscriptionInfo', array( $row ) );

         // fire plugin event here to enable extending the form
        JDispatcher::getInstance()->trigger('onAfterDisplaySubscriptionView', array( $row ) );
    ?>
