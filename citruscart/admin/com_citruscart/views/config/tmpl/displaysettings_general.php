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
            <th class="dsc-key">
                <?php echo JText::_( 'COM_CITRUSCART_INCLUDE_SITE_CSS' ); ?>
            </th>
            <td class="dsc-value">
                <?php echo CitruscartSelect::btbooleanlist( 'include_site_css', 'class="inputbox"', $this->row->get('include_site_css', '1') ); ?>
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_USE_BOOTSTRAP'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('use_bootstrap', 'class="inputbox"', $this -> row -> get('use_bootstrap', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_FRONT_END_SUBMENU'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('show_submenu_fe', 'class="inputbox"', $this -> row -> get('show_submenu_fe', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_OUT_OF_STOCK_PRODUCTS'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('display_out_of_stock', 'class="inputbox"', $this -> row -> get('display_out_of_stock', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_ROOT_CATEGORY_IN_JOOMLA_BREADCRUMB'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('include_root_pathway', 'class="inputbox"', $this -> row -> get('include_root_pathway', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_CITRUSCART_BREADCRUMB'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('display_citruscart_pathway', 'class="inputbox"', $this -> row -> get('display_citruscart_pathway', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_PRODUCT_QUANTITY'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('display_product_quantity', 'class="inputbox"', $this -> row -> get('display_product_quantity', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_RELATED_ITEMS'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('display_relateditems', 'class="inputbox"', $this -> row -> get('display_relateditems', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_ASK_A_QUESTION_ABOUT_THIS_PRODUCT'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('ask_question_enable', 'class="inputbox"', $this -> row -> get('ask_question_enable', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_CAPTCHA_ON_ASK_A_QUESTION_ABOUT_THIS_PRODUCT'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('ask_question_showcaptcha', 'class="inputbox"', $this -> row -> get('ask_question_showcaptcha', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ASK_A_QUESTION_ABOUT_THIS_PRODUCT_IN_MODAL'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('ask_question_modal', 'class="inputbox"', $this -> row -> get('ask_question_modal', '1')); ?>
            </td>
            <td>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_PRODUCT_PRICES_WITH_TAX'); ?>
            </th>
            <td><?php echo CitruscartSelect::displaywithtax($this -> row -> get('display_prices_with_tax', '0'), 'display_prices_with_tax'); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_NUMBER_OF_SUBCATEGORIES_PER_LINE'); ?>
            </th>
            <td><input type="text" name="subcategories_per_line" id="subcategories_per_line" value="<?php echo $this -> row -> get('subcategories_per_line', 5); ?>" />
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_NUMBER_OF_SUBCATEGORIES_PER_LINE_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_PRODUCT_PRICES_WITH_LINK_TO_SHIPPING_COSTS_ARTICLE'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('display_prices_with_shipping', 'class="inputbox"', $this -> row -> get('display_prices_with_shipping', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHIPPING_COSTS_ARTICLE'); ?>
            </th>
            <td style="width: 280px;"><?php echo $this -> elementArticle_shipping; ?> <?php echo $this -> resetArticle_shipping; ?>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_ARTICLE_FOR_SHIPPING_COSTS_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ADD_TO_CART_ACTION'); ?>
            </th>
            <td><?php echo CitruscartSelect::addtocartaction($this -> row -> get('addtocartaction', 'lightbox'), 'addtocartaction'); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_ADD_TO_CART_BUTTON_IN_CATEGORY_LISTINGS'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('display_category_cartbuttons', 'class="inputbox"', $this -> row -> get('display_category_cartbuttons', '1')); ?>
            </td>
            <td></td>
        </tr>
        <!--  Add Display Add to Cart Button in Product -->
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_ADD_TO_CART_BUTTON_IN_PRODUCT'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('display_product_cartbuttons', 'class="inputbox"', $this -> row -> get('display_product_cartbuttons', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SELECT_CART_BUTTON_TYPE'); ?>
            </th>
            <td><?php echo CitruscartSelect::cartbutton($this -> row -> get('cartbutton', 'button'), 'cartbutton'); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th class="dsc-key">
                <?php echo JText::_( 'COM_CITRUSCART_ENABLE_NAV_ON_DETAIL_PAGES' ); ?>
            </th>
            <td class="dsc-value">
                <?php echo CitruscartSelect::btbooleanlist( 'enable_product_detail_nav', 'class="inputbox"', $this->row->get('enable_product_detail_nav', '0') ); ?>
            </td>
            <td>
                <p class="dsc-tip"><?php echo JText::_( 'COM_CITRUSCART_ENABLE_NAV_ON_DETAIL_PAGES_DESC' ); ?></p>
            </td>
        </tr>
    </tbody>
</table>
