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
defined('_JEXEC') or die('Restricted access');?>
<?php JHTML::_('stylesheet', 'citruscart.css', 'media/com_citruscart/css/'); ?>
<?php Citruscart::load( 'CitruscartHelperBase', 'helpers._base' ); ?>
<?php jimport('joomla.html') ?>

<?php
$rates = array();
foreach($vars->rates as $rate){
	$r = new JObject;
	$r->value = $rate->shipping_rate_id;
	$r->text = CitruscartHelperBase::currency($rate->shipping_rate_price, $vars->order->currency_id);
	$rates[] = $r;
}
?>
<div class="shipping_rates">
<?php
echo JHTML::_( 'select.radiolist', $rates, 'shipping_rate', array() );
?>
</div>