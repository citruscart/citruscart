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
<?php JHTML::_( 'script', 'site.js', 'media/citruscart/js/' ); ?>

<?php if (!empty($items)) { ?>
<script type="text/javascript">
citruscartJQ(document).ready(function(){
    Citruscart.setupPaoFilters();
});
</script>

<div class="citruscart-paofilters-wrapper dsc-wrap">

    <ul class="citruscart-paofilters dsc-wrap">
        <?php foreach ($items as $item) { ?>
            <li class="citruscart-paofilter dsc-wrap" data-name="<?php echo $item->productattribute_name; ?>">
                <span><?php echo $item->productattribute_name; ?></span>
                <?php
                $data_group = "option-" . JFilterOutput::stringURLSafe($item->productattribute_name);
                if (!empty($optionnames[$data_group])) { ?>
                    <ul>
                    <?php foreach ($optionnames[$data_group] as $selected_option) { ?>
                        <li>
                            <?php echo $selected_option; ?>
                        </li>
                    <?php } ?>
                    </ul>
                <?php } ?>
            </li>
        <?php } ?>

        <?php if (!empty($show_reset)) { ?>
        <li class="citruscart-reset-filters dsc-wrap">
            <a href="<?php echo JRoute::_("index.php?reset=1"); ?>"><?php echo JText::_('COM_CITRUSCART_REMOVE_FILTERS') ?></a>
        </li>
        <?php } ?>

    </ul>

    <div class="citruscart-paofilter-options-container dsc-wrap">
        <?php foreach ($items as $item) { ?>
            <div data-name="<?php echo $item->productattribute_name; ?>" class="citruscart-paofilter-options-wrapper option-<?php echo JFilterOutput::stringURLSafe($item->productattribute_name); ?> dsc-wrap">
                <ul class="citruscart-paofilter-options dsc-wrap">
                    <?php foreach ($item->productattribute_options as $option) {
                        $data_group = "option-" . JFilterOutput::stringURLSafe($item->productattribute_name);
                        ?>
                        <li class="<?php if (!empty($filter_pao_id_groups[$data_group]) && array_intersect($filter_pao_id_groups[$data_group], $option->productattributeoption_ids)) { echo "selected"; } ?> Citruscart-paofilter-option option-<?php echo JFilterOutput::stringURLSafe($option->productattributeoption_name); ?> dsc-wrap" data-ids='<?php echo json_encode($option->productattributeoption_ids); ?>' data-group="option-<?php echo JFilterOutput::stringURLSafe($item->productattribute_name); ?>">
                            <div>
                                <?php echo $option->productattributeoption_name; ?>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
                <ul class="citruscart-paofilter-buttons dsc-wrap">
                    <li class="show-all"><a data-group="option-<?php echo JFilterOutput::stringURLSafe($item->productattribute_name); ?>"><?php echo JText::_( "COM_CITRUSCART_SHOW_ALL" ); ?></a></li>
                    <li class="go"><a class="btn"><?php echo JText::_( "COM_CITRUSCART_GO" ); ?></a></li>
                </ul>
            </div>
        <?php } ?>
    </div>

    <form action="<?php echo JRoute::_( "index.php?Itemid=" . $itemid ); ?>" method="post" id="paofilters-form" enctype="multipart/form-data">
        <?php if (!empty($filter_pao_id_groups)) { ?>
            <?php foreach ($filter_pao_id_groups as $group=>$filter_pao_ids) { ?>
                <?php foreach ($filter_pao_ids as $filter_pao_id) { ?>
                    <?php if (!empty($filter_pao_id)) { ?>
                        <input id="filter_pao_id-<?php echo $filter_pao_id; ?>" name="filter_pao_id_groups[<?php echo $group; ?>][]" value="<?php echo $filter_pao_id; ?>" type="hidden" class="filter_pao_id" />
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </form>

</div>
<?php } ?>