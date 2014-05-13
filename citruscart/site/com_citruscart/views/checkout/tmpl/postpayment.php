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

	defined('_JEXEC') or die('Restricted access');
	$doc = JFactory::getDocument();

	$doc->addScript(JUri::root().'media/citruscart/js/citruscart.js');
	$doc->addScript(JUri::root().'media/citruscart/js/citruscart_checkout.js');
	$order_link = $this->order_link;
	$plugin_html = $this->plugin_html;
?>

<div class='componentheading'>
    <span><?php echo JText::_('COM_CITRUSCART_CHECKOUT_RESULTS'); ?></span>
</div>

<?php if( !Citruscart::getInstance()->get('one_page_checkout', '0') ) : ?>
<!-- Progress Bar -->
<?php echo $this->progress; ?>
<?php endif; ?>

<?php if (!empty($this->onBeforeDisplayPostPayment)) : ?>
    <div id='onBeforeDisplayPostPayment_wrapper'>
    <?php echo $this->onBeforeDisplayPostPayment; ?>
    </div>
<?php endif; ?>

<?php echo $plugin_html; ?>

<div class="note">
	<a href="<?php echo JRoute::_($order_link); ?>">
        <?php echo JText::_('COM_CITRUSCART_CLICK_HERE_TO_VIEW_AND_PRINT_AN_INVOICE'); ?>
	</a>
</div>

<?php foreach ($this->articles as $article) : ?>
    <div class="postpayment_article">
        <?php echo $article; ?>
    </div>
<?php endforeach; ?>

<?php if (!empty($this->onAfterDisplayPostPayment)) : ?>
    <div id='onAfterDisplayPostPayment_wrapper'>
    <?php echo $this->onAfterDisplayPostPayment; ?>
    </div>
<?php endif;
