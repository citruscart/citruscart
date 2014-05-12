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

<div style="float: left; width: 50%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_NON_RECURRING_SUBSCRIPTION'); ?>
        </legend>

        <div class="note well">
            <?php echo JText::_('COM_CITRUSCART_NON_RECURRING_SUBSCRIPTION_NOTE'); ?>
        </div>

        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PRODUCT_CREATES_SUBSCRIPTION'); ?>:</td>
                <td>
                    <div class="control-group">
                        <div class="controls">
                            <fieldset id="product_enabled" class="radio btn-group">
                                <input type="radio" <?php if (empty($row->product_subscription)) { echo "checked='checked'"; } ?> value="0" name="product_subscription" id="product_subscription0" /><label for="product_subscription0"><?php echo JText::_('COM_CITRUSCART_NO'); ?> </label> <input type="radio" <?php if (!empty($row->product_subscription)) { echo "checked='checked'"; } ?> value="1" name="product_subscription" id="product_subscription1" /><label for="product_subscription1"><?php echo JText::_('COM_CITRUSCART_YES'); ?> </label>

                            </fieldset>
                        </div>
                    </div>

                </td>
            </tr>
            <tr>
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_LIFETIME_SUBSCRIPTION'); ?>:</td>
                <td><?php  echo CitruscartSelect::btbooleanlist( 'subscription_lifetime', '', $row->subscription_lifetime ); ?>
                </td>
            </tr>
            <tr>
                <td title="<?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_PERIOD_INTERVAL').'::'.JText::_('COM_CITRUSCART_SUBSCRIPTION_PERIOD_INTERVAL_TIP'); ?>" style="width: 125px; text-align: right;" class="key hasTip"><?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_PERIOD_INTERVAL'); ?>:</td>
                <td><input name="subscription_period_interval" id="subscription_period_interval" value="<?php echo $row->subscription_period_interval; ?>" size="10" maxlength="10" type="text" />
                </td>
            </tr>
            <tr>
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_PERIOD_UNIT'); ?>:</td>
                <td><?php echo CitruscartSelect::periodUnit( $row->subscription_period_unit, 'subscription_period_unit' ); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_ISSUES_LIST'); ?>:</td>
                <td><?php
                if (empty($row->product_id))
                {
                    // doing a new product, so display a note
                    ?>
                    <div class="note well">
                        <?php echo JText::_('COM_CITRUSCART_CLICK_APPLY_TO_BE_ABLE_TO_ADD_ISSUES_TO_THE_PRODUCT'); ?>
                    </div> <?php
                }
                else
                {
                    Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' );
                    $next_issue = CitruscartHelperSubscription::getMarginalIssue( $row->product_id );
                    $last_issue = CitruscartHelperSubscription::getMarginalIssue( $row->product_id, 'DESC' );
                    $num_issues = CitruscartHelperSubscription::getNumberIssues( $row->product_id );
                    ?> [<?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&view=products&task=setissues&id=".$row->product_id."&tmpl=component", JText::_('COM_CITRUSCART_SET_ISSUES') ); ?>]<br /> <?php
                    if( isset( $next_issue ) )
                        echo '<b>'.JText::_('COM_CITRUSCART_NEXT_ISSUE_PUBLISHED').':</b> '.JHTML::_('date', $next_issue->publishing_date, JText::_('DATE_FORMAT_LC4') ).'<br />';
                    if( isset( $last_issue ) )
                        echo '<b>'.JText::_('COM_CITRUSCART_LAST_ISSUE_PUBLISHED').':</b> '.JHTML::_('date', $last_issue->publishing_date, JText::_('DATE_FORMAT_LC4') ).'<br />';
                    echo '<b>'.JText::_('COM_CITRUSCART_ISSUES_LEFT').':</b> '.$num_issues;?><br /> <?php } ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_WITH_PRO_RATED_CHARGES'); ?>
        </legend>
        <div class="note well">
            <?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_WITH_PRO-RATED_CHARGES_NOTE'); ?>
        </div>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <?php $onclick_prorated = 'showProRatedFields();'; ?>
            <tr>
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PRODUCT_CHARGES_PRO-RATED'); ?>:</td>
                <td>
                    <div class="control-group">
                        <div class="controls">
                            <fieldset id="subscription_lifetime" class="radio btn-group">
                                <input type="radio" <?php if ( !$row->subscription_prorated ) { echo "checked='checked'"; } ?> value="0" name="subscription_prorated" id="subscription_prorated0" onchange="<?php echo $onclick_prorated; ?>" /><label for="subscription_prorated0"><?php echo JText::_('COM_CITRUSCART_NO'); ?> </label> <input type="radio" <?php if ( $row->subscription_prorated ) { echo "checked='checked'"; } ?> value="1" name="subscription_prorated" id="subscription_prorated1" onchange="<?php echo $onclick_prorated; ?>" /><label for="subscription_prorated1"><?php echo JText::_('COM_CITRUSCART_YES'); ?> </label>
                            </fieldset>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="prorated_related">
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_PRO-RATED_INITIAL_CHARGE'); ?>:</td>
                <td>
                    <div class="control-group">
                        <div class="controls">
                            <fieldset id="product_enabled" class="radio btn-group">
                                <input type="radio" <?php if ( !$row->subscription_prorated_charge ) { echo "checked='checked'"; } ?> value="0" name="subscription_prorated_charge" id="subscription_prorated_charge0" /><label for="subscription_prorated_charge0"><?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_PRO-RATED_CHARGE_FULL'); ?> </label> <input type="radio" <?php if ( $row->subscription_prorated_charge ) { echo "checked='checked'"; } ?> value="1" name="subscription_prorated_charge" id="subscription_prorated_charge1" /><label for="subscription_prorated_charge1"><?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_PRO-RATED_CHARGE_PRO-RATED'); ?> </label>

                            </fieldset>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="prorated_related">
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_PRO-RATED_DATE'); ?>:<br /> <?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_PRO-RATED_DATE_NOTE');?>
                </td>
                <td><input name="subscription_prorated_date" id="subscription_prorated_date" value="<?php echo $row->subscription_prorated_date; ?>" size="8" maxlength="5" type="text" />
                </td>
            </tr>
            <tr class="prorated_related">
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_PRO-RATED_TERM'); ?>:</td>
                <td>
                    <div class="control-group">
                        <div class="controls">
                            <fieldset id="product_enabled" class="radio btn-group">
                                <input type="radio" <?php if ( $row->subscription_prorated_term == 'D' ) { echo "checked='checked'"; } ?> value="D" name="subscription_prorated_term" id="subscription_prorated_termD" /><label for="subscription_prorated_termD"><?php echo JText::_('COM_CITRUSCART_DAY'); ?> </label> <input type="radio" <?php if ( $row->subscription_prorated_term == 'M' ) { echo "checked='checked'"; } ?> value="M" name="subscription_prorated_term" id="subscription_prorated_termM" /><label for="subscription_prorated_termM"><?php echo JText::_('COM_CITRUSCART_MONTH'); ?> </label>

                            </fieldset>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div style="float: left; width: 50%;">
    <div class="well options">
        <legend>
            <?php echo JText::_('COM_CITRUSCART_SUBSCRIPTION_WITH_RECURRING_CHARGES'); ?>
        </legend>
        <table class="table table-striped table-bordered" style="width: 100%;">
            <tr>
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_PRODUCT_CHARGES_RECUR'); ?>:</td>
                <td>
                    <div class="control-group">
                        <div class="controls">
                            <fieldset id="product_enabled" class="radio btn-group">
                                <input type="radio" <?php if (empty($row->product_recurs)) { echo "checked='checked'"; } ?> value="0" name="product_recurs" id="product_recurs0" /><label class="btn" for="product_recurs0"><?php echo JText::_('COM_CITRUSCART_NO'); ?> </label> <input type="radio" <?php if (!empty($row->product_recurs)) { echo "checked='checked'"; } ?> value="1" name="product_recurs" id="product_recurs1" /><label class="btn" for="product_recurs1"><?php echo JText::_('COM_CITRUSCART_YES'); ?> </label>

                            </fieldset>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_NUMBER_OF_RECURRING_CHARGES'); ?>:</td>
                <td><input name="recurring_payments" id="recurring_payments" value="<?php echo $row->recurring_payments; ?>" size="10" maxlength="10" type="text" />
                </td>
            </tr>
            <tr>
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_RECURRING_PERIOD_INTERVAL'); ?>:</td>
                <td><input name="recurring_period_interval" id="recurring_period_interval" value="<?php echo $row->recurring_period_interval; ?>" size="10" maxlength="10" type="text" />
                </td>
            </tr>
            <tr>
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_RECURRING_PERIOD_UNITS'); ?>:</td>
                <td><?php echo CitruscartSelect::periodUnit( $row->recurring_period_unit, 'recurring_period_unit' ); ?>
                </td>
            </tr>
            <tr class="prorated_unrelated">
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_TRIAL_PERIOD'); ?>:</td>
                <td><?php  echo CitruscartSelect::btbooleanlist( 'recurring_trial', '', $row->recurring_trial ); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 125px; text-align: right;" class="key trial_price"><?php echo JText::_('COM_CITRUSCART_TRIAL_PERIOD_PRICE'); ?>:</td>
                <td><input name="recurring_trial_price" id="recurring_trial_price" value="<?php echo $row->recurring_trial_price; ?>" size="10" maxlength="10" type="text" />
                </td>
            </tr>
            <tr class="prorated_unrelated">
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_TRIAL_PERIOD_INTERVAL'); ?>:</td>
                <td><input name="recurring_trial_period_interval" id="recurring_trial_period_interval" value="<?php echo $row->recurring_trial_period_interval; ?>" size="10" maxlength="10" type="text" />
                </td>
            </tr>
            <tr class="prorated_unrelated">
                <td style="width: 125px; text-align: right;" class="dsc-key"><?php echo JText::_('COM_CITRUSCART_TRIAL_PERIOD_UNITS'); ?>:</td>
                <td><?php echo CitruscartSelect::periodUnit( $row->recurring_trial_period_unit, 'recurring_trial_period_unit' ); ?>
                </td>
            </tr>
        </table>
    </div>

</div>

<div style="clear: both;"></div>
