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
defined('_JEXEC') or die('Restricted access');?>


<ul class="nav nav-tabs" id="myTab">
  <li ><a href="index.php?option=com_citruscart&view=pos"><?php echo JText::_('COM_CITRUSCART_POS_STEP1_SELECT_USER'); ?></a></li>
  <li class="active"><a href=""><?php echo JText::_('COM_CITRUSCART_POS_STEP2_SELECT_PRODUCTS'); ?></a></li>
  <li class="disabled"><a href=""><?php echo JText::_('COM_CITRUSCART_POS_STEP3_SELECT_PAYMENT_SHIPPING_METHODS'); ?></a></li>
  <li  class="disabled"><a href=""><?php echo JText::_('COM_CITRUSCART_POS_STEP4_REVIEW_SUBMIT_ORDER'); ?></a></li>
    <li  class="disabled"><a href=""><?php echo JText::_('COM_CITRUSCART_POS_STEP5_PAYMENT_CONFIRMATION'); ?></a></li>
</ul>
<div class="progress">
  <div class="bar" style="width: 30%;"></div>
</div>

  <div id="validation_message"></div>
<div class="accordion" id="accordion2">
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
       <?php echo JText::_('COM_CITRUSCART_POS_STEP1_SELECT_USER'); ?>
      </a>
    </div>
    <div id="collapseOne" class="accordion-body collapse">
      <div class="accordion-inner">
       <?php  echo $this->step1_inactive; ?>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
        <?php echo JText::_('COM_CITRUSCART_SELECT_PRODUCTS'); ?>
      </a>
    </div>
    <div id="collapseTwo" class="accordion-body collapse  in">
      <div class="accordion-inner">
       <span class="new_product">
                    <?php echo CitruscartURL::popup( "index.php?option=com_citruscart&view=pos&task=addproducts&tmpl=component", JText::_('COM_CITRUSCART_ADD_NEW_PRODUCT_TO_ORDER') , array('width'=>800, 'height'=>400)); ?>
                </span>
                 <div id="cart">
                <?php if (empty($this->cart)): ?>
                    <?php echo JText::_('COM_CITRUSCART_NO_ITEMS_IN_CART'); ?>
                <?php else: ?>
                	<?php echo $this->cart;?>
                <?php endif; ?>
            </div>

            <div class="continue">

            	<!--<input type="checkbox" value="1" name="skippayment" id="skippayment"> <?php echo JText::_('COM_CITRUSCART_SKIP_PAYMENT');?>-->
            	<!--<a class="modal" href="" />[?]</a>-->
                <?php $onclick = "CitruscartValidation( '" . $this->validation_url . "', 'validation_message', 'saveStep2', document.adminForm, true, '".JText::_('COM_CITRUSCART_VALIDATING')."' );"; ?>
                <input onclick="<?php echo $onclick; ?>" value="<?php echo JText::_('COM_CITRUSCART_CONTINUE_STEP2'); ?>" type="button" class="button btn btn-success" />
            </div>

      </div>
    </div>
  </div>
</div>









<input type="hidden" name="nextstep" id="nextstep" value="step3" />