<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access'); ?>
<?php $row = @$this -> row; ?>

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_SHIPPING_METHOD'); ?>
            </th>
            <td><?php echo CitruscartSelect::shippingtype($this -> row -> get('defaultShippingMethod', '2'), 'defaultShippingMethod'); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_GLOBAL_HANDLING_COST'); ?>
            </th>
            <td><input type="text" name="global_handling" value="<?php echo $this -> row -> get('global_handling', ''); ?>" class="inputbox" size="10" />
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_GLOBAL_HANDLING_COST_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_CALCULATE_TAX_BASED_ON_SHIPPING_ADDRESS'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('calc_tax_shipping', 'class="inputbox"', $this->row->get('calc_tax_shipping', '0')); ?>
            </td>
            <td>
                <?php echo JText::_('COM_CITRUSCART_CALCULATE_TAX_BASED_ON_SHIPPING_ADDRESS_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_TAXES'); ?>
            </th>
            <td><?php echo CitruscartSelect::taxdisplaycheckout($this -> row -> get('show_tax_checkout', '4'), 'show_tax_checkout'); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_TAX_GEOZONE'); ?>
            </th>
            <td><?php echo CitruscartSelect::geozone($this -> row -> get('default_tax_geozone'), 'default_tax_geozone', 1); ?>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_DEFAULT_TAX_GEOZONE_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_SHIPPING_TAX_ON_ORDER_INVOICES_AND_CHECKOUT'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('display_shipping_tax', 'class="inputbox"', $this -> row -> get('display_shipping_tax', '1')); ?>
            </td>
            <td></td>
        </tr>
    </tbody>
</table>
