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
defined('_JEXEC') or die('Restricted access');
	$doc = JFactory::getDocument();

?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $form = $this->form; ?>
<?php $row = $this->row; ?>


<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >

    <h3><?php echo JText::_('COM_CITRUSCART_EDIT_ADDRESSES_FOR_ORDER') . " " . $row->order_id; ?></h3>

    <fieldset style="width: 48%; float: left;">
        <legend><?php echo JText::_('COM_CITRUSCART_BILLING_ADDRESS'); ?></legend>
        <table class="table table-striped table-bordered">
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_FIRST_NAME'); ?>:
                </td>
                <td>
                    <input type="text" name="billing_first_name" value="<?php echo $row->billing_first_name; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_LAST_NAME'); ?>:
                </td>
                <td>
                    <input type="text" name="billing_last_name" value="<?php echo $row->billing_last_name; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_COMPANY'); ?>:
                </td>
                <td>
                    <input type="text" name="billing_company" value="<?php echo $row->billing_company; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_ADDRESS_LINE_1'); ?>:
                </td>
                <td>
                    <input type="text" name="billing_address_1" value="<?php echo $row->billing_address_1; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_ADDRESS_LINE_2'); ?>:
                </td>
                <td>
                    <input type="text" name="billing_address_2" value="<?php echo $row->billing_address_2; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_CITY'); ?>:
                </td>
                <td>
                    <input type="text" name="billing_city" value="<?php echo $row->billing_city; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_POSTAL_CODE'); ?>:
                </td>
                <td>
                    <input type="text" name="billing_postal_code" value="<?php echo $row->billing_postal_code; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_COUNTRY'); ?>:
                </td>
                <td>
                    <?php
                    $url = "index.php?option=com_citruscart&format=raw&controller=addresses&task=getzones&name=shop_zone&country_id=";
                    $attribs = array('onchange' => 'citruscartDoTask( \''.$url.'\'+document.getElementById(\'billing_country_id\').value, \'billing_zones_wrapper\', \'\');' );
                    echo CitruscartSelect::country( $row->orderinfo->billing_country_id, 'billing_country_id', $attribs, 'billing_country_id', true );
                    ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_ZONE'); ?>:
                </td>
                <td>
                    <div id="billing_zones_wrapper">
                        <?php
                        if (empty($row->orderinfo->billing_zone_id))
                        {
                            echo JText::_('COM_CITRUSCART_SELECT_COUNTRY_FIRST');
                        }
                        else
                        {
                            echo CitruscartSelect::zone( $row->orderinfo->billing_zone_id, 'billing_zone_id', $row->orderinfo->billing_country_id );
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>

    <fieldset style="width: 48%; float: left;">
        <legend><?php echo JText::_('COM_CITRUSCART_SHIPPING_ADDRESS'); ?></legend>
        <table class="table table-striped table-bordered">
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_FIRST_NAME'); ?>:
                </td>
                <td>
                    <input type="text" name="shipping_first_name" value="<?php echo $row->shipping_first_name; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_LAST_NAME'); ?>:
                </td>
                <td>
                    <input type="text" name="shipping_last_name" value="<?php echo $row->shipping_last_name; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_COMPANY'); ?>:
                </td>
                <td>
                    <input type="text" name="shipping_company" value="<?php echo $row->shipping_company; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_ADDRESS_LINE_1'); ?>:
                </td>
                <td>
                    <input type="text" name="shipping_address_1" value="<?php echo $row->shipping_address_1; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_ADDRESS_LINE_2'); ?>:
                </td>
                <td>
                    <input type="text" name="shipping_address_2" value="<?php echo $row->shipping_address_2; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_CITY'); ?>:
                </td>
                <td>
                    <input type="text" name="shipping_city" value="<?php echo $row->shipping_city; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_POSTAL_CODE'); ?>:
                </td>
                <td>
                    <input type="text" name="shipping_postal_code" value="<?php echo $row->shipping_postal_code; ?>" size="48" maxlength="250" />
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_COUNTRY'); ?>:
                </td>
                <td>
                    <?php
                    $url = "index.php?option=com_citruscart&format=raw&controller=addresses&task=getzones&name=shop_zone&country_id=";
                    $attribs = array('onchange' => 'citruscartDoTask( \''.$url.'\'+document.getElementById(\'shipping_country_id\').value, \'shipping_zones_wrapper\', \'\');' );
                    echo CitruscartSelect::country( $row->orderinfo->shipping_country_id, 'shipping_country_id', $attribs, 'shipping_country_id', true );
                    ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100px; text-align: right;" class="key">
                    <?php echo JText::_('COM_CITRUSCART_ZONE'); ?>:
                </td>
                <td>
                    <div id="shipping_zones_wrapper">
                        <?php
                        if (empty($row->orderinfo->shipping_zone_id))
                        {
                            echo JText::_('COM_CITRUSCART_SELECT_COUNTRY_FIRST');
                        }
                        else
                        {
                            echo CitruscartSelect::zone( $row->orderinfo->shipping_zone_id, 'shipping_zone_id', $row->orderinfo->shipping_country_id );
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>

    <input type="hidden" name="id" value="<?php echo $row->order_id; ?>" />
    <input type="hidden" name="task" id="task" value="saveAddresses" />
</form>
