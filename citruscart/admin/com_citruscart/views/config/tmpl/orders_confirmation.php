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
defined('_JEXEC') or die('Restricted access'); ?>
<?php $row = $this -> row; ?>

<table class="dsc-table table table-striped table-bordered">
    <tbody>
        <tr>
            <th class="dsc-key">
                <?php echo JText::_('COM_CITRUSCART_CUSTOM_HEADER_CODE'); ?>
            </th>
            <td>
                <div class="dsc-tip">
                    <p>Enter any code you want to inserted into the header of the order confirmation page.  This is useful for analytics and tracking pixels.</p>

                    <p>You have several objects available to you, both standard and special.  The {confirmation} object is a special object specifically used for inserting data about the current order into the header in special formats.  Its properties are as follows:</p>
                    <ul>
                        <li>
                        {confirmation.orderitems} = All of the items in the order in a single string.  Each item is separated by a double-pipe (||), and the properties of each orderitem are separated by a single-pipe (|).  The properties are (in this order): <br/>
                        Product SKU | Product Name | Product Category | Orderitem Price | Orderitem Quantity
                        </li>
                        <li>
                        {confirmation.subtotal} = Order Subtotal
                        </li>
                        <li>
                        {confirmation.total} = Order Grand Total
                        </li>
                        <li>
                        {confirmation.tax} = Order Tax Total
                        </li>
                        <li>
                        {confirmation.shipping} = Order Shipping Total
                        </li>
                        <li>
                        {confirmation.ordernumber} = Order Number
                        </li>
                    </ul>

                    <p>You also have several standard objects available to you, including:</p>
                    <ul>
                        <li>{order} = CitruscartTableOrders()</li>
                        <li>{user} = JFactory::getUser(order.user_id)</li>
                        <li>{date} = JFactory::getDate()</li>
                        <li>{request} = JRequest::getVar()</li>
                    </ul>

                    <p>
                    Typical code would look like:<br />
                    <?php $string = "<iframe src='http://pixel.fetchback.com/serve/fb/pdj?name=success&oid={confirmation.ordernumber}&crv={confirmation.subtotal}&purchase_products={confirmation.orderitems}' scrolling='no' width='1' height='1' marginheight='0' marginwidth='0' frameborder='0'></iframe>"; ?>
                    <pre><?php echo htmlspecialchars( $string ); ?></pre>
                    or<br/>
                    <?php $string = '<iframe src="https://t.pepperjamnetwork.com/track?TYPE=1&OID={confirmation.ordernumber}&AMOUNT={confirmation.subtotal}" height="1" width="1" frameborder="0"></iframe>'; ?>
                    <pre><?php echo htmlspecialchars( $string ); ?></pre>
                    </p>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <textarea id="orders_confirmation_header_code" name="orders_confirmation_header_code" class="input-xxlarge" style="width: 98%; min-height: 500px;"><?php echo $this->row->get('orders_confirmation_header_code'); ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
