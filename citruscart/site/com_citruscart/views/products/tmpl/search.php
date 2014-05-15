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

JHTML::_('stylesheet', 'menu.css', 'media/citruscart/css/');
JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
$state = $this->state;
$items = $this->items;
?>

<div class='categoryheading'>
<?php echo JText::_('COM_CITRUSCART_SEARCH_RESULTS_FOR').': '.$state->filter; ?>
</div>

<form action="<?php echo JRoute::_( $form['action']."&limitstart=".$state->limitstart )?>" method="post" name="adminForm" enctype="multipart/form-data">

    <table class="product">
      <tfoot>
        <tr>
            <td colspan="20">
                <div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
      </tfoot>
      <tbody>
<?php
if (empty($items)) {
    echo JText::_('COM_CITRUSCART_NO_MATCHING_ITEMS_FOUND');
} else {
    foreach ($items as $item) {
?>
        <tr class="productitem">
            <td class="productthumb">
                <div class="productbuy">
                    <a href="<?php echo JRoute::_( $item->link ); ?>">
                    <?php echo CitruscartHelperProduct::getImage($item->product_id); ?>
                    </a>
                    <p class="price"><?php echo CitruscartHelperBase::currency($item->price); ?></p>
                </div>
            </td>
            <td class="productinfo">
                <span class="productname"><a href="<?php echo JRoute::_($item->link); ?>"><?php echo $item->product_name; ?></a></span><br />
                <span class="productnums">
                <?php
                    $sep = '';
                    if (!empty($item->product_model)) {
                        echo '<b>'.JText::_('COM_CITRUSCART_MODEL').":</b> $item->product_model";
                        $sep = "&nbsp;&nbsp;";
                    }
                    if (!empty($item->product_sku)) {
                        echo "$sep <b>".JText::_('COM_CITRUSCART_SKU').":</b> $item->product_sku";
                    }
                ?>
                </span><br />
                <span class="productrating"><!-- <img src="media/citruscart/images/ratings_star_4_1.gif" alt="" /> --></span><br />
                <span class="productminidesc"><?php $str = wordwrap($item->product_description, 200, '`|+'); echo substr($str, 0, stripos($str, '`|+')).'...'; ?></span>
            </td>
        </tr>

<?php
    }
}
?>
      </tbody>
    </table>
<?php echo $this->form['validate']; ?>
</form>