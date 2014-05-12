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
<?php JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/'); ?>
<?php $form = $this -> form; ?>
<?php $row = $this -> row; ?>
<!-- Get the application -->
<?php $app = JFactory::getApplication();?>

<?php JFilterOutput::objectHTMLSafe($row); ?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data">

    <?php echo CitruscartGrid::pagetooltip($app->input->getString('view'));

    //CitruscartGrid::pagetooltip(JRequest::getVar('view'));
    ?>

    <div id='onBeforeDisplay_wrapper'>
        <?php

        $dispatcher -> trigger('onBeforeDisplayConfigForm', array());
        ?>
    </div>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="vertical-align: top; min-width: 70%;">


                    <div class="accordion" id="accordion2">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#shop"> <?php echo JText::_('COM_CITRUSCART_SHOP_INFORMATION'); ?>
                                </a>
                            </div>
                            <div id="shop" class="accordion-body collapse" style="height: 0px;">
                                <div class="accordion-inner">
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
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#images"> <?php echo JText::_('COM_CITRUSCART_IMAGES_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="images" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_DEFAULT_CATEGORY_IMAGE'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('use_default_category_image', '' , $this -> row -> get('use_default_category_image', '1')) ; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_PRODUCT_IMAGE_HEIGHT'); ?>
                                                </th>
                                                <td><input type="text" name="product_img_height" value="<?php echo $this -> row -> get('product_img_height', ''); ?>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_PRODUCT_IMAGE_WIDTH'); ?>
                                                </th>
                                                <td><input type="text" name="product_img_width" value="<?php echo $this -> row -> get('product_img_width', ''); ?>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_RECREATE_PRODUCT_THUMBNAILS'); ?>
                                                </th>
                                                <td><a href="index.php?option=com_citruscart&view=products&task=recreateThumbs" onClick="return confirm('<?php echo JText::_('Are you sure? Remember to save your new Configuration Values before doing this!'); ?>');"><?php echo JText::_('COM_CITRUSCART_CLICK_HERE_TO_RECREATE_THE_PRODUCT_THUMBNAILS'); ?> </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_CATEGORY_IMAGE_HEIGHT'); ?>
                                                </th>
                                                <td><input type="text" name="category_img_height" value="<?php echo $this -> row -> get('category_img_height', ''); ?>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_CATEGORY_IMAGE_WIDTH'); ?>
                                                </th>
                                                <td><input type="text" name="category_img_width" value="<?php echo $this -> row -> get('category_img_width', ''); ?>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_RECREATE_CATEGORY_THUMBNAILS'); ?>
                                                </th>
                                                <td><a href="index.php?option=com_citruscart&view=categories&task=recreateThumbs" onClick="return confirm('<?php echo JText::_('Are you sure? Remember to save your new Configuration Values before doing this!'); ?>');"><?php echo JText::_('COM_CITRUSCART_CLICK_HERE_TO_RECREATE_THE_CATEGORY_THUMBNAILS'); ?> </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_MANUFACTURER_IMAGE_HEIGHT'); ?>
                                                </th>
                                                <td><input type="text" name="manufacturer_img_height" value="<?php echo $this -> row -> get('manufacturer_img_height', ''); ?>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_MANUFACTURER_IMAGE_WIDTH'); ?>
                                                </th>
                                                <td><input type="text" name="manufacturer_img_width" value="<?php echo $this -> row -> get('manufacturer_img_width', ''); ?>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_RECREATE_MANUFACTURER_THUMBNAILS'); ?>
                                                </th>
                                                <td><a href="index.php?option=com_citruscart&view=manufacturers&task=recreateThumbs" onClick="return confirm('<?php echo JText::_('COM_CITRUSCART_ARE_YOU_SURE_REMEMBER_TO_SAVE_YOUR_NEW_CONFIGURATION_VALUES'); ?>');"><?php echo JText::_('COM_CITRUSCART_CLICK_HERE_TO_RECREATE_THE_MANUFACTURER_THUMBNAILS'); ?> </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#currency"> <?php echo JText::_('COM_CITRUSCART_CURRENCY_UNITS_AND_DATE_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="currency" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SET_DATE_FORMAT_ACT'); ?>
                                                </th>
                                                <td><input name="date_format_act" value="<?php echo $this -> row -> get('date_format_act', 'D, d M Y, h:iA'); ?>" type="text" size="40" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_CONFIG_SET_DATE_FORMAT_ACT'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SET_DATE_FORMAT'); ?>
                                                </th>
                                                <td><input name="date_format" value="<?php echo $this -> row -> get('date_format', '%a, %d %b %Y, %I:%M%p'); ?>" type="text" size="40" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_CONFIG_SET_DATE_FORMAT'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SELECT_DEFAULT_CURRENCY_FOR_DB_VALUES'); ?>
                                                </th>
                                                <td><?php echo CitruscartSelect::currency($this -> row -> get('default_currencyid', '1'), 'default_currencyid'); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_CONFIG_DEFAULT_CURRENCY'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_AUTO_UPDATE_EXCHANGE_RATES'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('currency_exchange_autoupdate', 'class="inputbox"', $this -> row -> get('currency_exchange_autoupdate', '1')); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_AUTO_UPDATE_EXCHANGE_RATES_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DIMENSIONS_MEASURE_UNIT'); ?>
                                                </th>
                                                <td><input type="text" name="dimensions_unit" value="<?php echo $this -> row -> get('dimensions_unit', ''); ?>" />
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_WEIGHT_MEASURE_UNIT'); ?>
                                                </th>
                                                <td><input type="text" name="weight_unit" value="<?php echo $this -> row -> get('weight_unit', ''); ?>" />
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#orders"> <?php echo JText::_('COM_CITRUSCART_ORDER_AND_CHECKOUT_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="orders" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ONE_PAGE_CHECKOUT'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('one_page_checkout', '' ,  $this -> row -> get('one_page_checkout', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ONE_PAGE_CHECKOUT_LAYOUT'); ?>
                                                </th>
                                                <td><?php
                                                echo CitruscartSelect::opclayouts($this -> row -> get('one_page_checkout_layout', 'onepagecheckout'), 'one_page_checkout_layout');
                                                ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_TOOLTIPS_ONE_PAGE_CHECKOUT'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('one_page_checkout_tooltips_enabled', '' , $this -> row -> get('one_page_checkout_tooltips_enabled', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_FORCE_SSL_ON_CHECKOUT'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('force_ssl_checkout', '' , $this -> row -> get('force_ssl_checkout', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_REQUIRE_ACCEPTANCE_OF_TERMS_ON_CHECKOUT'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('require_terms', 'class="inputbox"', $this -> row -> get('require_terms', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TERMS_AND_CONDITIONS_ARTICLE'); ?>
                                                </th>
                                                <td style="width: 280px;"><?php echo $this -> elementArticle_terms; ?> <?php echo $this -> resetArticle_terms; ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_ARTICLE_FOR_TERMS_AND_CONDITIONS_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_NO_ZONES_COUNTRIES'); ?>
                                                </th>
                                                <td style="width: 280px;"><input type="text" name="ignored_countries" value="<?php echo $this -> row -> get('ignored_countries', ''); ?>" class="inputbox" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_COUNTRIES_THAT_WILL_BE_IGNORED_WHEN_VALIDATING_THE_ZONES_DURING_CHECKOUT_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_TAXES'); ?>
                                                </th>
                                                <td><?php echo CitruscartSelect::taxdisplaycheckout($this -> row -> get('show_tax_checkout', '3'), 'show_tax_checkout'); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_SHIPPING_TAX_ON_ORDER_INVOICES_AND_CHECKOUT'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('display_shipping_tax', 'class="inputbox"', $this -> row -> get('display_shipping_tax', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_INITIAL_ORDER_STATE'); ?>
                                                </th>
                                                <td><?php echo CitruscartSelect::orderstate($this -> row -> get('initial_order_state', '15'), 'initial_order_state'); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_INITIAL_ORDER_STATE_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_PENDING_ORDER_STATE'); ?>
                                                </th>
                                                <td><?php echo CitruscartSelect::orderstate($this -> row -> get('pending_order_state', '1'), 'pending_order_state'); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_PENDING_ORDER_STATE_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_SHIPPING_METHOD'); ?>
                                                </th>
                                                <td><?php echo CitruscartSelect::shippingtype($this -> row -> get('defaultShippingMethod', '2'), 'defaultShippingMethod'); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_GUEST_CHECKOUT'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('guest_checkout_enabled', 'class="inputbox"', $this -> row -> get('guest_checkout_enabled', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ORDER_NUMBER_PREFIX'); ?>
                                                </th>
                                                <td><input type="text" name="order_number_prefix" value="<?php echo $this -> row -> get('order_number_prefix', ''); ?>" class="inputbox" size="10" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_ORDER_NUMBER_PREFIX_DESC'); ?>
                                                </td>
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
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ARTICLE_TO_DISPLAY_AFTER_SUCCESSFUL_CHECKOUT'); ?>
                                                </th>
                                                <td style="width: 280px;"><?php echo $this -> elementArticleModel -> _fetchElement('article_checkout', $this -> row -> get('article_checkout')); ?> <?php echo $this -> elementArticleModel -> _clearElement('article_checkout', '0'); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_ARTICLE_TO_DISPLAY_AFTER_SUCCESSFUL_CHECKOUT_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ARTICLE_TO_DISPLAY_AFTER_UNSUCCESSFUL_CHECKOUT'); ?>
                                                </th>
                                                <td style="width: 280px;"><?php echo $this -> elementArticleModel -> _fetchElement('article_default_payment_failure', $this -> row -> get('article_default_payment_failure')); ?> <?php echo $this -> elementArticleModel -> _clearElement('article_default_payment_failure', '0'); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_ARTICLE_TO_DISPLAY_AFTER_UNSUCCESSFUL_CHECKOUT_DESC'); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#display"> <?php echo JText::_('COM_CITRUSCART_DISPLAY_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="display" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
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
                                                <td><?php  echo CitruscartSelect::btbooleanlist('display_citruscart_pathway', 'class="inputbox"', $this -> row -> get('display_Citruscart_pathway', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_PRODUCT_SORT_BY'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('display_sort_by', 'class="inputbox"', $this -> row -> get('display_sort_by', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_PRODUCT_SORTINGS'); ?>
                                                </th>
                                                <td><input type="text" name="display_sortings" value="<?php echo $this -> row -> get('display_sortings', 'Name|product_name,Price|price,Rating|product_rating'); ?>" class="inputbox" size="45" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_PRODUCT_SORTINGS_DESC')?>
                                                </td>
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
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_FACEBOOK_LIKE_BUTTON'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('display_facebook_like', 'class="inputbox"', $this -> row -> get('display_facebook_like', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_TWITTER_BUTTON'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('display_tweet', 'class="inputbox"', $this -> row -> get('display_tweet', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_TWITTER_MESSAGE'); ?>
                                                </th>
                                                <td><input type="text" name="display_tweet_message" value="<?php echo $this -> row -> get('display_tweet_message', 'Check this out!'); ?>" class="inputbox" size="35" />
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_GOOGLE_PLUS1_BUTTON'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('display_google_plus1', 'class="inputbox"', $this -> row -> get('display_google_plus1', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_GOOGLE_PLUS1_BUTTON_SIZE'); ?>
                                                </th>
                                                <td><?php
                                                $google_sizes = array();
                                                $google_sizes[] = JHTML::_('select.option', 'small', JText::_('COM_CITRUSCART_GOOGLE_SMALL'));
                                                $google_sizes[] = JHTML::_('select.option', 'medium', JText::_('COM_CITRUSCART_GOOGLE_MEDIUM'));
                                                $google_sizes[] = JHTML::_('select.option', '', JText::_('COM_CITRUSCART_GOOGLE_STANDARD'));
                                                $google_sizes[] = JHTML::_('select.option', 'tall', JText::_('COM_CITRUSCART_GOOGLE_TALL'));
                                                echo JHTML::_('select.genericlist', $google_sizes, 'display_google_plus1_size', array('class' => 'inputbox', 'size' => '1'), 'value', 'text', $this -> row -> get('display_google_plus1_size', 'medium'));
                                                ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_USE_URI_FOR_SOCIAL_BOOKMARK_INTEGRATION'); ?>
                                                </th>
                                                <td><?php
                                                $social_uri_types = array();
                                                $social_uri_types[] = JHTML::_('select.option', 0, JText::_('COM_CITRUSCART_LONG_URI'));
                                                $social_uri_types[] = JHTML::_('select.option', 1, JText::_('COM_CITRUSCART_BITLY'));
                                                echo JHTML::_('select.genericlist', $social_uri_types, 'display_bookmark_uri', array('class' => 'inputbox', 'size' => '1'), 'value', 'text', $this -> row -> get('display_bookmark_uri', 0));
                                                ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_BITLY_LOGIN'); ?>
                                                </th>
                                                <td><input type="text" name="bitly_login" value="<?php echo $this -> row -> get('bitly_login', ''); ?>" class="inputbox" size="35" />
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_BITLY_KEY'); ?>
                                                </th>
                                                <td><input type="text" name="bitly_key" value="<?php echo $this -> row -> get('bitly_key', ''); ?>" class="inputbox" size="35" />
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
                                                <td><?php echo JText::_('COM_CITRUSCART_SHOW_THE_ASK_A_QUESTION_ABOUT_THIS_PRODUCT_FORM_IN_MODAL'); ?>
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
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_WORKING_IMAGE_PRODUCT'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('dispay_working_image_product', 'class="inputbox"', $this -> row -> get('dispay_working_image_product', '1')); ?>
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
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_TAX_GEOZONE'); ?>
                                                </th>
                                                <td><?php echo CitruscartSelect::geozone($this -> row -> get('default_tax_geozone'), 'default_tax_geozone', 1); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_DEFAULT_TAX_GEOZONE_DESC'); ?>
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
                                                <td><?php echo CitruscartSelect::cartbutton($this -> row -> get('cartbutton', 'image'), 'cartbutton'); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_WIDTH_OF_UI_LIGHTBOXES'); ?>
                                                </th>
                                                <td><input type="text" name="lightbox_width" value="<?php echo $this -> row -> get('lightbox_width', '800'); ?>" class="inputbox" size="10" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_WIDTH_OF_UI_LIGHTBOXES_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_HEIGHT_OF_UI_LIGHTBOXES'); ?>
                                                </th>
                                                <td><input type="text" name="lightbox_height" value="<?php echo $this -> row -> get('lightbox_height', '480'); ?>" class="inputbox" size="10" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_HEIGHT_OF_UI_LIGHTBOXES_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_DIOSCOURI_LINK_IN_FOOTER'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist( 'show_linkback', 'class="inputbox"', $this -> row -> get('show_linkback', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_YOUR_DIOSCOURI_AFFILIATE_ID'); ?>
                                                </th>
                                                <td><input type="text" name="amigosid" value="<?php echo $this -> row -> get('amigosid', ''); ?>" class="inputbox" />
                                                </td>
                                                <td><a href='http://www.dioscouri.com/index.php?option=com_amigos' target='_blank'> <?php echo JText::_('COM_CITRUSCART_NO_AMIGOSID'); ?>
                                                </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_CONFIG_PROCESS_CONTENT_PLUGIN_PRODUCT_DESC'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('content_plugins_product_desc', 'class="inputbox"', $this -> row -> get('content_plugins_product_desc', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#subscriptions"> <?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="subscriptions" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_EXPIRATION_NOTICE'); ?>
                                                </th>
                                                <td><input name="subscriptions_expiring_notice_days" value="<?php echo $this -> row -> get('subscriptions_expiring_notice_days', '14'); ?>" type="text" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_EXPIRATION_NOTICE_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_SUBSCRIPTION_NUMBER'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('display_subnum', 'class="inputbox"', $this -> row -> get('display_subnum', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_SUBSCRIPTION_NUMBER_DIGITS'); ?>
                                                </th>
                                                <td><input type="text" name="sub_num_digits" value="<?php echo $this -> row -> get('sub_num_digits', ''); ?>" class="inputbox" size="10" />
                                                </td>
                                                <td></td>
                                            </tr>
                                            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_SUBSCRIPTION_NUMBER'); ?>
                                            </th>
                                            <td><input type="text" name="default_sub_num" value="<?php echo $this -> row -> get('default_sub_num', '1'); ?>" class="inputbox" size="10" />
                                            </td>
                                            <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#DASHBOARD_SETTINGS"> <?php echo JText::_('COM_CITRUSCART_ADMINISTRATOR_DASHBOARD_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="DASHBOARD_SETTINGS" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISPLAY_STATISTICS'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('display_dashboard_statistics', 'class="inputbox"', $this -> row -> get('display_dashboard_statistics', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SELECT_ORDER_STATES_TO_REPORT_ON'); ?>
                                                </th>
                                                <td><input type="text" name="orderstates_csv" value="<?php echo $this -> row -> get('orderstates_csv', '2, 3, 5, 17'); ?>" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_CONFIG_ORDER_STATES_TO_REPORT_ON'); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#coupons"> <?php echo JText::_('COM_CITRUSCART_COUPON_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="coupons" class="accordion-body collapse">
                                <div class="accordion-inner">

                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_COUPONS'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('coupons_enabled', 'class="inputbox"', $this -> row -> get('coupons_enabled', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_MULTIPLE_USER_SUBMITTED_COUPONS_PER_ORDER'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('multiple_usercoupons_enabled', 'class="inputbox"', $this -> row -> get('multiple_usercoupons_enabled', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#ADMINISTRATOR_TOOLTIPS"> <?php echo JText::_('COM_CITRUSCART_ADMINISTRATOR_TOOLTIPS'); ?>
                                </a>
                            </div>
                            <div id="ADMINISTRATOR_TOOLTIPS" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_HIDE_DASHBOARD_NOTE'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('page_tooltip_dashboard_disabled', 'class="inputbox"', $this -> row -> get('page_tooltip_dashboard_disabled', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_HIDE_CONFIGURATION_NOTE'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('page_tooltip_config_disabled', 'class="inputbox"', $this -> row -> get('page_tooltip_config_disabled', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_HIDE_TOOLS_NOTE'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('page_tooltip_tools_disabled', 'class="inputbox"', $this -> row -> get('page_tooltip_tools_disabled', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_HIDE_USER_DASHBOARD_NOTE'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('page_tooltip_users_view_disabled', 'class="inputbox"', $this -> row -> get('page_tooltip_users_view_disabled', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#PRODUCT_REVIEWS"> <?php echo JText::_('COM_CITRUSCART_PRODUCT_REVIEWS'); ?>
                                </a>
                            </div>
                            <div id="PRODUCT_REVIEWS" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_PRODUCT_REVIEWS'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('product_review_enable', 'class="inputbox"', $this -> row -> get('product_review_enable', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_AUTOMATICALLY_APPROVE_REVIEWS'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('product_reviews_autoapprove', 'class="inputbox"', $this -> row -> get('product_reviews_autoapprove', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_REQUIRE_LOGIN_TO_LEAVE_REVIEW'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('login_review_enable', 'class="inputbox"', $this -> row -> get('login_review_enable', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_REQUIRE_PURCHASE_TO_LEAVE_REVIEW'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('purchase_leave_review_enable', 'class="inputbox"', $this -> row -> get('purchase_leave_review_enable', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_USE_CAPTCHA'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('use_captcha', 'class="inputbox"', $this -> row -> get('use_captcha', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_REVIEW_HELPFULNESS_VOTING'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('review_helpfulness_enable', 'class="inputbox"', $this -> row -> get('review_helpfulness_enable', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_SHARE_THIS_LINK'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('share_review_enable', 'class="inputbox"', $this -> row -> get('share_review_enable', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#ADVANCED_SETTINGS"> <?php echo JText::_('COM_CITRUSCART_ADVANCED_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="ADVANCED_SETTINGS" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_AUTOMATIC_TABLE_REORDERING'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('enable_reorder_table', 'class="inputbox"', $this -> row -> get('enable_reorder_table', '1')); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_ENABLE_AUTOMATIC_TABLE_REORDERING_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DEFAULT_USER_GROUP'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php echo CitruscartSelect::groups($this -> row -> get('default_user_group', '1'), 'default_user_group'); ?>
                                                </td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_LOAD_CUSTOM_LANGUAGE_FILE'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('custom_language_file', 'class="inputbox"', $this -> row -> get('custom_language_file', '0')); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_CITRUSCART_CUSTOM_LANGUAGE_FILE_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_USE_SHA1_TO_STORE_THE_IMAGES'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('sha1_images', 'class="inputbox"', $this -> row -> get('sha1_images', '0')); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_CITRUSCART_SHA1_IMAGE_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_MAX_FILESIZE_FOR_IMAGES_IMAGE_ARCHIVES'); ?>
                                                </th>
                                                <td style="width: 150px;"><input type="text" name="files_maxsize" value="<?php echo $this -> row -> get('files_maxsize', '3000'); ?>" /> Kb</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_CHOOSE_MULTI_UPLOAD_SCRIPT'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php echo CitruscartSelect::multipleuploadscript($this -> row -> get('multiupload_script', '0'), 'multiupload_script'); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_CHOOSE_MULTI_UPLOAD_SCRIPT_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_CONFIG_PASSWORD_LENGTH'); ?>
                                                </th>
                                                <td style="width: 150px;"><input type="text" name="password_min_length" value="<?php echo $this -> row -> get('password_min_length', '5'); ?>" />
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_CONFIG_PASSWORD_REQUIRE_DIGIT'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('password_req_num', 'class="inputbox"', $this -> row -> get('password_req_num', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_CONFIG_PASSWORD_REQUIRE_ALPHA'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('password_req_alpha', 'class="inputbox"', $this -> row -> get('password_req_alpha', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_CONFIG_PASSWORD_REQUIRE_SPECIAL'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('password_req_spec', 'class="inputbox"', $this -> row -> get('password_req_spec', '1')); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_CONFIG_PASSWORD_REQUIRE_SPECIAL_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_CONFIG_PASSWORD_VALDATE_PHP'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('password_php_validate', 'class="inputbox"', $this -> row -> get('password_php_validate', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_CONFIG_LOWER_FILENAME'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('lower_filename', 'class="inputbox"', $this -> row -> get('lower_filename', '1')); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_CONFIG_LOWER_FILENAME_DESC'); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#email"> <?php echo JText::_('COM_CITRUSCART_EMAIL_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="email" class="accordion-body collapse" style="height: 0px;">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOP_EMAIL_ADDRESS'); ?><br />
                                                </th>
                                                <td><input type="text" name="shop_email" value="<?php echo $this -> row -> get('shop_email', ''); ?>" class="inputbox" size="35" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_SHOP_EMAIL_ADDRESS_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOP_EMAIL_FROM_NAME'); ?><br />
                                                </th>
                                                <td><input type="text" name="shop_email_from_name" value="<?php echo $this -> row -> get('shop_email_from_name', ''); ?>" class="inputbox" size="35" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_SHOP_EMAIL_FROM_NAME_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_DISABLE_GUEST_SIGNUP_EMAIL'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('disable_guest_signup_email', 'class="inputbox"', $this -> row -> get('disable_guest_signup_email', '0')); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_DISABLE_GUEST_SIGNUP_EMAIL_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_OBFUSCATE_GUEST_EMAIL'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('obfuscate_guest_email', 'class="inputbox"', $this -> row -> get('obfuscate_guest_email', '0')); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_OBFUSCATE_GUEST_EMAIL_DESC'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_ORDER_STATUS_UPDATE_EMAIL_TO_USER_WHEN_ORDER_PAYMENT_IS_RECEIVED'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('autonotify_onSetOrderPaymentReceived', 'class="inputbox"', $this -> row -> get('autonotify_onSetOrderPaymentReceived', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ADDITIONAL_EMAIL_ADDRESSES_TO_RECEIVE_ORDER_NOTIFICATIONS'); ?><br />
                                                </th>
                                                <td><textarea name="order_emails" style="width: 250px;" rows="10">
                                                        <?php echo $this -> row -> get('order_emails', ''); ?>
                                                    </textarea>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_ADDITIONAL_EMAIL_ADDRESSES_TO_RECEIVE_ORDER_NOTIFICATIONS_DESC'); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#address_fields"> <?php echo JText::_('COM_CITRUSCART_ADDRESS_FIELDS_MANAGEMENT'); ?>
                                </a>
                            </div>
                            <div id="address_fields" class="accordion-body collapse" style="height: 0px;">
                                <div class="accordion-inner">
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
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#product_compare"> <?php echo JText::_('COM_CITRUSCART_PRODUCT_COMPARE_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="product_compare" class="accordion-body collapse" style="height: 0px;">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_PRODUCT_COMPARE'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist( 'enable_product_compare', 'class="inputbox"', $this -> row -> get('enable_product_compare', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_PRODUCT_COMPARED_LIMIT'); ?>
                                                </th>
                                                <td style="width: 150px;"><input type="text" name="compared_products" value="<?php echo $this -> row -> get('compared_products', ''); ?>" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_NUMBER_OF_PRODUCTS_THAT_CAN_BE_COMPARED_AT_ONCE'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_ADD_TO_CART'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist( 'show_addtocart_productcompare', 'class="inputbox"', $this -> row -> get('show_addtocart_productcompare', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_AVERAGE_CUSTOMER_RATING'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist( 'show_rating_productcompare', 'class="inputbox"', $this -> row -> get('show_rating_productcompare', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_MANUFACTURER'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist( 'show_manufacturer_productcompare', 'class="inputbox"', $this -> row -> get('show_manufacturer_productcompare', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_PRODUCT_MODEL'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist( 'show_model_productcompare', 'class="inputbox"', $this -> row -> get('show_model_productcompare', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_SHOW_PRODUCT_SKU'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist( 'show_sku_productcompare', 'class="inputbox"', $this -> row -> get('show_sku_productcompare', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#low_stock_notify"> <?php echo JText::_('COM_CITRUSCART_LOW_STOCK_NOTIFY_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="low_stock_notify" class="accordion-body collapse" style="height: 0px;">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_LOW_STOCK_NOTIFY'); ?>
                                                </th>
                                                <td><?php  echo CitruscartSelect::btbooleanlist('low_stock_notify', 'class="inputbox"', $this -> row -> get('low_stock_notify', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_LOW_STOCK_NOTIFY_VALUE'); ?>
                                                </th>
                                                <td><input ="text" name="low_stock_notify_value" value="<?php echo $this -> row -> get('low_stock_notify_value', '0'); ?>" />
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_LOW_STOCK_NOTIFY_VALUE_DESC'); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#eav_editor_settings"> <?php echo JText::_('COM_CITRUSCART_EAV_EDITOR_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="eav_editor_settings" class="accordion-body collapse" style="height: 0px;">
                                <div class="accordion-inner">

                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TEXTAREA_ROWS'); ?>
                                                </th>
                                                <td style="width: 150px;"><input type="text" name="eav_textarea_rows" value="<?php echo $this -> row -> get('eav_textarea_rows', '20'); ?>" />
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TEXTAREA_COLUMNS'); ?>
                                                </th>
                                                <td style="width: 150px;"><input type="text" name="eav_textarea_columns" value="<?php echo $this -> row -> get('eav_textarea_columns', '50'); ?>" />
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TEXTAREA_WIDTH'); ?>
                                                </th>
                                                <td style="width: 150px;"><input type="text" name="eav_textarea_width" value="<?php echo $this -> row -> get('eav_textarea_width', '300'); ?>" />
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TEXTAREA_HEIGHT'); ?>
                                                </th>
                                                <td style="width: 150px;"><input type="text" name="eav_textarea_height" value="<?php echo $this -> row -> get('eav_textarea_height', '200'); ?>" />
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_EAV_CONTENT_PLUGIN_TEXTAREA'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('eavtext_content_plugin', 'class="inputbox"', $this -> row -> get('eavtext_content_plugin', '1')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_EAV_INTEGER_THOUSANDS_SEPARATOR'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('eavinteger_use_thousand_separator', 'class="inputbox"', $this -> row -> get('eavinteger_use_thousand_separator', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#features_settings"> <?php echo JText::_('COM_CITRUSCART_FEATURES_SETTINGS'); ?>
                                </a>
                            </div>
                            <div id="features_settings" class="accordion-body collapse" style="height: 0px;">
                                <div class="accordion-inner">
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_SUBSCRIPTIONS'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('display_subscriptions', 'class="inputbox"', $this -> row -> get('display_subscriptions', '1')); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_ENABLE_SUBSCRIPTIONS_NOTE'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_MY_DOWNLOADS'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('display_mydownloads', 'class="inputbox"', $this -> row -> get('display_mydownloads', '1')); ?>
                                                </td>
                                                <td><?php echo JText::_('COM_CITRUSCART_ENABLE_MY_DOWNLOADS_NOTE'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_WISHLIST'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('display_wishlist', 'class="inputbox"', $this -> row -> get('display_wishlist', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_CREDITS'); ?>
                                                </th>
                                                <td style="width: 150px;"><?php  echo CitruscartSelect::btbooleanlist('display_credits', 'class="inputbox"', $this -> row -> get('display_credits', '0')); ?>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    </div> <?php

						// if there are plugins, display them accordingly
		                if ($this->items_sliders)
		                {
	                		$tab=1;
							$pane=2;
							for ($i=0, $count=count($this->items_sliders); $i < $count; $i++) {
								if ($pane == 1) {
									// echo $this->sliders->startPane( "pane_$pane" );
								}
								$item = $this->items_sliders[$i];
								?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#plugin<?php echo $i; ?>"> <?php echo JText::_($item -> element); ?>
                            </a>
                        </div>
                        <div id="plugin<?php echo $i; ?>" class="accordion-body collapse" style="height: 0px;">
                            <div class="accordion-inner">
                                <?
								// load the plugin
								$import = JPluginHelper::importPlugin(strtolower('Citruscart'), $item -> element);
								// fire plugin

								$dispatcher -> trigger('onDisplayConfigFormSliders', array($item, $this -> row));
								?>


                            </div>
                        </div>
                    </div> <?php
								if ($i == $count - 1) {
									// echo $this->sliders->endPane();
								}
								}
								}
						?>





                    </div>
                    </div>



                </td>
                <td style="vertical-align: top; max-width: 30%;">

                    <div id='onDisplayRightColumn_wrapper'>
                        <?php

							$dispatcher -> trigger('onDisplayConfigFormRightColumn', array());
							?>
                    </div>

                </td>
            </tr>
        </tbody>
    </table>

    <div id='onAfterDisplay_wrapper'>
        <?php

			$dispatcher -> trigger('onAfterDisplayConfigForm', array());
			?>
    </div>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="" />
    <input type="hidden" name="filter_order" value="<?php echo $state -> order; ?>" />
    <input type="hidden" name="filter_direction" value="<?php echo $state -> direction; ?>" />

    <?php echo $this -> form['validate']; ?>
</form>
