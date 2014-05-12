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

<form id="opc-payment-form" name="opc-payment-form" action="" method="post">

<ul class="unstyled control-group">
<?php if (empty($this->showPayment)) { ?>
    <li class="control">
        <p class="text-info">
            <?php echo JText::_( "COM_CITRUSCART_NO_PAYMENT_NECESSARY" ); ?>
        </p>
    </li>
<?php } else { ?>
    <?php if (!empty($this->payment_plugins)) { ?>
    
        <?php foreach($this->payment_plugins as $payment_plugin) { ?>
            <li class="control <?php echo $payment_plugin->element; ?>">
                <label class="radio <?php echo $payment_plugin->element; ?>">
                    <input class="payment-plugin required" value="<?php echo $payment_plugin->element; ?>" onclick="Opc.payment.getPluginForm('<?php echo $payment_plugin->element; ?>', 'opc-payment-method-form-container', '<?php echo JText::_('COM_CITRUSCART_GETTING_PAYMENT_METHOD'); ?>');" name="payment_plugin" type="radio" <?php echo (!empty($payment_plugin->checked)) ? "checked" : ""; ?> />
                    <div class="paymentOptionName <?php echo $payment_plugin->element;  ?>">
                    <span class="paymentOptionNameText <?php echo $payment_plugin->element;  ?>"><?php echo $payment_plugin->getName();  ?></span>
                    </div>
                                    
                </label>
            </li>
        <?php } ?>
        
        <div id='opc-payment-method-form-container'>
            <?php if (!empty($this->payment_form_div)) { ?>
                <?php echo $this->payment_form_div;?>
            <?php } ?>
        </div>
        
    <?php } ?>
<?php } ?>
</ul>

    <div id="opc-payment-validation"></div>
    
    <a id="opc-payment-button" class="btn btn-primary"><?php echo JText::_('COM_CITRUSCART_CONTINUE') ?></a>

</form>
