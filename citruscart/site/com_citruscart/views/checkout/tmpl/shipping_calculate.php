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
<?php  if (!Citruscart::getInstance()->get('one_page_checkout')) :?>
	<h3><?php echo JText::_('COM_CITRUSCART_SELECT_A_SHIPPING_METHOD') ?></h3>
	<input type="button" class="btn" value="<?php echo JText::_( "COM_CITRUSCART_GET_SHIPPING_RATES" ); ?>" onclick="CitruscartGetShippingRates( 'onCheckoutShipping_wrapper', document.adminForm ); CitruscartGetPaymentOptions( 'onCheckoutPayment_wrapper', document.adminForm ); " />
<?php endif; ?>
<input type="hidden" id="shippingrequired" name="shippingrequired" value="1"  />
<div class="note">
	<?php echo JText::_('COM_CITRUSCART_NO_SHIPPING_RATES_FOUND'); ?>
</div>
<input type="button" class="btn" value="<?php echo JText::_( "COM_CITRUSCART_GET_SHIPPING_RATES" ); ?>" onclick="CitruscartGetShippingRates( 'onCheckoutShipping_wrapper', document.adminForm ); CitruscartGetPaymentOptions( 'onCheckoutPayment_wrapper', document.adminForm ); " />
