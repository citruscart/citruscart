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
defined('_JEXEC') or die('Restricted access');

	JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
	$state = @$this->state;
	$items = @$this->items;
	$citems = @$this->citems;
?>

<div id="citruscart" class="products directory">
    <div id="citruscart_categories">
        <div id='citruscart_category_header'>
            <h6><?php echo JText::_('COM_CITRUSCART_BROWSE_CATEGORIES'); ?></h6>
            <div class='category_description'><?php echo $this->cat->category_description; ?></div>
        </div>

        <?php if (!empty($citems)) : ?>
            <div class="directory_categories" style="width: 100%;">
                <?php
                foreach ($citems as $citem) :
                    $model = JModelLegacy::getInstance('Categories', 'CitruscartModel');
                    $model->setState('filter_enabled', '1');
                    $model->setState('filter_level', $citem->category_id);
                    $model->setState('order', 'tbl.ordering');
                    $model->setState('direction', 'ASC');
                    $categories = $model->getList();
                    ?>
                    <div class="directory_category" style="width: 49%; float: left; margin: 3px;">
                        <div class="directory_category_header" style="padding: 5px; background-color: #DDDDDD; font-size: 120%; font-weight: bold;">
                            <span><?php echo $citem->category_name; ?></span>
                        </div>
                        <?php if (!empty($categories)) { ?>
                            <!--  	<ul class="directory_subcategory">-->
                            <ul class="nav">
                            <?php foreach ($categories as $category) {
                                $pmodel = JModelLegacy::getInstance('Products', 'CitruscartModel');
                                $pmodel->setState('filter_category', $category->category_id);
                                $products = $pmodel->getTotal();
                                ?>
                                <li>
                                <a href="<?php echo JRoute::_( "index.php?option=com_citruscart&view=products&filter_category=".$category->category_id.$category->slug."&Itemid=".$citem->itemid ); ?>">
                                <?php echo $category->category_name; ?> (<?php echo $products; ?>)
                                </a>
                                </li>
                            <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                <?php
                endforeach;
                ?>
                <div class="reset"></div>
            </div>
        <?php endif; ?>
    </div>

</div>