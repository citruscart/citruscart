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


JHTML::_('script', 'citruscart.js', 'media/citruscart/js/');
JHTML::_('script', 'citruscart_orders.js', 'media/citruscart/js/');
$form = $this->form;
$row = $this->row;
JFilterOutput::objectHTMLSafe( $row );
?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm">

<table>
<tr>
	<td style="width: 70%; vertical-align: top;">

        <!-- Start Products in Order section -->
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="text-align: left;">
                       <?php echo JText::_('COM_CITRUSCART_PRODUCTS_IN_ORDER'); ?>
                    </th>
                    <th style="text-align: center; width: 20%;" >
                       <?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&controller=orders&task=selectproducts&tmpl=component", JText::_('COM_CITRUSCART_ADD_PRODUCTS_TO_ORDER') ); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="2">
	                <div id="order_products_div">
	                    <?php include("orderproducts.php"); ?>
	                </div>
                </td>
            </tr>
            </tbody>
        </table>
        <!-- End Products in Order section -->

	    <!-- Start Addresses section -->
	    <table style="clear: both; width: 100%;">
	        <tr>
	            <td style="width: 50%; vertical-align: top;">

                    <?php include("form_address_billing.php"); ?>
	                <div id="billingSelectedAddressDiv" style="padding-left: 5px;"></div>

	            </td>
	            <td style="width: 50%; vertical-align: top;">

	                <?php include("form_address_shipping.php"); ?>
	                <div id="shippingSelectedAddressDiv" style="padding-left: 5px;"></div>
	            </td>
	        </tr>
	    </table>
	    <!-- End Addresses section -->

	</td>
	<td style="width: 30%; vertical-align: top;">

        <!-- Start General information section -->
        <table class="table table-striped table-bordered">
        <thead>
           <tr>
               <th colspan="2" style="text-align: left;"><?php echo JText::_('COM_CITRUSCART_GENERAL_INFORMATION'); ?></th>
           </tr>
        </thead>
        <tbody>
            <tr>
                <th style="width: 100px;" class="key">
                     <?php echo JText::_('COM_CITRUSCART_ORDER_CURRENCY'); ?>:
                </th>
                <td>
                    <?php echo CitruscartSelect::currency( $row->order_currency_id, 'order_currency_id', '', 'order_currency_id', false ); ?>
                </td>
            </tr>
            <tr>
                <th style="width: 100px;" class="key">
                     <?php echo JText::_('COM_CITRUSCART_CUSTOMER_INFORMATION'); ?>:
                </th>
                <td>
                    <?php echo $row->userinfo->name .' [ '.$row->user_id.' ]'; ?>
                    <br/>
                    &nbsp;&bull;&nbsp;&nbsp;<?php echo $row->userinfo->email .' [ '.$row->userinfo->username.' ]'; ?>
                </td>
            </tr>
            <tr>
                <th style="width: 100px;" class="key">
                     <?php echo JText::_('COM_CITRUSCART_EMAIL'); ?>:
                </th>
                <td>
                    <input type="text" name="user_email"
                            id="user_email" value="<?php echo $row->userinfo->email; ?>"
                            size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <?php echo JText::_('COM_CITRUSCART_EMAIL_ORDER_CONFIRMATION_TO_USER'); ?>
                    <input id="emailorderconfirmation" name="emailorderconfirmation" type="checkbox" checked="checked"/>
                </td>
            </tr>
        </tbody>
        </table>
        <!-- End General information section -->

        <!-- Start Shipping and Payment methods section -->
		<table class="table table-striped table-bordered">
		<thead>
		    <tr>
		        <th colspan="4" style="text-align: left;"><?php echo JText::_('COM_CITRUSCART_SHIPPING_METHOD'); ?></th>
		    </tr>
		</thead>
		<tbody>
		    <tr>
		        <th style="width: 100px;" class="key">
		            <?php echo JText::_('COM_CITRUSCART_SELECT'); ?>:
		        </th>
		        <td>
			        <?php $attribs = array( 'class' => 'inputbox', 'size' => '1', 'onchange' => 'CitruscartGetOrderTotals();' ); ?>
		            <?php echo CitruscartSelect::shippingmethod( 0, 'shipping_method_id', $attribs, 'shipping_method_id', true ); ?>
		        </td>
		    </tr>
        </tbody>
        </table>
        <!-- End Shipping and Payment methods section -->


	    <!-- Start Order totals section -->
	    <div id="order_totals_div">
	        <?php include("ordertotals.php"); ?>
	    </div>
	    <!-- End Order totals section -->

        <!-- Start Order History section -->
        <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th style="text-align: left;"><?php echo JText::_('COM_CITRUSCART_ORDER_COMMENT'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <textarea id="order_history_comments" name="order_history_comments" style="width: 100%;" rows="5"></textarea>
                </td>
            </tr>
        </tbody>
        </table>
        <!-- End Order History section -->

	</td>
</tr>
</table>

    <?php // TODO Could this go up top? Or must it be at the bottom of the form? ?>
	<script language="javascript">
		CitruscartSelectDefaultAddresses();
	</script>

	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="user_id" id="user_id" value="<?php echo $row->user_id; ?>" />

</form>
