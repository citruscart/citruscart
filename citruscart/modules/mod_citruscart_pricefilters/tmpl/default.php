<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $doc = JFactory::getDocument(); ?>
<?php $doc->addStyleSheet( JURI::root(true).'/modules/mod_citruscart_pricefilters/tmpl/mod_citruscart_pricefilters.css'); ?>
<div id="citruscart-mod-price_filter">
<h4><?php echo JText::_('COM_CITRUSCART_PRICE');?></h4>
<ul class="nav nav-tab" id="citruscart_pricefilter_mod">
<?php if (empty($priceRanges)) { ?>
    <li class="emptyfilter">
    	<?php echo JText::_('COM_CITRUSCART_NO_CATEGORY_MANUFACTURER_FOUND'); ?>
    </li>
<?php }else{?>

	<?php $i = 1;?>
	<?php foreach ($priceRanges as $link => $range ) : ?>
		<?php $selected = JRequest::getInt('rangeselected') ?>
		<?php $class = ($selected == $i) ? 'range selected' : 'range';?>
		<li class="<?php echo $class;?>" >
			<a href="<?php echo JRoute::_( "index.php?option=com_citruscart&view=products".$link."&rangeselected=".$i ); ?>">
				<span><?php echo $range; ?></span>
			</a>
		</li>
	<?php $i++;?>
	<?php endforeach; ?>

<?php }?>

</ul>
<br/>
<div>
<?php if ($show_remove) : ?>
    <a class="btn btn-error" href="<?php echo JRoute::_( $remove_pricefilter_url ); ?>"><?php echo JText::_('COM_CITRUSCART_REMOVE_FILTER') ?></a>
<?php endif; ?>
</div>
</div>
