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
<?php JHtml::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $app = JFactory::getApplication(); ?>
<div id="citruscart" class="search default">
<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<?php echo CitruscartGrid::pagetooltip( $app->input->getString('view') ); ?>
    <div id="citruscart_searchfilters">
        <h2><?php echo JText::_('COM_CITRUSCART_ADVANCED_SEARCH'); ?></h2>
        <?php $attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
        <div class="row filtername" >
            <span class="label"><?php echo JText::_('COM_CITRUSCART_NAME'); ?>:</span>
            <input id="filter_name" name="filter_name" value="<?php echo $state->filter_name; ?>" size="25" class="text_area" />
        </div>

        <div class="row filtermulticategory" >
            <span class="label"><?php echo JText::_('COM_CITRUSCART_CATEGORY'); ?>: </span>
            <?php $catattribut = array('class' => 'inputbox', 'size' => '1','multiple' => 'yes' , 'size'=>5 );?>
            <?php echo CitruscartSelect::category( $state->filter_multicategory, 'filter_multicategory[]',$catattribut , 'filter_multicategory', true ); ?>
        </div>

        <div class="row filtershipping" >
            <span class="label"><?php echo JText::_('COM_CITRUSCART_REQUIRES_SHIPPING'); ?>: </span>
            <?php echo CitruscartSelect::booleans( $state->filter_ships, 'filter_ships', '', 'ships', true, "Doesn't Matter", 'Yes', 'No' ); ?>
        </div>

        <div class="row filtersku" >
            <span class="label"><?php echo JText::_('COM_CITRUSCART_SKU'); ?>: </span>
            <input id="filter_sku" name="filter_sku" value="<?php echo $state->filter_sku; ?>" size="15" class="text_area" />
        </div>

        <div class="row price">
        	<span class="label"><?php echo JText::_('COM_CITRUSCART_PRICE_RANGE'); ?>: </span>
            <div class="range">
            	<div><span class="label"><?php echo JText::_('COM_CITRUSCART_FROM'); ?>:</span> <input id="filter_price_from" name="filter_price_from" value="<?php echo $state->filter_price_from; ?>" size="5" class="input" class="text_area" /></div>
                <div><span class="label"><?php echo JText::_('COM_CITRUSCART_TO'); ?>:</span> <input id="filter_price_to" name="filter_price_to" value="<?php echo $state->filter_price_to; ?>" size="5" class="input" class="text_area" /></div>
        	</div>
        </div>

        <div class="reset"></div>

        <div class="row quantity">
            <span class="label"><?php echo JText::_('COM_CITRUSCART_SHOW_ONLY_ITEMS_THAT_ARE_IN_STOCK'); ?>: </span>
            <?php echo CitruscartSelect::booleanlist( 'filter_stock', '', $state->filter_stock ); ?>
        </div>

        <div class="row filterdescription" >
            <span class="label"><?php echo JText::_('COM_CITRUSCART_DESCRIPTION'); ?>: </span>
            <input id="filter_description" name="filter_description" value="<?php echo $state->filter_description; ?>" size="15" class="text_area" />
        </div>

        <div class="row filtermanufacturer" >
            <span class="label"><?php echo JText::_('COM_CITRUSCART_MANUFACTURER'); ?>: </span>
            <input id="filter_manufacturer" name="filter_manufacturer" value="<?php echo $state->filter_manufacturer; ?>" size="15" class="text_area" />
        </div>

        <div class="row submit" >
            <input id="filter_submit" name="filter_submit" type="submit" value="<?php echo JText::_('COM_CITRUSCART_SEARCH') ?>" class="btn" />
        </div>

        <div class="reset"></div>
    </div>

    <div id="citruscart_searchresults">
        <h2><?php echo JText::_('COM_CITRUSCART_SEARCH_RESULTS'); ?></h2>
        <div id="searchresults_sort">
            <div class="sortresults title"><?php echo JText::_('COM_CITRUSCART_SORT_RESULTS_BY'); ?>:</div>
            <div class="sortresults option"><?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.product_name", $state->direction, $state->order ); ?></div>
            <div class="sortresults option"><?php echo CitruscartGrid::sort( 'COM_CITRUSCART_SKU', "tbl.product_sku", $state->direction, $state->order ); ?></div>
            <div class="sortresults option"><?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PRICE', "price", $state->direction, $state->order ); ?></div>
            <div class="sortresults option"><?php echo CitruscartGrid::sort( 'COM_CITRUSCART_RATING', "tbl.product_rating", $state->direction, $state->order ); ?></div>
            <div class="sortresults option"><?php echo CitruscartGrid::sort( 'COM_CITRUSCART_REVIEWS', "tbl.product_comments", $state->direction, $state->order ); ?></div>
            <div class="reset"></div>
        </div>

        <div id="searchresults_results">
            <?php $i=0; $k=0; ?>
            <?php foreach ($items as $item) : ?>
            <div class="product_item">
                <div class="product_thumb">
                    <a href="<?php echo JRoute::_( $item->link."&filter_category=".$item->category_id."&Itemid=".$item->itemid ); ?>">
                        <?php echo CitruscartHelperProduct::getImage($item->product_id, 'id', $item->product_name, 'full', false, false, array( 'width'=>48 ) ); ?>
                    </a>
                </div>

                <div class="product_buy">
                    <?php if (empty($item->product_notforsale)) : ?>

                        <?php if (!empty($item->product_listprice_enabled)) : ?>
                            <div class="product_listprice">
                            <span class="title"><?php echo JText::_('COM_CITRUSCART_LIST_PRICE'); ?>:</span>
                            <del><?php echo CitruscartHelperBase::currency($item->product_listprice); ?></del>
                            </div>
                        <?php endif; ?>

                        <div class="product_price">
                        <?php
                        // For UE States, we should let the admin choose to show (+19% vat) and (link to the shipping rates)
                        $config = Citruscart::getInstance();
                        $show_tax = $config->get('display_prices_with_tax');

                        $article_link = $config->get('article_shipping', '');
                        $shipping_cost_link = JRoute::_('index.php?option=com_content&view=article&id='.$article_link);

                        if (!empty($show_tax))
                        {
                            Citruscart::load('CitruscartHelperUser', 'helpers.user');
                            $geozones = CitruscartHelperUser::getGeoZones( JFactory::getUser()->id );
                            if (empty($geozones))
                            {
                                // use the default
                                $table = JTable::getInstance('Geozones', 'CitruscartTable');
                                $table->load(array('geozone_id'=>Citruscart::getInstance()->get('default_tax_geozone')));
                                $geozones = array( $table );
                            }
                            $taxtotal = CitruscartHelperProduct::getTaxTotal($item->product_id, $geozones);
                            $tax = $taxtotal->tax_total;
                            if (!empty($tax))
                            {
                                if ($show_tax == '2')
                                {
                                    // sum
                                    echo CitruscartHelperBase::currency($item->price + $tax);
                                }
                                    else
                                {
                                    echo CitruscartHelperBase::currency($item->price);
                                    echo sprintf( JText::_('COM_CITRUSCART_INCLUDE_TAX'), CitruscartHelperBase::currency($tax));
                                }
                            }
                                else
                            {
                                echo CitruscartHelperBase::currency($item->price);
                            }
                        }
                           else
                        {
                            echo CitruscartHelperBase::currency($item->price);
                        }

                        if (Citruscart::getInstance()->get( 'display_prices_with_shipping') && !empty($item->product_ships))
                        {
                            echo '<br /><a href="'.$shipping_cost_link.'" target="_blank">'.sprintf( JText::_('COM_CITRUSCART_LINK_TO_SHIPPING_COST'), $shipping_cost_link).'</a>' ;
                        }
                        ?>
                        </div>
                    <?php endif; ?>

                    <?php // TODO Make this display the "quickAdd" layout in a lightbox ?>
                    <?php // $url = "index.php?option=com_citruscart&format=raw&controller=carts&task=addToCart&productid=".$item->product_id; ?>
                    <?php // $onclick = 'citruscartDoTask(\''.$url.'\', \'CitruscartUserShoppingCart\', \'\');' ?>
                    <?php // <img class="addcart" src="media/citruscart/images/addcart.png" alt="" onclick="<?php echo $onclick; " /> ?>
                </div>

                <div class="product_info">
                    <div class="product_name">
                        <span>
                            <a href="<?php echo JRoute::_($item->link."&filter_category=".$item->category_id."&Itemid=".$item->itemid ); ?>">
                            <?php echo $item->product_name; ?>
                            </a>
                        </span>
                    </div>

                    <div class="product_rating">
                       <?php echo CitruscartHelperProduct::getRatingImage( $item->product_rating ); ?>
                       <?php if (!empty($item->product_comments)) : ?>
                       <span class="product_comments_count">(<?php echo $item->product_comments; ?>)</span>
                       <?php endif; ?>
                    </div>

                    <?php if (!empty($item->product_model) || !empty($item->product_sku)) { ?>
                        <div class="product_numbers">
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

                    <div class="product_minidesc">
                    <?php
                        if (!empty($item->product_description_short))
                        {
                            echo $item->product_description_short;
                        }
                            else
                        {
                            $str = wordwrap($item->product_description, 200, '`|+');
                            $wrap_pos = strpos($str, '`|+');
                            if ($wrap_pos !== false) {
                                echo substr($str, 0, $wrap_pos).'...';
                            } else {
                                echo $str;
                            }
                        }
                    ?>
                    </div>
                </div>
            </div>

            <div class="reset"></div>

            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>

            <?php if (!count($items)) : ?>
            <div class="product_item">
                <?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
            </div>
            <?php endif; ?>
        </div>

        <div id="searchresults_footer" class="pagination">
            <div id="results_counter"><?php echo $this->pagination->getResultsCounter(); ?></div>
            <?php echo $this->pagination->getListFooter(); ?>
        </div>
    </div>

    <div class="reset"></div>

    <input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</form>

</div>