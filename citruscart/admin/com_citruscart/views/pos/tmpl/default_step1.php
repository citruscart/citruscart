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



<ul class="nav nav-tabs" id="myTab">
  <li class="active"><a href="#"><?php echo JText::_('COM_CITRUSCART_POS_STEP1_SELECT_USER'); ?></a></li>
 <li class="disabled"><a href=""><?php echo JText::_('COM_CITRUSCART_POS_STEP2_SELECT_PRODUCTS'); ?></a></li>
  <li class="disabled"><a href=""><?php echo JText::_('COM_CITRUSCART_POS_STEP3_SELECT_PAYMENT_SHIPPING_METHODS'); ?></a></li>
  <li  class="disabled"><a href=""><?php echo JText::_('COM_CITRUSCART_POS_STEP4_REVIEW_SUBMIT_ORDER'); ?></a></li>
    <li  class="disabled"><a href=""><?php echo JText::_('COM_CITRUSCART_POS_STEP5_PAYMENT_CONFIRMATION'); ?></a></li>
</ul>
<div class="progress">
  <div class="bar" style="width: 10%;"></div>
</div>

            <h2><?php echo JText::_('COM_CITRUSCART_SELECT_USER_OR_CREATE_ONE'); ?></h2>
          <div class="clearfix">
            <div id="validation_message"></div>



<div class="tabbable tabs-left">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#existing"><?php echo JText::_('COM_CITRUSCART_USE_AN_EXISTING_USER'); ?></a></li>
    <li class=""><a data-toggle="tab" href="#create"><?php echo JText::_('COM_CITRUSCART_CREATE_A_NEW_USER'); ?></a></li>
    <li class=""><a data-toggle="tab" href="#anonymous"><?php echo JText::_('COM_CITRUSCART_ANONYMOUS'); ?></a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="existing">
             <div class="option userOption well well-small">
                <label class="radio">
                <input type="radio" id="radioExisting" name="user_type" value="existing" <?php if ($this->session->get('user_type', '', 'citruscart_pos') == 'existing' || $this->session->get('user_type', '', 'citruscart_pos') == '') { ?>checked="checked" <?php } ?> /><?php echo JText::_('COM_CITRUSCART_USE_AN_EXISTING_USER'); ?>

                </label>
                <div id="existing" class="option_data">
                    <?php echo $this->getModel('elementUser')->fetchElement( 'user_id', $this->session->get('user_id', '', 'citruscart_pos' ) ); ?>
                    <?php echo $this->getModel('elementUser')->clearElement( 'user_id', '' ); ?>
                </div>
                <div class="clearfix"><br></div>
            </div>
    </div>
     <div class="tab-pane" id="create">
            <div class="option userOption well well-small">
                <label class="radio">
                <input type="radio" id="radioCreate" name="user_type" value="new" <?php if ($this->session->get('user_type', '', 'citruscart_pos') == 'new') { ?>checked="checked" <?php } ?> /><?php echo JText::_('COM_CITRUSCART_CREATE_A_NEW_USER'); ?>
                </label>
                <div id="new" class="option_data">
                    <input type="text" name="new_email" value="<?php echo $this->session->get('new_email', JText::_('COM_CITRUSCART_EMAIL'), 'citruscart_pos' ); ?>" size="40" onclick="CitruscartClearInput( this, '<?php echo JText::_('COM_CITRUSCART_EMAIL'); ?>' );" />
                    <input type="text" name="new_name" value="<?php echo $this->session->get('new_name', JText::_('COM_CITRUSCART_FULLNAME'), 'citruscart_pos' ); ?>" size="75" onclick="CitruscartClearInput( this, '<?php echo JText::_('COM_CITRUSCART_FULLNAME'); ?>' );" />
                </div>
                <div class="option_data">
                    <input id="new_username_create" type="checkbox" name="new_username_create" value="yes" checked="checked" /><?php echo JText::_('COM_CITRUSCART_AUTO_CREATE_USERNAME'); ?>
                   <div id="createUsernameHolder" style="display:none;">
                    <input type="text"  name="new_username" value="<?php echo $this->session->get('new_username', JText::_('COM_CITRUSCART_USERNAME'), 'citruscart_pos' ); ?>" size="40" onclick="CitruscartClearInput( this, '<?php echo JText::_('COM_CITRUSCART_USERNAME'); ?>' );" />
                    </div>
                </div>
            </div>
    </div>
     <div class="tab-pane" id="anonymous">
             <div class="option userOption well well-small">
                <label class="radio">
                <input type="radio" id="radioAnno" name="user_type" value="anonymous" <?php if ($this->session->get('user_type', '', 'citruscart_pos') == 'anonymous') { ?>checked="checked" <?php } ?> /><?php echo JText::_('COM_CITRUSCART_ANONYMOUS'); ?>
                </label>
                <div id="anonymous" class="option_data">
                    <input type="checkbox" name="anon_emails" value="yes" />
                    <?php echo JText::_('COM_CITRUSCART_SEND_ANON_EMAILS'); ?>
                    <div class="option_data">
                        <?php echo JText::_('COM_CITRUSCART_ANON_EMAILS'); ?>
                        <br/>
                        <input type="text" name="anon_email" value="<?php echo $this->session->get('anon_email', JText::_('COM_CITRUSCART_EMAIL'), 'citruscart_pos' ); ?>" size="40" onclick="CitruscartClearInput( this, '<?php echo JText::_('COM_CITRUSCART_EMAIL'); ?>' );" />
                    </div>
                </div>
            </div>
    </div>
  </div>
</div>







            <div class="continue">
                <?php $onclick = "CitruscartValidation( '" . $this->validation_url . "', 'validation_message', 'saveStep1', document.adminForm, true, '".JText::_('COM_CITRUSCART_VALIDATING')."' );"; ?>
                <input onclick="<?php echo $onclick; ?>" value="<?php echo JText::_('COM_CITRUSCART_CONTINUE_STEP1'); ?>" type="button" class="button btn btn-success" />
            </div>
        </div>

<script>
//this is just for testing, could probably be better when moved to remote JS
jQuery(document).ready(function() {
    jQuery('a[data-toggle="tab"]').on('shown', function (e) {
  e.target // activated tab
  e.relatedTarget // previous tab
  // uncheck all radio buttons, and than find and check the radio button in active tab
  var id = jQuery(e.target).attr('href');
  jQuery('input:radio[name=user_type]').attr('checked',false);
  jQuery(id + ' input:radio').attr('checked',true);
})
    jQuery('#new_username_create').click(function() {
        jQuery('#createUsernameHolder').toggle();
    })

});






</script>


<input type="hidden" name="nextstep" id="nextstep" value="step2" />