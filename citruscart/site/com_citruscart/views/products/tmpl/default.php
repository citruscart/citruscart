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

$doc = JFactory::getDocument();
$doc->addScript(JUri::root().'media/citruscart/js/citruscart.js');
JHtml::_('stylesheet','media/citruscart/css/citruscart.css');


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
$app = JFactory::getApplication();
?>
<style>
.citruscart-product-main-images {
	width:80px;
	height:150px;
	margin-left:5px;
	}
#image-list{

	}
.ul-images{
	margin-left :5px;
	margin-bottom:5px;
	padding :5px;
	border:1px solid #ccc;
	text-align: center;
	width:272px;
	height:250px;
	}
	
#citruscart .citruscart-product-main-images {
width:250px;
}
</style>
<div id="citruscart" class="products default">

    <?php if ($this->level > 1 && $config->get('display_citruscart_pathway')) : ?>
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
        <?php if (!empty($citems)) : ?>
            <div id="citruscart_subcategories" class="dsc-wrap">
                <?php if ($this->level > 1) { 
                	//echo '<h3>'.JText::_('COM_CITRUSCART_SUBCATEGORIES').'</h3>'; 
                 } ?>
                <?php
                $i = 0;
                $subcategories_per_line = $config->get('subcategories_per_line', '5');
				?>                                
                <?php foreach ($citems as $citem) :
                ?>
                
                    <div class="dsc-wrap subcategory category-<?php echo $citem->category_id; ?>">
                        <?php if( $citem->display_name_subcategory ) : ?>
                        <h5 class="subcategory_name">
                            <a href="<?php echo JRoute::_( "index.php?option=com_citruscart&view=products&filter_category=".$citem->category_id.$citem->slug.$citem->itemid_string ); ?>">
                            <?php echo $citem->category_name; ?>
                            </a>
                        </h5>
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
                    if ( ($i+1) >= $subcategories_per_line)      {
                        ?>
                        
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
	<div class="row-fluid">
	<div class="col-md-6">
    <?php if (($items)) : ?>
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
        		<a class="modal"  rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_("&limitstart=".$state->limitstart."&filter_sortby=".$state->filter_sortby."&filter_dir=".$dir);?>">
						<img src="<?php echo Citruscart::getURL('images').$img_dir?>" alt="filter_direction"/>
        		</a>
        	</span>
    	</div>
        <?php echo $this->form['validate']; ?>
    </form>
    <?php endif;?>
    </div>
    <div class="col-md-6">

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
	</div>
	</div>
		<ul id="image-list" class="nav navbar-nav">
			<?php foreach ($items as $item) :?>

			<li class="ul-images"  onmouseover="showBuyInfo()">
			  <span>
			  <?php $thumb = CitruscartHelperProduct::getImage($item->product_id, '', $item->product_name); ?>
               <?php if ($thumb) { ?>
                      <!--<div class="dsc-wrap product_listimage">-->
                            <a href="<?php echo JRoute::_( $item->link . $item->itemid_string ); ?>">
                                <?php echo $thumb; ?>
                            </a>
                        <!--</div>-->
                <?php } ?>
					<div class="product-short-info">
                        <h6>
                        	<a href="<?php echo JRoute::_($item->link . $item->itemid_string ); ?>"><?php echo htmlspecialchars_decode( $item->product_name ); ?></a>
                        </h6>
	                     <?php if ( $config->get('product_review_enable', '0') ) : ?>
	                   	 <!--  <div class="dsc-wrap product_rating"> -->
	                       <?php echo CitruscartHelperProduct::getRatingImage( $item->product_rating, $this ); ?>
	                       <?php if (!empty($item->product_comments)) : ?>
	                       <span class="product_comments_count">(<?php echo $item->product_comments; ?>)</span>
	                       <?php endif; ?>

	                    <?php endif; ?>
	                      <span id="product_price_<?php echo $item->product_id; ?>" class="product_price">
					    	<?php  echo CitruscartHelperProduct::dispayPriceWithTax($item->price, @$item->tax, @$this->show_tax); ?>
					    	 <!-- For UE States, we should let the admin choose to show (+19% vat) and (link to the shipping rates) -->
					    	<br />
					    	<?php if(Citruscart::getInstance()->get( 'display_prices_with_shipping') && !empty($item->product_ships)):?>
					    	<?php echo CitruscartUrl::popup( JRoute::_($this->shipping_cost_link.'&tmpl=component'), JText::_('COM_CITRUSCART_LINK_TO_SHIPPING_COST') ); ?>
					    	<?php endif;?>
					   		 </span>
					 </div>
					 <div class="product-buy-info">
						<?php // echo CitruscartHelperProduct::getCartButton($item->product_id);?>
				 </div>
	          </span>
	         </li>
            <?php endforeach; ?>
            </ul>
    <?php endif; ?>

</div>
