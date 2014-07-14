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
$form =$this->form;
$row = $this->row;
$helper_product = new CitruscartHelperProduct();
?>


<div style="float: left; width: 50%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_CORE_JOOMLA_USER_INTEGRATION'); ?>
        </legend>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_CHANGE_JOOMLA_ACL').'::'.JText::_('COM_CITRUSCART_CHANGE_JOOMLA_ACL_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_CHANGE_JOOMLA_ACL'); ?>:</td>
                <td><?php  echo CitruscartSelect::btbooleanlist( 'core_user_change_gid', 'class="inputbox"', $row->product_parameters->get('core_user_change_gid') ); ?>
                </td>
            </tr>
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_NEW_JOOMLA_ACL').'::'.JText::_('COM_CITRUSCART_NEW_JOOMLA_ACL_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_NEW_JOOMLA_ACL'); ?>:</td>
                <td><?php
                Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
                $helper = new CitruscartHelperUser();
                echo $helper->getACLSelectList( $row->product_parameters->get('core_user_new_gid') );
                ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
// fire plugin event here to enable extending the form
JDispatcher::getInstance()->trigger('onDisplayProductFormIntegrations', array( $row ) );
?>

<div style="clear: both;"></div>
