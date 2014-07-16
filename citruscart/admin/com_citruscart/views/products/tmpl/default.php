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
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();
$doc = JFactory::getDocument();
?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items;
?>
<?php Citruscart::load( 'CitruscartUrl', 'library.url' ); ?>
<?php $helper_category = CitruscartHelperBase::getInstance( 'Category' ); ?>
<?php $helper_product = CitruscartHelperBase::getInstance( 'Product' ); ?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<?php echo CitruscartGrid::pagetooltip($app->input->getString('view') ); ?>

	<!--  Here i changed the ul class  -->
	<?php echo CitruscartGrid::searchform($state->filter,JText::_('COM_CITRUSCART_SEARCH'), JText::_('COM_CITRUSCART_RESET'),$class = "inline" ) ?>

	<table class="table table-bordered " style="clear: both;">
		<thead>
            <tr>
                <th style="width: 5px;">
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="width: 20px;">
                	<?php echo JHtmlGrid::checkall($name = 'cid', $tip = 'JGLOBAL_CHECK_ALL', $action = 'Joomla.checkAll(this)')?>
                </th>
                <th style="width: 50px;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.product_id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;" colspan="2">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.product_name", $state->direction, $state->order ); ?>
                	+
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_RATING', "tbl.product_rating", $state->direction, $state->order ); ?>
                	+
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_REVIEWS', "tbl.product_comments", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 70px;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_SKU', "tbl.product_sku", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 50px;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PRICE', "price", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_QUANTITY', "product_quantity", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ORDER', "tbl.ordering", $state->direction, $state->order ); ?>
                    <?php echo JHTML::_('grid.order', $items ); ?>
                </th>
                <th style="width: 100px;">
    	            <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ENABLED', "tbl.product_enabled", $state->direction, $state->order ); ?>
                </th>
            </tr>
            <tr class="filterline">
                <th colspan="3">
                	<?php  //$attribs = array('class' => 'inputbox', 'onchange' => 'document.adminForm.submit();');
                	$attribs = array('class' => 'input', 'onchange' => 'document.adminForm.submit();'); ?>

                	<div class="range">
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_FROM'); ?>" id="filter_id_from" name="filter_id_from" value="<?php echo $state->filter_id_from; ?>" size="5" class="input input-small" />
                        </div>
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_TO'); ?>" id="filter_id_to" name="filter_id_to" value="<?php echo $state->filter_id_to; ?>" size="5" class="input input-small" />
                        </div>
                    </div>


                </th>
                <th style="text-align: left;" colspan="2">
                	<input class="input" id="filter_name" name="filter_name" placeholder="Product Name..." type="text" value="<?php echo $state->filter_name; ?>" size="25"/>
                	<?php echo CitruscartSelect::category( $state->filter_category, 'filter_category', $attribs, 'category', true ); ?>
                	<?php echo CitruscartSelect::booleans( $state->filter_ships, 'filter_ships', $attribs, 'ships', true, 'Requires Shipping', 'Yes', 'No' ); ?>
                	<?php echo CitruscartSelect::taxclass( $state->filter_taxclass, 'filter_taxclass', $attribs, 'taxclass', true, false ); ?>
                </th>
                <th>
                	<input id="filter_sku"  name="filter_sku" type="text" value="<?php echo $state->filter_sku; ?>" class="input" placeholder="<?php echo JText::_('COM_CITRUSCART_SKU'); ?>"/>
                </th>
                <th>
                	<div class="range">
	                	<div class="rangeline">
	                		<input  type="text"id="filter_price_from" name="filter_price_from" value="<?php echo $state->filter_price_from; ?>" size="5" class="input input-mini" placeholder="<?php echo JText::_("COM_CITRUSCART_FROM"); ?>"/>
	                	</div>
	                	<div class="rangeline">
	                		<input type="text" id="filter_price_to" name="filter_price_to" value="<?php echo $state->filter_price_to; ?>" size="5" class="input input-mini" placeholder="<?php echo JText::_("COM_CITRUSCART_TO"); ?>"/>
	                	</div>
                	</div>
                </th>
                <th>
                	<div class="range">
	                	<div class="rangeline">
	                		<input type="text" id="filter_quantity_from" name="filter_quantity_from" value="<?php echo $state->filter_quantity_from; ?>" size="5" class="input input-mini"  placeholder="<?php echo JText::_("COM_CITRUSCART_FROM"); ?>"/>
	                	</div>
	                	<div class="rangeline">
	                		<input type="text" id="filter_quantity_to" name="filter_quantity_to" value="<?php echo $state->filter_quantity_to; ?>" size="5" class="input input-mini" placeholder="<?php echo JText::_("COM_CITRUSCART_TO"); ?>"/>
	                	</div>
                	</div>
                </th>
                <th>
                </th>
                <th>
    	            <?php
    	            $attribs1 = array('class' => 'input-small', 'onchange' => 'document.adminForm.submit();');
    	            echo CitruscartSelect::booleans( $state->filter_enabled, 'filter_enabled', $attribs1, 'enabled', true, 'COM_CITRUSCART_ENABLED_STATE' ); ?>
                </th>
            </tr>
			<tr>
				<th colspan="20" style="font-weight: normal;">
					<div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
					<div style="float: left;"><?php echo $this->pagination->getListFooter(); ?></div>
				</th>
			</tr>
		</thead>
	</table>
	<table class="table table-striped table-bordered ">
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach ($items as $a=>$item) : ?>
            <tr class='row<?php echo $a%2; ?>'>
				<td align="center">
					<?php echo $i + 1; ?>
				</td>
				<td style="text-align: center;">
					<?php echo CitruscartGrid::checkedout( $item, $i, 'product_id' ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link_edit; ?>">
						<?php echo $item->product_id; ?>
					</a>
				</td>
				<td style="text-align: center; width: 50px;">
                    <?php echo $helper_product->getImage($item->product_id, 'id', $item->product_name, 'full', false, false, array( 'width'=>48 ) ); ?>
				</td>
				<!-- -- -->
				<td style="text-align: left;">
					<a href="<?php echo $item->link_edit; ?>">
						<?php echo JText::_($item->product_name); ?>
					</a>

					<div class="product_rating">
					   <?php echo $helper_product->getRatingImage( $item->product_rating, $this ); ?>
					   <?php if (!empty($item->product_comments)) : ?>
					   <span class="product_comments_count">(<?php echo $item->product_comments; ?>)</span>
					   <?php endif; ?>
					</div>

					<div class="product_categories">
						<span style="float: right;">[<?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&controller=products&task=selectcategories&id=".$item->product_id."&tmpl=component", JText::_('COM_CITRUSCART_SELECT_CATEGORIES'), array('update' => true) ); ?>]</span>
						<?php $categories = $helper_product->getCategories( $item->product_id );
						?>
						<?php for ($n='0'; $n<count($categories) && $n<'1'; $n++) : ?>
							<?php $category = $categories[$n]; ?>
							<?php echo $helper_category->getPathName( $category ); ?>
							<br/>
						<?php endfor; ?>
						<?php if (count($categories) > $n) { echo sprintf( JText::_('COM_CITRUSCART_AND_X_MORE'), count($categories) - $n ); } ?>
					</div>

                    <div class="product_images_path">
                        <b><?php echo JText::_('COM_CITRUSCART_IMAGE_GALLERY_PATH'); ?>:</b> <?php echo str_replace( JPATH_SITE, '', $helper_product->getGalleryPath( $item->product_id ) ); ?>
                    </div>

                    <?php

                    $layout = $helper_product->getLayout( $item->product_id );
                    if ($layout != 'view')
                    {
                        echo "<b>".JText::_('COM_CITRUSCART_LAYOUT_OVERRIDE')."</b>: ".$layout;
                    }
                    ?>
                </td>
                <td style="text-align: center;">
					<span class="badge badge-success"><?php echo $item->product_sku; ?></span>
				</td>
				<td style="text-align: right;">
					<span class="badge btn-danger"><?php echo CitruscartHelperBase::currency($item->price); ?></span>
					<br/>
					[<?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&controller=products&task=setprices&id=".$item->product_id."&tmpl=component", JText::_('COM_CITRUSCART_SET_PRICES'), array('update' => true) ); ?>]
				</td>
				<td style="text-align: center;">

					<?php
					if(!isset($item->product_check_inventory)){
						echo JText::_('COM_CITRUSCART_CHECK_PRODUCT_INVENTORY_DISABLED');
					} else {
					?>	<span class="badge badge-warning"><?php echo (int) $item->product_quantity; ?></span>
                    <br/>
                    [<?php echo CitruscartUrl::popup( "index.php?option=com_citruscart&controller=products&task=setquantities&id=".$item->product_id."&tmpl=component", JText::_('COM_CITRUSCART_SET_QUANTITIES'), array('update' => true) ); ?>]

                    <?php } ?>
				</td>
                <td style="text-align: center;">
                    <?php echo CitruscartGrid::order($item->product_id); ?>
                    <?php echo CitruscartGrid::ordering($item->product_id, $item->ordering ); ?>
                </td>
				<td style="text-align: center;">
					<?php  echo CitruscartGrid::enable($item->product_enabled, $i, 'product_enabled.' ); ?>
				</td>
			</tr>
			<?php ++$i; $k = (1 - $k); ?>
			<?php endforeach; ?>

			<?php if (!count($items)) : ?>
			<tr>
				<td colspan="10" align="center">
					<?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
			<tfoot>
			<tr>
				<td colspan="20">
					<div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
					   <div class="pagination pagination-toolbar">
                    	<?php echo $this->pagination->getPagesLinks(); ?>
                	</div>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</form>