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
<table class="table table-bordered">
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_REQUIRES_SHIPPING'); ?>:</td>
        <td class="dsc-value"><?php // Make the shipping options div only display if yes ?>
            <div class="control-group">
                <div class="controls">
                    <fieldset id="shipoptions" class="radio btn-group">
                        <input class="input" type="radio" <?php if (empty($row->product_ships)) { echo "checked='checked'"; } ?> value="0" name="product_ships" id="product_ships0" />
                        <label onclick="CitruscartShowHideDiv('shipping_options');" for="product_ships0"><?php echo JText::_('COM_CITRUSCART_NO'); ?></label>
                        <input class="input" type="radio" <?php if (!empty($row->product_ships)) { echo "checked='checked'"; } ?> value="1" name="product_ships" id="product_ships1" />
                        <label for="product_ships1"><?php echo JText::_('COM_CITRUSCART_YES'); ?> </label>
                    </fieldset>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_WEIGHT'); ?>:
        </td>
        <td><input type="text" name="product_weight" id="product_weight" value="<?php echo $row->product_weight; ?>" size="30" maxlength="250" />
        </td>
    </tr>
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_LENGTH'); ?>:
        </td>
        <td><input type="text" name="product_length" id="product_length" value="<?php echo $row->product_length; ?>" size="30" maxlength="250" />
        </td>
    </tr>

    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_WIDTH'); ?>:
        </td>
        <td><input type="text" name="product_width" id="product_width" value="<?php echo $row->product_width; ?>" size="30" maxlength="250" />
        </td>
    </tr>
    <tr>
        <td class="dsc-key"><?php echo JText::_('COM_CITRUSCART_HEIGHT'); ?>:
        </td>
        <td><input type="text" name="product_height" id="product_height" value="<?php echo @$row->product_height; ?>" size="30" maxlength="250" />
        </td>
    </tr>
</table>
