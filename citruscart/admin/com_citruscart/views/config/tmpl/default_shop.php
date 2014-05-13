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
<?php $row = $this -> row; ?>

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_SHOPPING'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('shop_enabled', '' , $this -> row -> get('shop_enabled', '1')) ; ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOP_NAME'); ?>
            </th>
            <td><input type="text" name="shop_name" value="<?php echo $this -> row -> get('shop_name', ''); ?>" size="25" />
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_THE_NAME_OF_THE_SHOP'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_COMPANY_NAME'); ?>
            </th>
            <td><input type="text" name="shop_company_name" value="<?php echo $this -> row -> get('shop_company_name', ''); ?>" size="25" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ADDRESS_LINE_1'); ?>
            </th>
            <td><input type="text" name="shop_address_1" value="<?php echo $this -> row -> get('shop_address_1', ''); ?>" size="35" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ADDRESS_LINE_2'); ?>
            </th>
            <td><input type="text" name="shop_address_2" value="<?php echo $this -> row -> get('shop_address_2', ''); ?>" size="35" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_CITY'); ?>
            </th>
            <td><input type="text" name="shop_city" value="<?php echo $this -> row -> get('shop_city', ''); ?>" size="25" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_COUNTRY'); ?>
            </th>
            <td><?php
            // TODO Change this to use a task within the checkout controller rather than creating a new zones controller
            $url = "index.php?option=com_citruscart&format=raw&controller=addresses&task=getzones&name=shop_zone&country_id=";
            $attribs = array('onchange' => 'citruscartDoTask( \'' . $url . '\'+document.getElementById(\'shop_country\').value, \'zones_wrapper\', \'\');');
            echo CitruscartSelect::country($this -> row -> get('shop_country', ''), 'shop_country', $attribs, 'shop_country', true);
            ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_STATE_REGION'); ?>
            </th>
            <td>
                <div id="zones_wrapper">
                    <?php
                    $shop_zone = $this -> row -> get('shop_zone', '');
                    if (empty($shop_zone)) {
                        echo JText::_('COM_CITRUSCART_SELECT_COUNTRY_FIRST');
                    } else {
                        echo CitruscartSelect::zone($shop_zone, 'shop_zone', $this -> row -> get('shop_country', ''));
                    }
                    ?>
                </div>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_POSTAL_CODE'); ?>
            </th>
            <td><input type="text" name="shop_zip" value="<?php echo $this -> row -> get('shop_zip', ''); ?>" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TAX_NUMBER_1'); ?>
            </th>
            <td><input type="text" name="shop_tax_number_1" value="<?php echo $this -> row -> get('shop_tax_number_1', ''); ?>" size="25" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TAX_NUMBER_2'); ?>
            </th>
            <td><input type="text" name="shop_tax_number_2" value="<?php echo $this -> row -> get('shop_tax_number_2', ''); ?>" size="25" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_PHONE'); ?>
            </th>
            <td><input type="text" name="shop_phone" value="<?php echo $this -> row -> get('shop_phone', ''); ?>" />
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOP_OWNER_NAME'); ?>
            </th>
            <td><input type="text" name="shop_owner_name" value="<?php echo $this -> row -> get('shop_owner_name', ''); ?>" size="35" />
            </td>
            <td></td>
        </tr>

    </tbody>
</table>
