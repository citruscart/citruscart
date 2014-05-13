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


JHTML::_('script', 'citruscart.js', 'media/citruscart/js/');
$state = $this->state;
$items = $this->items;

$citems = $this->citems;
$form = $this->form;
Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
$config = Citruscart::getInstance();
$product_compare = $config->get('enable_product_compare', '1');
$plugins_short_desc = $config->get( 'content_plugins_product_desc', '0' );

$js_strings = array( 'COM_CITRUSCART_ADDING_PRODUCT_FOR_COMPARISON', 'COM_CITRUSCART_REMOVING_PRODUCT' );
CitruscartHelperBase::addJsTranslationStrings( $js_strings );
?>
<div id="citruscart" class="products default">

    <?php if ($this->level > 1 && $config->get('display_Citruscart_pathway')) : ?>
        <div id='citruscart_breadcrumb'>
            <?php echo CitruscartHelperCategory::getPathName($this->cat->category_id, 'links'); ?>
        </div>
    <?php endif; ?>
    <?php if( $product_compare ):?>
    <?php $compareitems = $this->compareitems;?>
	<div id="validationmessage"></div>
	<?php endif;?>
    <?php if (!empty($this->pricefilter_applied)) : ?>
        <div id='citruscart_pricefilter'>
            <b><?php echo JText::_('COM_CITRUSCART_DISPLAYING_PRICE_RANGE') . ": "; ?></b>
            <?php echo $this->filterprice_from .  " - " . $this->filterprice_to; ?>
            <a href="<?php echo JRoute::_( $this->remove_pricefilter_url ); ?>"><?php echo JText::_('COM_CITRUSCART_REMOVE_FILTER') ?></a>
        </div>
    <?php endif; ?>

    <div id="citruscart_categories" class="dsc-wrap">
        <div id='citruscart_category_header' class="dsc-wrap">
            <?php if (isset($state->category_name)) : ?>
                <?php if (!empty($this->cat->category_full_image) || $config->get('use_default_category_image', '1')) : ?>
                    <img src="<?php echo CitruscartHelperCategory::getImage($this->cat->category_id, '', '', '', true); ?>" alt="" class="category image" />
                <?php endif; ?>
            <?php endif; ?>

         	<?php if( $this->cat->display_name_category ) : ?>
                <h2><?php echo $this->title; ?></h2>
            <?php endif; ?>

            <div class='category_description dsc-wrap'><?php echo $this->cat->category_description; ?></div>
        </div>

        <?php if (!empty($citems)) : ?>
            <div id="citruscart_subcategories" class="dsc-wrap">
                <?php if ($this->level > 1) { echo '<h3>'.JText::_('COM_CITRUSCART_SUBCATEGORIES').'</h3>'; } ?>
                <?php
                $i = 0;
                $subcategories_per_line = $config->get('subcategories_per_line', '5');
                foreach ($citems as $citem) :
                ?>
                    <div class="dsc-wrap subcategory category-<?php echo $citem->category_id; ?>">
                        <?php if( $citem->display_name_subcategory ) : ?>
                        <h3 class="subcategory_name">
                            <a href="<?php echo JRoute::_( "index.php?option=com_citruscart&view=products&filter_category=".$citem->category_id.$citem->slug.$citem->itemid_string ); ?>">
                            <?php echo $citem->category_name; ?>
                            </a>
                        </h3>
                        <?php endif; ?>
                        <?php if (!empty($citem->category_full_image) || $config->get('use_default_category_image', '1')) : ?>
                            <div class="dsc-wrap subcategory_thumb">
                                <a href="<?php echo JRoute::_( "index.php?option=com_citruscart&view=products&filter_category=".$citem->category_id.$citem->slug.$citem->itemid_string ); ?>">
                                <?php echo CitruscartHelperCategory::getImage($citem->category_id); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                    if ( ($i+1) >= $subcategories_per_line)
                    {
                        ?>
                        <div class="reset"></div>
                        <?php $i = 0;
                    }
                        else
                    {
                        $i++;
                    }
                endforeach;
                ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($items)) : ?>

     <?php if($config->get('display_sort_by', '1')) :?>
      <form action="<?php echo JRoute::_("&limitstart=".$state->limitstart )?>" method="post" name="adminForm_sort" enctype="multipart/form-data">
     	<div class="citruscart_sortby">
        	<?php Citruscart::load('CitruscartSelect', 'libray.select');?>
        	<span class="sort_by_label">
        	<?php echo JText::_('COM_CITRUSCART_SORT_BY');?>
        	</span>
        	<?php echo CitruscartSelect::productsortby( $state->filter_sortby, 'filter_sortby', array('onchange' => 'document.adminForm_sort.submit();'), 'filter_sortby', true, JText::_('COM_CITRUSCART_DEFAULT_ORDER'));?>
        	<span class="sort_by_direction">
        		<?php
        			if(strtolower($state->filter_dir) == 'asc')
        			{
        				$dir = 'desc';
        				$img_dir = 'arrow_down.png';
        			}
        			else
        			{
        				$dir = 'asc';
        				$img_dir = 'arrow_up.png';
        			}
        		?>
        		<a href="<?php echo JRoute::_("&limitstart=".$state->limitstart."&filter_sortby=".$state->filter_sortby."&filter_dir=".$dir);?>">
        			<img src="<?php echo Citruscart::getURL('images').$img_dir?>" alt="filter_direction"/>
        		</a>
        	</span>
    	</div>
        <?php echo $this->form['validate']; ?>
    </form>
    <?php endif;?>

        <div id="citruscart_products" class="dsc-wrap">
            <?php foreach ($items as $item) : ?>
            <div id="product-<?php echo $item->product_id; ?>" class="dsc-wrap product_item product-<?php echo $item->product_id; ?> <?php echo $item->product_classes; ?>">
                <?php $thumb = CitruscartHelperProduct::getImage($item->product_id, '', $item->product_name); ?>
                <?php if ($thumb) { ?>
                    <div class="product_thumb dsc-wrap">
                        <div class="dsc-wrap product_listimage">
                            <a href="<?php echo JRoute::_( $item->link . $item->itemid_string ); ?>">
                                <?php echo $thumb; ?>
                            </a>
                        </div>
                    </div>
                <?php } ?>

                <div class="dsc-wrap product_info">
                    <div class="dsc-wrap product_name">
                        <h3>
                            <a href="<?php echo JRoute::_($item->link . $item->itemid_string ); ?>">
                            <?php echo htmlspecialchars_decode( $item->product_name ); ?>
                            </a>
                        </h3>
                    </div>

                    <?php if ( $config->get('product_review_enable', '0') ) { ?>
                    <div class="dsc-wrap product_rating">
                       <?php echo CitruscartHelperProduct::getRatingImage( $item->product_rating, $this ); ?>
                       <?php if (!empty($item->product_comments)) : ?>
                       <span class="product_comments_count">(<?php echo $item->product_comments; ?>)</span>
                       <?php endif; ?>
                    </div>
                    <?php } ?>

                    <?php if (!empty($item->product_model) || !empty($item->product_sku)) { ?>
                        <div class="dsc-wrap product_numbers">
                            <span class="model">
                                <?php if (!empty($item->product_model)) : ?>
                                    <span class="title"><?php echo JText::_('COM_CITRUSCART_MODEL'); ?>:</span>
                                    <?php echo $item->product_model; ?>
                                <?php endif; ?>
                            </span>
                            <span class="sku">
                                <?php if (!empty($item->product_sku)) : ?>
                                    <span class="title"><?php echo JText::_('COM_CITRUSCART_SKU'); ?>:</span>
                                    <?php echo $item->product_sku; ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php } ?>

                    <div class="dsc-wrap product_minidesc">
                    <?php
                        if (!empty($item->product_description_short))
                        {
                        	$product_desc = $item->product_description_short;
                        }
                            else
                        {
                            $str = wordwrap($item->product_description, 200, '`|+');
                            $wrap_pos = strpos($str, '`|+');
                            if ($wrap_pos !== false) {
                                $product_desc = substr($str, 0, $wrap_pos).'...';
                            } else {
                                $product_desc = $str;
                            }
                        }

                        if( $plugins_short_desc )
                        	echo JHTML::_('content.prepare', $product_desc);
                        else
                           echo $product_desc;
                    ?>
                    </div>
                </div>

                <div id="product_buy_<?php echo $item->product_id; ?>" class="dsc-wrap product_buy">
                    <?php echo CitruscartHelperProduct::getCartButton( $item->product_id ); ?>
                </div>

                <?php
						/*
                	if( $product_compare && (($item->product_parameters->get('show_product_compare', '1')))) { ?>
                <div id="product_compare" class="dsc-wrap">
                	<input <?php echo in_array($item->product_id,$compareitems) ? 'checked' : '';?> type="checkbox" onclick="CitruscartAddProductToCompare(<?php echo $item->product_id;?>, 'CitruscartComparedProducts', this, true);">
               	 	<a href="<?php echo JRoute::_('index.php?option=com_citruscart&view=productcompare');?>">
               	 		<?php echo JText::_('COM_CITRUSCART_COMPARE')?>
               	 		<span class="arrow" >»</span>
               	 	</a>
            	</div>
        	    <?php } */ ?>

            </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($this->pagination) && method_exists($this->pagination, 'getResultsCounter')) { ?>
        <form action="<?php echo JRoute::_( $form['action']."&limitstart=".$state->limitstart )?>" method="post" name="adminForm" enctype="multipart/form-data">
        <div id="products_footer" class="pagination">
            <div id="results_counter"><?php echo $this->pagination->getResultsCounter(); ?></div>
            <?php if ($this->defines->get('disable_changing_list_limit')) { ?>
                <?php

                    $html = "<div class=\"list-footer\">\n";
                    $html .= $this->pagination->getPagesLinks();
                    $html .= "\n<div class=\"counter\">" . $this->pagination->getPagesCounter() . "</div>";
                    $html .= "\n<input type=\"hidden\" name=\"limitstart\" value=\"". $this->pagination->limitstart ."\" />";
                    $html .= "\n</div>";

                    echo $html;
                ?>
            <?php } else { ?>
                <?php echo $this->pagination->getListFooter(); ?>
            <?php } ?>
        </div>
        <?php echo $this->form['validate']; ?>
        </form>
        <?php } ?>

    <?php endif; ?>

</div>
