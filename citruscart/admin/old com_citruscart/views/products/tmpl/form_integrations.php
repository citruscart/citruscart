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
            <?php echo JText::_('COM_CITRUSCART_AMBRASUBSCRIPTIONS_INTEGRATION'); ?>
        </legend>
        <?php if (Citruscart::getClass('CitruscartHelperAmbrasubs', 'helpers.ambrasubs')->isInstalled()) : ?>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_ASSOCIATED_AMBRASUBS_SUBSCRIPTION_TYPE').'::'.JText::_('ASSOCIATED_AMBRASUBS_SUBSCRIPTION_TYPE_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_ASSOCIATED_AMBRASUBS_SUBSCRIPTION_TYPE'); ?>:</td>
                <td><?php echo CitruscartHelperAmbrasubs::selectTypes( $row->product_parameters->get('ambrasubs_type_id'), 'ambrasubs_type_id' ); ?>
                </td>
            </tr>
        </table>
        <?php else : ?>
        <div class="note well">
            <?php echo JText::_('COM_CITRUSCART_AMBRASUBS_INSTALLATION_NOTICE'); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div style="float: left; width: 50%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_AMIGOS_INTEGRATION'); ?>
        </legend>
        <?php if (Citruscart::getClass('CitruscartHelperAmigos', 'helpers.amigos')->isInstalled()) : ?>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td style="width: 125px; text-align: right;" class="key hasTip" title="<?php echo JText::_('COM_CITRUSCART_COMMISSION_RATE_OVERRIDE').'::'.JText::_('COM_CITRUSCART_COMMISSION_RATE_OVERRIDE_TIP'); ?>"><?php echo JText::_('COM_CITRUSCART_COMMISSION_RATE_OVERRIDE'); ?>:</td>
                <td><input name="amigos_commission_override" id="amigos_commission_override" value="<?php echo $row->product_parameters->get('amigos_commission_override'); ?>" size="10" maxlength="10" type="text" />
                </td>
            </tr>
        </table>
        <?php else : ?>
        <div class="note well">
            <?php echo JText::_('COM_CITRUSCART_AMIGOS_INSTALLATION_NOTICE'); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div style="float: left; width: 50%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_BILLETS_INTEGRATION'); ?>
        </legend>

        <?php if (Citruscart::getClass('CitruscartHelperBillets', 'helpers.billets')->isInstalled()) : ?>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_TICKET_LIMIT_INCREASE').'::'.JText::_('COM_CITRUSCART_TICKET_LIMIT_INCREASE_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_TICKET_LIMIT_INCREASE'); ?>:</td>
                <td><input name="billets_ticket_limit_increase" value="<?php echo $row->product_parameters->get('billets_ticket_limit_increase'); ?>" size="10" maxlength="10" type="text" />
                </td>
            </tr>
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_EXCLUDES_USER_FROM_TICKET_LIMITS').'::'.JText::_('COM_CITRUSCART_EXCLUDES_USER_FROM_TICKET_LIMITS_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_EXCLUDES_USER_FROM_TICKET_LIMITS'); ?>:</td>
                <td><?php  echo CitruscartSelect::btbooleanlist( 'billets_ticket_limit_exclusion', 'class="inputbox"', $row->product_parameters->get('billets_ticket_limit_exclusion') ); ?>
                </td>
            </tr>
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_HOUR_LIMIT_INCREASE').'::'.JText::_('COM_CITRUSCART_HOUR_LIMIT_INCREASE_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_HOUR_LIMIT_INCREASE'); ?>:</td>
                <td><input name="billets_hour_limit_increase" value="<?php echo $row->product_parameters->get('billets_hour_limit_increase'); ?>" size="10" maxlength="10" type="text" />
                </td>
            </tr>
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_EXCLUDES_USER_FROM_HOUR_LIMITS').'::'.JText::_('COM_CITRUSCART_EXCLUDES_USER_FROM_HOUR_LIMITS_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_EXCLUDES_USER_FROM_HOUR_LIMITS'); ?>:</td>
                <td><?php  echo CitruscartSelect::btbooleanlist( 'billets_hour_limit_exclusion', 'class="inputbox"', $row->product_parameters->get('billets_hour_limit_exclusion') ); ?>
                </td>
            </tr>
        </table>
        <?php else : ?>
        <div class="note well">
            <?php echo JText::_('COM_CITRUSCART_BILLETS_VERSION_NOTICE'); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div style="float: left; width: 50%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_JUGA_INTEGRATION'); ?>
        </legend>

        <?php if (Citruscart::getClass('CitruscartHelperJuga', 'helpers.juga')->isInstalled()) : ?>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_JUGA_GROUP_IDS').'::'.JText::_('COM_CITRUSCART_JUGA_GROUP_IDS_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_JUGA_GROUP_IDS'); ?>:</td>
                <td><textarea name="juga_group_csv_add" cols="25">
                        <?php echo @$row->product_parameters->get('juga_group_csv_add'); ?>
                    </textarea>
                </td>
            </tr>
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_JUGA_GROUP_IDS_REMOVE').'::'.JText::_('COM_CITRUSCART_JUGA_GROUP_IDS_REMOVE_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_JUGA_GROUP_IDS_REMOVE'); ?>:</td>
                <td><textarea name="juga_group_csv_remove" cols="25">
                        <?php echo @$row->product_parameters->get('juga_group_csv_remove'); ?>
                    </textarea>
                </td>
            </tr>
            <tr>
                <td style="width: 125px; text-align: right;" class="dsc-key"></td>
                <td><?php echo JText::_('COM_CITRUSCART_ACTIONS_FOR_WHEN_SUBSCRIPTION_EXPIRES'); ?>
                </td>
            </tr>
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_JUGA_GROUP_IDS_EXPIRATION').'::'.JText::_('COM_CITRUSCART_JUGA_GROUP_IDS_EXPIRATION_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_JUGA_GROUP_IDS_EXPIRATION'); ?>:</td>
                <td><textarea name="juga_group_csv_add_expiration" cols="25">
                        <?php echo @$row->product_parameters->get('juga_group_csv_add_expiration'); ?>
                    </textarea>
                </td>
            </tr>
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_JUGA_GROUP_IDS_REMOVE_EXPIRATION').'::'.JText::_('COM_CITRUSCART_JUGA_GROUP_IDS_REMOVE_EXPIRATION_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_JUGA_GROUP_IDS_REMOVE_EXPIRATION'); ?>:</td>
                <td><textarea name="juga_group_csv_remove_expiration" cols="25">
                        <?php echo @$row->product_parameters->get('juga_group_csv_remove_expiration'); ?>
                    </textarea>
                </td>
            </tr>
        </table>
        <?php else : ?>
        <div class="note well">
            <?php echo JText::_('COM_CITRUSCART_JUGA_VERSION_NOTICE'); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div style="float: left; width: 50%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_TAGS_INTEGRATION'); ?>
        </legend>
        <?php if (Citruscart::getClass('CitruscartHelperTags', 'helpers.tags')->isInstalled()) : ?>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td>
                    <div class="note well">
                        <?php echo JText::_('COM_CITRUSCART_TAGS_IS_INSTALLED'); ?>
                    </div>
                </td>
            </tr>
        </table>
        <?php else : ?>
        <div class="note well">
            <?php echo JText::_('COM_CITRUSCART_TAGS_INSTALLATION_NOTICE'); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

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
