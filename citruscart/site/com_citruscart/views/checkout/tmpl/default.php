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
    JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
    JHtml::_('script', 'media/citruscart/js/common.js', false, false);
    JHtml::_('script', 'media/citruscart/js/citruscart_checkout_onepage.js', false, false);
    JHtml::_('script', 'media/citruscart/js/citruscart_checkout.js', false, false);
    JHtml::_('stylesheet', 'media/citruscart/css/citruscart_checkout_onepage.css');
	$form = $this->form;
	$row = $this->row;
	$baseurl = "index.php?option=com_citruscart&format=raw&controller=addresses&task=getAddress&address_id=";
?>
<div class='componentheading'>
    <span><?php echo JText::_('COM_CITRUSCART_SELECT_ADDRESSES_AND_SHIPPING_METHOD'); ?></span>
</div>
  <div class="naviagtion header">
	<?php
		require_once(JPATH_SITE.'/administrator/components/com_citruscart/helpers/toolbar.php');
	 	$toolbar = new CitruscartToolBar();
	 	$toolbar->renderLinkbar();

	?>
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

        <h3>
            <?php echo JText::_('COM_CITRUSCART_SELECT_SHIPPING_AND_BILLING_ADDRESSES') ?>
        </h3>

        <table>
        <tr>
            <td colspan="2">
                <div class='note'>
	                <?php $text = JText::_('COM_CITRUSCART_CLICK_HERE_TO_MANAGE_YOUR_STORED_ADDRESSES')."."; ?>
	                <?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&view=addresses&tmpl=component", $text, array('update' => true) );  ?>
                    <?php echo JText::_('COM_CITRUSCART_CHECKOUT_MANAGE_ADDRESSES_INSTRUCTIONS'); ?>
                </div>
            </td>
        </tr>

        <tr>
            <td style="text-align: left;">
            	<div id="billingAddress">
                <!--    BILLING ADDRESS   -->
                <h4 id='billing_address_header' class="address_header">
                    <?php echo JText::_('COM_CITRUSCART_BILLING_ADDRESS') ?>
                </h4>
                <?php
                    if (!empty($this->addresses))
                    {
                        $billattribs = array(
                           'class' => 'inputbox',
                           'size' => '1',
                           'onchange' => "citruscartDoTask('$baseurl'+this.options[this.selectedIndex].value, 'billingDefaultAddress', ''); CitruscartGetCheckoutTotals();"
                        );

                        // display select list of stored addresses
                        echo CitruscartSelect::address( JFactory::getUser()->id, $this->billing_address->address_id, 'billing_address_id', 1, $billattribs, 'billing_address_id', false );

                        if (count($this->addresses) == 1)
                        {
                            echo "<input type=\"hidden\" id=\"billing_address_id\" name=\"billing_address_id\" value=\"" . $this->billing_address->address_id . "\" />";
                        }
                    }
                ?>

                <!--    BILLING ADDRESS FORM  -->
                <span id="billingDefaultAddress">
                   <?php if (empty($this->addresses)) : ?>
                       <?php echo $this->billing_address_form; ?>
                   <?php else : ?>
                   <?php echo $this->default_billing_address; ?>
                   <?php endif; ?>
                </span>
              </div>
            </td>
        </tr>
        <tr>
            <td style="text-align: left;">
                <!-- SHIPPING ADDRESS   -->
                <?php if($this->showShipping) { ?>
              <div id="shippingAddress">
	            <h4 id='shipping_address_header' class="address_header">
	               <?php echo JText::_('COM_CITRUSCART_SHIPPING_ADDRESS') ?>
	            </h4>

	            <?php
                if (!empty($this->addresses))
                {	                $shipattribs = array(
	                   'class' => 'inputbox',
	                   'size' => '1',
	                   'onchange' => "citruscartGrayOutAddressDiv('".JText::_('COM_CITRUSCART_UPDATING_ADDRESS')."');citruscartDoTask('$baseurl'+this.options[this.selectedIndex].value, 'shippingDefaultAddress', '', '', false); citruscartGetShippingRates( 'onCheckoutShipping_wrapper', document.adminForm, CitruscartDeleteCombinedGrayDiv ); "
	                ); // CitruscartGetCheckoutTotals();

	                // display select list of stored addresses
	                echo CitruscartSelect::address( JFactory::getUser()->id, $this->shipping_address->address_id, 'shipping_address_id', 2, $shipattribs, 'shipping_address_id', false );

	               	if (count($this->addresses) == 1)
	               	{
	               		echo "<input type=\"hidden\" id=\"shipping_address_id\" name=\"shipping_address_id\" value=\"" . $this->shipping_address->address_id . "\" />";
	               	}
				}
				?>

                <?php if (empty($this->addresses)) : ?>
                    <div>
                        <input id="sameasbilling" name="sameasbilling" type="checkbox" onclick="citruscartDisableShippingAddressControls(this,this.form);" />&nbsp;
                        <?php echo JText::_('COM_CITRUSCART_SAME_AS_BILLING_ADDRESS'); ?>:
                    </div>
				<?php endif; ?>

				<!--    SHIPPING ADDRESS FORM  -->
	            <span id="shippingDefaultAddress">
	               <?php if (empty($this->addresses)) : ?>
	                   <?php echo $this->shipping_address_form; ?>
	               <?php else : ?>
	               <?php echo $this->default_shipping_address; ?>
	               <?php endif; ?>
	            </span>
	            	</div>
            </td>
        </tr>
         <?php } ?>
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
            <input type="button" class="btn" onclick="citruscartPutAjaxLoader( 'validationmessage', '<?php echo JText::_('COM_CITRUSCART_VALIDATING');?>' ); citruscartFormValidation( '<?php echo $form['validation']; ?>', 'validationmessage', 'selectpayment', document.adminForm )" value="<?php echo JText::_('COM_CITRUSCART_SELECT_PAYMENT_METHOD'); ?>" />
            <a href="<?php echo JRoute::_('index.php?option=com_citruscart&view=carts'); ?>"><?php echo JText::_('COM_CITRUSCART_RETURN_TO_SHOPPING_CART'); ?></a>

    		<input type="hidden" id="currency_id" name="currency_id" value="<?php echo $this->order->currency_id; ?>" />
    		<input type="hidden" id="step" name="step" value="selectshipping" />
    		<input type="hidden" id="task" name="task" value="" />

        <?php echo $this->form['validate']; ?>
    </form>
</div>
