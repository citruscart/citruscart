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
	JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); 
	JHTML::_('script', 'Citruscart_checkout.js', 'media/citruscart/js/');
	$form = $this->form; 
	$row = $this->row;
	$register = $this->register;
	$baseurl = "index.php?option=com_citruscart&format=raw&controller=addresses&task=getAddress&address_id="; 
?>
<div class='componentheading'>
    <span><?php echo JText::_('COM_CITRUSCART_SELECT_ADDRESSES_AND_SHIPPING_METHOD'); ?></span>
</div>

    <?php // if ($menu = CitruscartMenu::getInstance()) { $menu->display(); } ?>
    
<div id='onCheckout_wrapper'>

	<!-- Progress Bar -->
	<?php echo $this->progress; ?>

    <form action="<?php echo JRoute::_( $form['action'] ); ?>" method="post" name="adminForm" enctype="multipart/form-data">
        
        <!--    ORDER SUMMARY   -->
        <h3><?php echo JText::_('COM_CITRUSCART_ORDER_SUMMARY') ?></h3>
        <div id='onCheckoutCart_wrapper'> 
			<?php
                echo $this->orderSummary;
 		    ?>
        </div>
        
        <?php if (!empty($this->onBeforeDisplaySelectShipping)) : ?>
            <div id='onBeforeDisplaySelectShipping_wrapper'>
            <?php echo $this->onBeforeDisplaySelectShipping; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($register) : ?>
        <h3>
            <?php echo JText::_('COM_CITRUSCART_REGISTRATION') ?>
        </h3>
        <?php echo $this->form_register; ?>	
        <?php endif; ?>
       
        <h3>
            <?php echo JText::_('COM_CITRUSCART_SET_SHIPPING_AND_BILLING_ADDRESSES') ?>
        </h3>
         <?php if (!$register) :?>
        <h4>
            <?php echo JText::_('COM_CITRUSCART_YOUR_EMAIL_ADDRESS') ?>
        </h4>
       	
       	<table>
        	<tr>
            <td style="text-align: left;">
                <!--    Email Address   -->             
                <input name="email_address" id="email_address" type="text" size="48" maxlength="250" /> *
            </td>
        </tr>
		</table>
		<?php endif;?>	
			
        <table>
        <tr>
            <td style="text-align: left;">
                <!--    BILLING ADDRESS   -->             
              <div id="billingAddress">
                <h4 id='billing_address_header' class="address_header">
                    <?php echo JText::_('COM_CITRUSCART_BILLING_ADDRESS') ?>
                </h4>                
                <!--    BILLING ADDRESS FORM  -->
                <div id="billingDefaultAddress">
                   <?php echo $this->billing_address_form; ?>
                </div>
              </div>
            </td>
        </tr>
        
        <?php if ($this->showShipping) 
        {
            ?>
            <tr>
                <td style="text-align: left;">
                    <!--    SHIPPING ADDRESS   -->
                  <div id="shippingAddress">
	    	            <h4 id='shipping_address_header' class="address_header">
	    	               <?php echo JText::_('COM_CITRUSCART_SHIPPING_ADDRESS') ?>
	    	            </h4>
	    	           
	                        <div>
	                            <input id="sameasbilling" name="sameasbilling" type="checkbox" onclick="CitruscartDisableShippingAddressControls(this,this.form);" />&nbsp;
	                            <?php echo JText::_('COM_CITRUSCART_SAME_AS_BILLING_ADDRESS'); ?>:
	                        </div>
	    				
	    				<!--    SHIPPING ADDRESS FORM  -->
	    	            <div id="shippingDefaultAddress">
	    	                   <?php echo $this->shipping_address_form; ?>
	    	            </div>
    	            </div>
                </td>
            </tr>
            <?php 
        } 
        ?>
        </table>
        
        <!-- SHIPPING METHODS -->
        <div id='onCheckoutShipping_wrapper'>
            <?php echo $this->shipping_method_form; ?>
        </div>
        
        <?php if (!empty($this->onAfterDisplaySelectShipping)) : ?>
            <div id='onAfterDisplaySelectShipping_wrapper'>
            <?php echo $this->onAfterDisplaySelectShipping; ?>
            </div>
        <?php endif; ?>
            
        <h3><?php echo JText::_('COM_CITRUSCART_CONTINUE_CHECKOUT') ?></h3>
        
        <div id="validationmessage"></div>
        
        <!--    SUBMIT   -->
            <input type="button" class="btn" onclick="CitruscartPutAjaxLoader( 'validationmessage', '<?php echo JText::_('COM_CITRUSCART_VALIDATING');?>' ); CitruscartFormValidation( '<?php echo $form['validation']; ?>', 'validationmessage', 'selectpayment', document.adminForm )" value="<?php echo JText::_('COM_CITRUSCART_SELECT_PAYMENT_METHOD'); ?>" />
            <a href="<?php echo JRoute::_('index.php?option=com_citruscart&view=carts'); ?>"><?php echo JText::_('COM_CITRUSCART_RETURN_TO_SHOPPING_CART'); ?></a>
            	
    		<input type="hidden" id="currency_id" name="currency_id" value="<?php echo $this->order->currency_id; ?>" />
    		<input type="hidden" id="step" name="step" value="selectshipping" />
    		<input type="hidden" id="task" name="task" value="" />
    		
    		<?php  if (!$register) {?>
    		<input type="hidden" id="guest" name="guest" value="1" />
    		<?php } else {?>
    		<input type="hidden" id="register" name="register" value="1" />
    		<?php } ?>
        
        <?php echo $this->form['validate']; ?>
    </form>
</div>
