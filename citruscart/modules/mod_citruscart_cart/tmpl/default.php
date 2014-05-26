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

// Add CSS
$document->addStyleSheet( JURI::root(true).'/modules/mod_citruscart_cart/tmpl/citruscart_cart.css');
?>

<div class="buycart">
<?php
$src = JUri::root().'/modules/mod_citruscart_cart/media/images/citruscart_cart.png';
?>

<?php
$html = ($ajax) ? '' : '<div id="citruscartUserShoppingCart" class="pull-right">';

    $html .= '<span class="CartItems">';
    if ($num > 0)
    {
        $qty = 0;
        foreach ($items as $item)
        {
            $qty = $qty + $item->orderitem_quantity;
        }
        $html .= '<span class="qty">'.$qty.'</span> ';
        $html .= ($qty == 1) ? JText::_('COM_CITRUSCART_ITEM') : JText::_('COM_CITRUSCART_ITEMS');
    }
       elseif ($display_null == '1')
    {
        $text = JText::_( $null_text );
        $html .= $text;
    }

   //$html .= '<span class="CartTotal">'.JText::_('COM_CITRUSCART_TOTAL').':<span>'.CitruscartHelperBase::currency($orderTable->order_total).'</span> '.'</span> ';
    //$html .= '<span class="CartView">';
    if ($params->get('display_lightbox') == '1')
    {
        $lightbox_attribs = array(); $lightbox['update'] = false; if ($lightbox_width = Citruscart::getInstance()->get( 'lightbox_width' )) { $lightbox_attribs['width'] = $lightbox_width; };
       //$html .= CitruscartUrl::popup( "index.php?option=com_citruscart&view=carts&task=confirmAdd&tmpl=component", JText::_('COM_CITRUSCART_VIEW_YOUR_CART'), $lightbox_attribs );
    }
        else
    {
        //$html .= '<a id="cartLink" href="'.JRoute::_("index.php?option=com_citruscart&view=carts").'">'.JText::_('COM_CITRUSCART_VIEW_YOUR_CART').'</a>';

    	$html .= '<a id="cartLink" href="'.JRoute::_("index.php?option=com_citruscart&view=carts").'">'.'<img src="' . $src .' " >'.'</a>';

    }
    $html .= '</span>';
    $html .= '</span>';
    //$html .= '<span class="CartCheckout">'.'<a id="checkoutLink" href="'.JRoute::_("index.php?option=com_citruscart&view=checkout").'">'.JText::_('COM_CITRUSCART_CHECKOUT').'</a>'.'</span>';
    //$html .= '<div class="reset"></div>';

    if ($ajax)
    {
        $mainframe->setUserState('mod_usercart.isAjax', '0');
    }
       else
    {
        $html .= '</div>';
    }

echo $html;
?>
</div>
