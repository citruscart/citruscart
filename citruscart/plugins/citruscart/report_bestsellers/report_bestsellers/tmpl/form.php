<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access'); ?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>

<?php $state = $vars->state; ?>
<?php $attribs = array('class' => ''); ?>

<?php // echo JText::_('COM_CITRUSCART_THIS_REPORTS_ON_BEST_SELLING_PRODUCTS'); ?>
<div class="navbar">
  <div class="navbar-inner">
    <a class="brand" href="#">BESTSELLERS</a>

    <ul class="nav navbar-form pull-left">

    	 <li class="divider-vertical"></li>
      <li><?php echo CitruscartSelect::reportrange( $state->filter_range ? $state->filter_range : 'custom', 'filter_range', $attribs, 'range', true ); ?></li>
      <li class="divider-vertical"></li>
      <li>
      	<div class="input-prepend input-append">
      	<span class="add-on "><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span>
	            <?php echo JHTML::calendar( $state->filter_date_from, "filter_date_from", "filter_date_from", '%Y-%m-%d 00:00:00' ); ?></div></li>
	            <li class="divider-vertical"></li>
      <li>
	            <div class="input-prepend input-append">
	            	<span class="add-on"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span>
	            	<?php echo JHTML::calendar( $state->filter_date_to, "filter_date_to", "filter_date_to", '%Y-%m-%d 00:00:00' ); ?></div></li>

	            <li class="divider-vertical"></li>
	            <li>  <?php //$attribs = array('class' => '',  'onchange' => 'javascript:submitbutton(\'view\').click;'); ?><span class="label pull-left"><?php echo JText::_('LIMIT'); ?></span>
	            <?php echo CitruscartSelect::limit( $state->limit ? $state->limit : '20', 'limit', $attribs, 'limit', true ); ?></li>
    </ul></li>


  </div>
</div>
