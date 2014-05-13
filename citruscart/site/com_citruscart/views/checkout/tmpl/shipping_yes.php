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
	$shipping_rates_text = JText::_('COM_CITRUSCART_GETTING_SHIPPING_RATES');
	$one_page = Citruscart::getInstance()->get( 'one_page_checkout', '0' );

	$currency = Citruscart::getInstance()->get( 'default_currencyid', 1);

if(!$one_page ): ?>

<h3><?php echo JText::_('COM_CITRUSCART_SELECT_A_SHIPPING_METHOD'); ?></h3>
<?php endif; ?>
<p><?php echo JText::_('COM_CITRUSCART_PLEASE_SELECT_YOUR_PREFERRED_SHIPPING_METHOD_BELOW'); ?>:</p>

<input type="hidden" id="shippingrequired" name="shippingrequired" value="1" />
<?php
    if (!empty($this->rates))
    {
        foreach ($this->rates as $rate)
        {
            $checked = "";

            if ( !empty($this->default_rate) && $this->default_rate['name'] == $rate['name'] )
            {
            	$checked = "checked";
            }
            ?>
            <input id="shipping_<?php echo $rate['element']; ?>" name="shipping_plugin" rel="<?php echo $rate['name']; ?>" type="radio" value="<?php echo $rate['element'] ?>" onClick="citruscartGrayOutAddressDiv(); citruscartSetShippingRate('<?php echo $rate['name']; ?>','<?php echo $rate['price']; ?>',<?php echo $rate['tax']; ?>,<?php echo $rate['extra']; ?>, '<?php echo $rate['code']; ?>', true );" <?php echo $checked; ?> />
            <label for="shipping_<?php echo $rate['element']; ?>" onClick="citruscartGrayOutAddressDiv(); citruscartSetShippingRate('<?php echo $rate['name']; ?>','<?php echo $rate['price']; ?>',<?php echo $rate['tax']; ?>,<?php echo $rate['extra']; ?>, '<?php echo $rate['code']; ?>', true );"><?php echo $rate['name']; ?> ( <?php echo CitruscartHelperBase::currency( $rate['total'], $currency ); ?> )</label><br />
            <br/>
            <?php
        }
    }
        else
    {
        ?>
        <div class="note">
	        <?php echo JText::_('COM_CITRUSCART_NO_SHIPPING_RATES_FOUND'); ?>
        </div>
        <input type="button" class="btn" value="<?php echo JText::_( "COM_CITRUSCART_GET_SHIPPING_RATES" ); ?>" onclick="citruscartGetShippingRates( 'onCheckoutShipping_wrapper', document.adminForm );">
        <?php
    }
?>
<?php $setval = false;?>
<?php if(count($this->rates)==1 && ($this->rates['0']['name'] == $this->default_rate['name'])) $setval= true;?>
<input type="hidden" name="shipping_price" id="shipping_price" value="<?php echo $setval ? $this->rates['0']['price'] : "";?>" />
<input type="hidden" name="shipping_tax" id="shipping_tax" value="<?php echo $setval ? $this->rates['0']['tax'] : "";?>" />
<input type="hidden" name="shipping_name" id="shipping_name" value="<?php echo $setval ? $this->rates['0']['name'] : "";?>" />
<input type="hidden" name="shipping_code" id="shipping_code" value="<?php echo $setval ? $this->rates['0']['code'] : "";?>" />
<input type="hidden" name="shipping_extra" id="shipping_extra" value="<?php echo $setval ? $this->rates['0']['extra'] : "";?>" />

<?php if( !$one_page ):?>
<div id='shipping_form_div' style="padding-top: 10px;"></div>
<!--    COMMENTS   -->
<h3><?php echo JText::_('COM_CITRUSCART_SHIPPING_NOTES') ?></h3>
<?php echo JText::_('COM_CITRUSCART_ADD_OPTIONAL_NOTES_FOR_SHIPMENT_HERE'); ?>:
<br/>
<textarea id="customer_note" name="customer_note" rows="5" cols="70"></textarea>
<?php endif;?>

<?php
if (!empty($this->default_rate) ) :
	$default_rate = $this->default_rate; ?>
<script type="text/javascript">
window.addEvent( 'domready', function() {
	citruscartGrayOutAddressDiv();
	citruscartSetShippingRate('<?php echo $default_rate['name']; ?>','<?php echo $default_rate['price']; ?>',<?php echo $default_rate['tax']; ?>,<?php echo $default_rate['extra']; ?>, '<?php echo $default_rate['code']; ?>', '<?php echo JText::_('COM_CITRUSCART_UPDATING_SHIPPING_RATES')?>', '<?php echo JText::_('COM_CITRUSCART_UPDATING_CART')?>', true );
});
</script>
<?php endif;
