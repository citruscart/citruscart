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
<?php
$form = $this->form;
$row = $this->row;
$helper_product = new CitruscartHelperProduct();
?>

<table class="table table-striped table-bordered" style="width: 100%;">
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_MANUFACTURER'); ?>:</td>
        <td class="dsc-value"><?php echo CitruscartSelect::manufacturer( $row->manufacturer_id, 'manufacturer_id', '', 'manufacturer_id', false, true ); ?>
        </td>
    </tr>
    <?php
    if (empty($row->product_id))
    {
        // doing a new product, so display a notice
        ?>
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PRODUCT_ATTRIBUTES'); ?>:</td>
        <td class="dsc-value">
            <div class="note well">
                <?php echo JText::_('COM_CITRUSCART_CLICK_APPLY_TO_BE_ABLE_TO_CREATE_PRODUCT_ATTRIBUTES'); ?>
            </div>
        </td>
    </tr>
    <?php
    }
    else
    {
        // display lightbox link to manage attributes
        ?>
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PRODUCT_ATTRIBUTES'); ?>:</td>
        <td class="dsc-value">[<?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&view=products&task=setattributes&id=".$row->product_id."&tmpl=component", JText::_('COM_CITRUSCART_SET_ATTRIBUTES'), array('onclose' => '\function(){CitruscartNewModal(\''.JText::_('COM_CITRUSCART_SAVING_THE_PRODUCT').'\'); submitbutton(\'apply\');}') ); ?>] <?php $attributes = $helper_product->getAttributes( $row->product_id ); ?>
            <div id="current_attributes">
                <?php foreach ($attributes as $attribute) : ?>
                [<a href="<?php echo "index.php?option=com_citruscart&view=productattributes&task=delete&cid[]=".$attribute->productattribute_id."&return=".base64_encode("index.php?option=com_citruscart&view=products&task=edit&id=".$row->product_id); ?>"> <?php echo JText::_('COM_CITRUSCART_REMOVE'); ?>
                </a>] [
                <?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&view=products&task=setattributeoptions&id=".$attribute->productattribute_id."&tmpl=component", JText::_('Set Attribute Options'), array('onclose' => '\function(){CitruscartNewModal(\''.JText::_('COM_CITRUSCART_SAVING_THE_PRODUCT').'\'); submitbutton(\'apply\');}') ); ?>
                ]
                <?php echo $attribute->productattribute_name; ?>
                <?php echo "(".$attribute->option_names_csv.")"; ?>
                <br />
                <?php endforeach; ?>
            </div>
        </td>
    </tr>
    <?php
    }
    ?>

    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PUBLISH_UP'); ?>:</td>
        <td><?php echo JHTML::calendar( $row->publish_date, "publish_date", "publish_date", '%Y-%m-%d %H:%M:%S', array('size'=>25) ); ?>
        </td>
    </tr>
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PUBLISH_DOWN'); ?>:</td>
        <td><?php echo JHTML::calendar( $row->unpublish_date, "unpublish_date", "unpublish_date", '%Y-%m-%d %H:%M:%S', array('size'=>25) ); ?>
        </td>
    </tr>
    <?php

    if (empty($row->product_id))
    {
        // doing a new product, so collect default info
        ?>
    <tr>
        <td class="dsc-key">
        <label for="category_id"> <?php echo JText::_('COM_CITRUSCART_PRODUCT_CATEGORY'); ?>:
        </label>
        </td>
        <td><?php  echo CitruscartSelect::category( '', 'category_id', '', 'category_id' ); ?>
            <div class="note well">
                <?php  echo JText::_('COM_CITRUSCART_SET_INITIAL_CATEGORY_NOW_ADDITIONAL_ONES_LATER'); ?>
            </div>
        </td>
    </tr>
    <?php
    }
    else
    {
        // display lightbox link to manage categories
        ?>
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_CATEGORIES'); ?>:</td>
        <td><?php Citruscart::load( 'CitruscartHelperCategory', 'helpers.category' ); ?> <?php Citruscart::load( 'CitruscartUrl', 'library.url' );
        $options = array('update' => true );
        ?> [<?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&view=products&task=selectcategories&id=".$row->product_id."&tmpl=component", JText::_('COM_CITRUSCART_SELECT_CATEGORIES'), $options); ?>] <?php $categories = $helper_product->getCategories( $row->product_id ); ?>
            <div id="current_categories">
                <?php

                if($categories):
                foreach ($categories as $category) : ?>
                [<a href="<?php echo "index.php?option=com_citruscart&view=products&task=selected_disable&id=".$row->product_id."&cid[]=".$category."&return=".base64_encode("index.php?option=com_citruscart&view=products&task=edit&id=".$row->product_id); ?>"> <?php echo JText::_('COM_CITRUSCART_REMOVE'); ?>
                </a>]
                <?php echo CitruscartHelperCategory::getPathName( $category ); ?>
                <br />
                <?php endforeach;
					endif;
                ?>
            </div>
        </td>
    </tr>
    <?php
    }
    ?>
</table>


<?php
// fire plugin event here to enable extending the form
JDispatcher::getInstance()->trigger('onAfterDisplayProductFormMainColumn', array( $row ) );
?>