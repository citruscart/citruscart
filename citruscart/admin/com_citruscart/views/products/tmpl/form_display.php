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

?>
<?php
$form = $this->form;
$row = $this->row;
$helper_product = new CitruscartHelperProduct();
?>

<div style="float: left; width: 50%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_TEMPLATE'); ?>
        </legend>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td style="vertical-align: top; width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PRODUCT_LAYOUT_FILE'); ?>:</td>
                <td><?php echo CitruscartSelect::productlayout( $row->product_layout, 'product_layout' ); ?>
                    <div class="note well">
                        <?php echo JText::_('COM_CITRUSCART_PRODUCT_LAYOUT_FILE_DESC'); ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_EXTRA'); ?>
        </legend>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <th class="dsc-key">
                    <?php echo JText::_('COM_CITRUSCART_PROUCT_CLASS_SUFFIX'); ?>:
                </th>
                <td class="dsc-value">
                    <input name="product_class_suffix" id="product_class_suffix" value="<?php echo $row->product_class_suffix; ?>" type="text" class="input-xlarge" />
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_FEATURE_COMPARISON'); ?>:</td>
                <td><?php  echo CitruscartSelect::btbooleanlist( 'param_show_product_compare', 'class="inputbox"', $row->product_parameters->get('show_product_compare', '1') ); ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<div style="float: right; width: 50%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_POST_PURCHASE_ARTICLE'); ?>
        </legend>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td style="vertical-align: top; width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_SELECT_AN_ARTICLE_TO_DISPLAY_AFTER_PURCHASE'); ?>:</td>
                <td><?php echo $this->elementArticleModel->_fetchElement( 'product_article', $row->product_article ); ?> <?php echo $this->elementArticleModel->_clearElement( 'product_article', 0 ); ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
// fire plugin event here to enable extending the form
JDispatcher::getInstance()->trigger('onDisplayProductFormDisplay', array( $row ) );
?>

<div style="clear: both;"></div>
