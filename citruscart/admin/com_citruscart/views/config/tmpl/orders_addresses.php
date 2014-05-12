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

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_ADDRESS_NAME_FIELD'); ?><br /> <small><?php echo JText::_('COM_CITRUSCART_CONFIG_SHOW_ADDRESS_TITLE_NOTE'); ?> </small>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_address_name', '3'), 'show_field_address_name'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_ADDRESS_NAME_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_address_name', '3'), 'validate_field_address_name'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_TITLE_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_title', '3'), 'show_field_title'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_TITLE_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_title', '3'), 'validate_field_title'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_FIRST_NAME_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_name', '3'), 'show_field_name'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_FIRST_NAME_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_name', '3'), 'validate_field_name'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_MIDDLE_NAME_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_middle', '3'), 'show_field_middle'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_MIDDLE_NAME_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_middle', '0'), 'validate_field_middle'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_LAST_NAME_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_last', '3'), 'show_field_last'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_LAST_NAME_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_last', '3'), 'validate_field_last'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_COMPANY_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_company', '3'), 'show_field_company'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_COMPANY_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_company', '0'), 'validate_field_company'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_COMPANY_TAX_NUMBER_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_tax_number', '3'), 'show_field_tax_number'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_COMPANY_TAX_NUMBER_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_tax_number', '3'), 'validate_field_tax_number'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_ADDRESS_1_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_address1', '3'), 'show_field_address1'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_ADDRESS_1_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_address1', '3'), 'validate_field_address1'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_ADDRESS_2_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_address2', '3'), 'show_field_address2'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_ADDRESS_2_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_address2', '0'), 'validate_field_address2'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_CITY_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_city', '3'), 'show_field_city'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_CITY_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_city', '3'), 'validate_field_city'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_COUNTRY_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_country', '3'), 'show_field_country'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_COUNTRY_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_country', '3'), 'validate_field_country'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_ZONE_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_zone', '3'), 'show_field_zone'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_ZONE_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_zone', '3'), 'validate_field_zone'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_POSTAL_CODE_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_zip', '3'), 'show_field_zip'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_POSTAL_CODE_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_zip', '3'), 'validate_field_zip'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_PHONE_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_phone', '3'), 'show_field_phone'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_PHONE_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_phone', '0'), 'validate_field_phone'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_CELL_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_cell', '3'), 'show_field_cell'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_CELL_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_cell', '0'), 'validate_field_cell'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_FAX_FIELD'); ?>
            </th>
            <td style="width: 150px;"><?php echo CitruscartSelect::addressShowList($this -> row -> get('show_field_fax', '3'), 'show_field_fax'); ?>
            </td>
            <th><?php echo JText::_('COM_CITRUSCART_VALIDATE_FAX_FIELD'); ?>
            </th>
            <td><?php echo CitruscartSelect::addressShowList($this -> row -> get('validate_field_fax', '0'), 'validate_field_fax'); ?>
            </td>
        </tr>
    </tbody>
</table>
