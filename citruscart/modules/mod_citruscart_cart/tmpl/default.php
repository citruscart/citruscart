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

<script type="text/javascript">
$(document).ready(function(){
   $('.modal').modal({ backdrop: 'static', keyboard: false })
});
</script>

<div class="buycart">
<?php
$src = JUri::root()."modules/mod_citruscart_cart/media/images/citruscart_cart.png";
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

    	//$html .= '<a id="cartLink" href="'.JRoute::_("index.php?option=com_citruscart&view=carts").'">'.'<img src="'.$src.'" >'.'</a>';
    	
    	if(!empty($items)) {             
			$html .= '<a id="cartLink" href="'.JRoute::_("index.php?option=com_citruscart&view=carts").'">'.'<img src="'.$src.'" >'.'</a>';
 	    } else  {
		    $html.= '<a id="cartLink" href="#" data-toggle="modal" data-target="#showEmpty" data-backdrop="static">'.'<img src="'.$src.'" >'.'</a>';
		}

    }
    $html .= '</span>';
    $html .= '</span>';
   
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

<!-- empty cart div starts -->
<div class="modal fade showemptycart" id="showEmpty">
	 
	 <!-- product modal div starts -->
	 <div class="popluar-products-modal">
					
		<!-- modal header div starts -->
		<div class="modal-header">	
			<a href="#" style="float:right;" data-dismiss="modal">X</a>		
			<h4 class="emptycarttitle"><?php echo JText::_('MOD_CITRUSCART_CART');?></h4>									
		</div><!-- modal header div ends -->
							
		<!-- modal body div starts -->
		<div class="modal-body">
			
			<div id="shows">
				<h5><?php echo JText::_('MOD_CITRUSCART_EMPTY_CART'); ?></h5>		
			</div>
															
		</div><!-- modal body div ends -->				

     </div><!-- product modal div ends -->

</div><!-- empty cart div ends --> 

</div>
