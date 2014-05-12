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
defined('_JEXEC') or die('Restricted access');?>
<?php
$form = $this->form;
$row = $this->row;
$helper_product = new CitruscartHelperProduct();
?>

<div class="note well">
    <?php echo JText::_('COM_CITRUSCART_ADVANCED_PANEL_NOTICE'); ?>
</div>

<div style="clear: both;"></div>

<div style="float: left; width: 50%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_PRODUCT_PARAMETERS'); ?>
        </legend>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td style="vertical-align: top; width: 100px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PRODUCT_PARAMS'); ?>:</td>
                <td><textarea name="product_params" id="product_params" rows="10" cols="55">
                        <?php echo $row->product_params; ?>
                    </textarea>
                </td>
            </tr>
        </table>
    </div>
</div>

<div style="float: left; width: 50%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_SQL_FOR_AFTER_PURCHASE'); ?>
        </legend>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_PRODUCT_SQL').'::'.JText::_('COM_CITRUSCART_PRODUCT_SQL_TIP'); ?>" style="width: 100px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_PRODUCT_SQL'); ?>:</td>
                <td><textarea name="product_sql" rows="10" cols="55">
                        <?php echo $row->product_sql; ?>
                    </textarea>
                </td>
            </tr>
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_AVAILABLE_OBJECTS').'::'.JText::_('COM_CITRUSCART_AVAILABLE_OBJECTS_TIP'); ?>" style="width: 100px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_AVAILABLE_OBJECTS'); ?>:</td>
                <td>{user} = JFactory::getUser( <?php echo "$"."order->user_id"; ?> )<br /> {date} = JFactory::getDate()<br /> {request} = JRequest::getVar()<br /> {order} = CitruscartTableOrders()<br /> {orderitem} = CitruscartTableOrderItems()<br /> {product} = CitruscartTableProducts()<br />
                </td>
            </tr>
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_NORMAL_USAGE').'::'.JText::_('COM_CITRUSCART_NORMAL_USAGE_TIP'); ?>" style="width: 100px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_NORMAL_USAGE'); ?>:</td>
                <td><br /> <?php echo "{user.name} == JFactory::getUser()->name"; ?><br /> <?php echo "{user.username} == JFactory::getUser()->username"; ?><br /> <?php echo "{user.email} == JFactory::getUser()->email"; ?><br /> <?php echo "{date.toSql()} == JFactory::getDate()->toSql()"; ?><br /> <?php echo "{request.task} == JRequest::getVar('task');"; ?><br />
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
// fire plugin event here to enable extending the form
JDispatcher::getInstance()->trigger('onDisplayProductFormAdvanced', array( $row ) );
?>

<div style="clear: both;"></div>
