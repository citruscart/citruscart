<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $shipping_rates_text = JText::_('COM_CITRUSCART_GETTING_SHIPPING_RATES'); ?>

<h3><?php echo JText::_('COM_CITRUSCART_SELECT_A_SHIPPING_METHOD') ?></h3>

<input type="button" class="btn btn-danger" onclick="CitruscartGetShippingRates( 'onCheckoutShipping_wrapper', this.form, '<?php echo $shipping_rates_text; ?>' )" value="<?php echo JText::_("COM_CITRUSCART_CLICK_HERE_TO_UPDATE_CUSTOMERS_SHIPPING_RATES"); ?>" />
<p><?php echo JText::_('COM_CITRUSCART_PLEASE_SELECT_YOUR_PREFERRED_SHIPPING_METHOD_BELOW'); ?>:</p>

<input type="hidden" id="shippingrequired" name="shippingrequired" value="1" />

<?php
    if (!empty($this->rates)) 
    {      
        foreach ($this->rates as $rate) 
        {
            $checked = "";
            if (!empty($this->default_rate) && $this->default_rate['name'] == $rate['name'] )
            {
            	$checked = "checked";                        
            }        	        		
            ?>
            <input name="shipping_plugin" type="radio" value="<?php echo $rate['element'] ?>" onClick="CitruscartSetShippingRate('<?php echo $rate['name']; ?>','<?php echo $rate['price']; ?>',<?php echo $rate['tax']; ?>,<?php echo $rate['extra']; ?>, '<?php echo $rate['code']; ?>');" <?php echo $checked; ?> /> <?php echo $rate['name']; ?> ( <?php echo CitruscartHelperBase::currency( $rate['total'] ); ?> )<br />
            <br/>
            <?php
        }
    }
        else
    {
        ?>
        <div class="note">
        <?php echo JText::_('COM_CITRUSCART_NO_SHIPPING_NOTE'); ?>
        </div>
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

    
<?php if(!Citruscart::getInstance()->get('one_page_checkout', '0')):?>
<div id='shipping_form_div' style="padding-top: 10px;"></div>
<!--    COMMENTS   -->     
<h3><?php echo JText::_('COM_CITRUSCART_SHIPPING_NOTES') ?></h3>
<?php echo JText::_('COM_CITRUSCART_ADD_OPTIONAL_NOTES_FOR_SHIPMENT_HERE'); ?>:
<br/>
<textarea id="customer_note" name="customer_note" rows="5" cols="70"></textarea>
<?php endif;?>

<?php if (!empty($this->default_rate)) : ?>
    <?php $default_rate = $this->default_rate; ?>
    <script type="text/javascript">
        window.onload = CitruscartSetShippingRate('<?php echo $default_rate['name']; ?>','<?php echo $default_rate['price']; ?>',<?php echo $default_rate['tax']; ?>,<?php echo $default_rate['extra']; ?>, '<?php echo $default_rate['code']; ?>');
    </script>
<?php endif; ?>
<input type="hidden" name="subtask" id="subtask" value="payment" />