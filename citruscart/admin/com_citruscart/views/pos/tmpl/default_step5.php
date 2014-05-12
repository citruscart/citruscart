<?php defined('_JEXEC') or die('Restricted access');?>
<?php JHTML::_('stylesheet', 'Citruscart.css', 'media/citruscart/css/'); ?>
<?php 
$order_link = @$this->order_link;
$plugin_html = @$this->plugin_html;
?>


<?php $display_credits = Citruscart::getInstance()->get( 'display_credits', '0' ); ?>

<ul class="nav nav-tabs" id="myTab">
  <li><a href="index.php?option=com_citruscart&view=pos"><?php echo JText::_('COM_CITRUSCART_POS_STEP1_SELECT_USER'); ?></a></li>
  <li><a href="index.php?option=com_citruscart&view=pos&nextstep=step2"><?php echo JText::_('COM_CITRUSCART_POS_STEP2_SELECT_PRODUCTS'); ?></a></li>
  <li class=""><a href=""><?php echo JText::_('COM_CITRUSCART_POS_STEP3_SELECT_PAYMENT_SHIPPING_METHODS'); ?></a></li>
  <li class=""><a href=""><?php echo JText::_('COM_CITRUSCART_POS_STEP4_REVIEW_SUBMIT_ORDER'); ?></a></li>
  <li class="active"><a href=""><?php echo JText::_('COM_CITRUSCART_POS_STEP5_PAYMENT_CONFIRMATION'); ?></a></li>
</ul>
<div class="progress">
  <div class="bar" style="width: 100%;"></div>
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
       <?php echo JText::_('COM_CITRUSCART_POS_STEP2_SELECT_PRODUCTS'); ?>
      </a>
    </div>
    <div id="collapseTwo" class="accordion-body collapse">
      <div class="accordion-inner">
      <div id="orderSummary">
				<?php echo $this->orderSummary;?>				
			</div>
            
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">
        <?php echo JText::_('COM_CITRUSCART_POS_STEP3_SELECT_PAYMENT_SHIPPING_METHODS'); ?>
      </a>
    </div>
    <div id="collapseThree" class="accordion-body collapse">
      <div class="accordion-inner">
       
		
            
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFour">
        <?php echo JText::_('COM_CITRUSCART_POS_STEP4_REVIEW_SUBMIT_ORDER'); ?>
      </a>
    </div>
    <div id="collapseFour" class="accordion-body collapse in">
      <div class="accordion-inner">
       
		
            
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFive">
       <?php echo JText::_('COM_CITRUSCART_POS_STEP5_PAYMENT_CONFIRMATION'); ?>
      </a>
    </div>
    <div id="collapseFive" class="accordion-body collapse in">
      <div class="accordion-inner">
       <?php echo $plugin_html; ?>
			
			<div class="note">
				<a href="<?php echo JRoute::_($order_link);?>">
				<?php echo JText::_('COM_CITRUSCART_CLICK_TO_VIEW_OR_EDIT_ORDER');?>
				</a>
			</div>
			<?php foreach ($this->articles as $article) : ?>
			<div class="postpayment_article">
				<?php echo $article;?>
			</div>
			<?php endforeach;?>
		
            
      </div>
    </div>
  </div>
</div>



	<input type="hidden" name="nextstep" id="nextstep" value="step1" />

