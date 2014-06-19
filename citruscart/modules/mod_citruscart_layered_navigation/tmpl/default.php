<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('stylesheet', 'mod_citruscart_layered_navigation.css', 'modules/mod_citruscart_layered_navigation/includes/css/');?>

<?php if($found):?>

<span class="citruscart_layered_nav_<?php echo $params->get('multi_mode', 1) ? 'multi' : 'single'?>">

<?php if(count($filters)):?>
<h3><?php echo JText::_('COM_CITRUSCART_CURRENTLY_SHOPPING_BY');?></h3>
	<ul class="citruscart_browse_currently" id="citruscart_browse_currently">
		<?php foreach($filters as $filter):?>
		<li>
			<a class="btn-remove" title="<?php echo JText::_('COM_CITRUSCART_REMOVE_THIS_ITEM');?>" href="<?php echo JRoute::_($filter->link);?>"><?php echo JText::_('COM_CITRUSCART_REMOVE_THIS_ITEM');?></a>
			<span class="label"><?php echo $filter->label;?>:</span>
			<span class="value"><?php echo $filter->value;?></span>
		</li>
		<?php endforeach;?>
	</ul>
<?php endif;?>

<?php if($trackcatcount || $priceRanges || $attributes || $manufacturers):?>
<h3><?php echo JText::_('COM_CITRUSCART_SHOPPING_OPTIONS');?></h3>
<?php endif;?>

<?php if($trackcatcount > 0):?>

	<?php $rootTxt = $params->get('roottext'); ?>
	<h4><?php echo $helper->category_current->isroot && !empty( $rootTxt ) ? $rootTxt : $helper->category_current->category_name;?></h4>

	<ul id="citruscart_browse_category">
	<?php foreach($categories as $category):?>

		<?php if($category->category_id != $helper->category_current->category_id && $category->product_total > 0):?>
		<li>
			<a href="<?php echo $category->link;?>">
				<span class="refinementLink">
					<?php echo $category->category_name;?>
				</span>
			</a>
			<span class="narrowValue">
				(<?php echo $category->product_total;?>)
			</span>
		</li>
		<?php endif;?>

	<?php endforeach;?>

	</ul>
<?php endif;?>

<?php if(count($priceRanges) > 0):?>
	<h4><?php echo JText::_('COM_CITRUSCART_PRICE');?></h4>
	<ul id="citruscart_browse_pricerange">
		<?php foreach($priceRanges as $priceRange):?>
			<?php if($priceRange->total > 0):?>
			<li>
				<a href="<?php echo JRoute::_($priceRange->link);?>">
					<span class="refinementLink">
						<?php echo CitruscartHelperBase::currency($priceRange->price_from).' - '.CitruscartHelperBase::currency($priceRange->price_to);?>
					</span>
				</a>
				<span class="narrowValue">
					(<?php echo $priceRange->total;?>)
				</span>
			</li>
			<?php endif;?>
		<?php endforeach;?>
	</ul>
<?php endif;?>

<?php if(count($attributes) > 0):?>
	<?php foreach($attributes as $key=>$val):?>
		<h4><?php echo $key;?></h4>
		<ul id="citruscart_browse_attribute">
			<?php foreach($val as $k=>$option):?>
				<li>
					<a href="<?php echo JRoute::_($option->link.'&filter_attribute_set='.implode(',', $option->attributes));?>">
						<span class="refinementLink">
						<?php echo $k;?>
						</span>
					</a>
					<span class="narrowValue">
						(<?php echo count(array_unique($option->products));?>)
					</span>
				</li>
			<?php endforeach;?>
		</ul>
	<?php endforeach;?>
<?php endif;?>

<?php if(count($ratings) > 0):?>
	<h4><?php echo JText::_('COM_CITRUSCART_AVG_CUSTOMER_RATING');?></h4>
	<ul id="citruscart_browse_rating">
		<?php foreach($ratings as $rating):?>
			<li>
				<a href="<?php echo JRoute::_($rating->link);?>">
					<span class="refinementLink">
						<?php echo $rating->rating_name;?>
					</span>
				</a>
				<a href="<?php echo JRoute::_($rating->link);?>">
					<span class="refinementLink">
						<?php echo JText::_('COM_CITRUSCART_AND_UP');?>
					</span>
				</a>
				<span class="narrowValue">
					(<?php echo $rating->total;?>)
				</span>
			</li>
		<?php endforeach;?>
	</ul>
<?php endif;?>

<?php if(count($manufacturers) > 0):?>
	<h4><?php echo JText::_('COM_CITRUSCART_MANUFACTURERS');?></h4>
	<ul id="citruscart_browse_manufacturer">
		<?php foreach($manufacturers as $manufacturer):?>
			<?php if($manufacturer->total > 0):?>
			<li>
				<a href="<?php echo JRoute::_($manufacturer->link);?>">
					<span class="refinementLink">
						<?php echo $manufacturer->manufacturer_name;?>
					</span>
				</a>
				<span class="narrowValue">
					(<?php echo $manufacturer->total;?>)
				</span>
			</li>
			<?php endif;?>
		<?php endforeach;?>
	</ul>
<?php endif;?>

</span>

<?php endif;?>