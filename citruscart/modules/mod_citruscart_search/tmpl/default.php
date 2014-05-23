<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
// for some reason adding the models from the front end makes it so it will load the select list something is wierd
JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_citruscart/models' );
Citruscart::load( 'CitruscartSelect', 'library.select' );

?>
<style>
	.search_submit,
	.search_submit:hover
	{
		background-color:#2CB1B1;
		color:#ffffff;
	}
</style>
<div id="productSearch">
    <form class="form" action="<?php echo JRoute::_( 'index.php', false); ?>" method="post" id="siteForm" name="productSearch" onSubmit="if(this.elements['filter'].value == '<?php echo JText::_('COM_CITRUSCART_SKU_MODEL_OR_KEYWORD'); ?>') this.elements['filter'].value = '';">
       <div class="form-group">
       	<label for="searchInput">
        	<?php echo JText::_('COM_CITRUSCART_SEARCH').': '; ?>
        </label>
        <?php  if ($category_filter != '0') : ?>
            <?php echo CitruscartSelect::category($category_filter, 'filter_category', array('id' =>'citruscart_product_search','class'=>'form-control input-xs'), '', false, false, 'All Categories', '', '1'); ?>
        <?php else: ?>
            <input type="hidden" name="filter_category" value="1" />
        <?php endif; ?>
		</div>
		<div class="form-group">
			<label>
       			<input placeholder="<?php echo JText::_('MOD_CITRUSCART_SEARCH_SEARCH_PLACHOLDER');?>" type="text" class="form-control input-sm" name="filter" value="<?php echo JText::_( $filter_text ); ?>" onclick="this.value='';"/>
       		</label>
       		<input class="btn btn-default search_submit pull-right" type="submit" value="<?php echo JText::_('COM_CITRUSCART_SUBMIT'); ?>" />
       	</div>
        <input type="hidden" name="option" value="com_citruscart" />
        <input type="hidden" name="view" value="products" />
        <input type="hidden" name="task" value="search" />
        <input type="hidden" name="search" value="1" />
        <input type="hidden" name="search_type" value="<?php echo (int) $params->get('filter_fields'); ?>" />
        <input type="hidden" name="Itemid" value="<?php echo $item_id; ?>" />
        <input type="hidden" id="filter_cat" name="filter_category" value="" />

    </form>
 </div>
<script type="text/javascript">
var catvalue =0;

jQuery("#citruscart_product_search").change(function(){
	jQuery("#filter_cat").val(this.value);
	catvalue = this.value;
	jQuery("#siteForm").submit();
});

</script>