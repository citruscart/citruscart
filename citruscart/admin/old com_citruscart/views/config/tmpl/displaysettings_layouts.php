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
defined('_JEXEC') or die('Restricted access'); ?>
<?php $row = $this -> row; ?>

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th class="dsc-key">
                <?php echo JText::_('COM_CITRUSCART_DEFAULT_CATEGORY_LAYOUT'); ?>
            </th>
            <td>
                <?php echo CitruscartSelect::categorylayout( $this->row->get('default_category_layout'), 'default_category_layout' ); ?>
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <th class="dsc-key">
                <?php echo JText::_('COM_CITRUSCART_DEFAULT_PRODUCT_DETAIL_LAYOUT'); ?>
            </th>
            <td>
                <?php echo CitruscartSelect::productlayout( $this->row->get('default_product_layout'), 'default_product_layout' ); ?>
            </td>
            <td>

            </td>
        </tr>
    </tbody>
</table>
