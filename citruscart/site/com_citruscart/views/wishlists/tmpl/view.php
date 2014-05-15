<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
	JHTML::_('stylesheet', 'menu.css', 'media/citruscart/css/');
	JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
	JHTML::_('script', 'joomla.javascript.js', 'includes/js/');
	Citruscart::load( 'CitruscartGrid', 'library.grid' );
	$items = $this->items;
	$state = $this->state;
	Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
	$router = new CitruscartHelperRoute();
	Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
	$menu = CitruscartMenu::getInstance( $this->submenu );
	$products_model = $this->getModel('products');
?>

<script type="text/javascript">
citruscartJQ(document).ready(function(){
	DisplaySharingOptions( <?php echo $this->row->privacy; ?>, 0 );

    citruscartJQ('.privatize-wishlist').on('change', function(){
        el = citruscartJQ(this);
        privacy = el.val();
        if (privacy > 0) {
            Citruscart.privatizeWishlist(<?php echo $this->row->wishlist_id; ?>, privacy, function(response){
                container = citruscartJQ('#message-container');
                container.find('.confirmation').remove();
                container.append('<p class="confirmation">'+response.html+'</p>').find('.confirmation').fadeIn().delay(1500).fadeOut().delay(3000);
            });
			DisplaySharingOptions( privacy, 'slow' );
        }
    });

    citruscartJQ('.delete-wishlistitem').on('click', function(){
        el = citruscartJQ(this);
        wishlistitem_id = el.attr('data-wishlistitem_id');
        if (wishlistitem_id) {
            Citruscart.deleteWishlistItem(wishlistitem_id, '<?php echo JText::_("COM_CITRUSCART_CONFIRM_DELETE_WISHLISTITEM"); ?>', function(){
                citruscartJQ('.wishlistitem-'+wishlistitem_id).remove();
            });
        }
    });

    citruscartJQ('.delete-wishlist').on('click', function(){
        el = citruscartJQ(this);
        Citruscart.deleteWishlist(<?php echo $this->row->wishlist_id; ?>, '<?php echo JText::_("COM_CITRUSCART_CONFIRM_DELETE_WISHLIST"); ?>', function(){
            window.location = '<?php echo JRoute::_('index.php?option=com_citruscart&view=wishlists&Itemid='.$router->findItemid( array('view'=>'wishlists') ) ); ?>';
        });
    });

    citruscartJQ('.rename-wishlist').on('click', function() {
        el = citruscartJQ(this);
        Citruscart.renameWishlist(<?php echo $this->row->wishlist_id; ?>, '<?php echo JText::_("COM_CITRUSCART_PROVIDE_WISHLIST_NAME"); ?>', function(response){
            citruscartJQ('.wishlist-name.wishlist-<?php echo $this->row->wishlist_id; ?>').html( response.wishlist_name );
        });
    });
});

function DisplaySharingOptions( privacy, t ) {
	var obj = citruscartJQ( '#wishlist-sharing' );
	console.log( privacy );
	if( privacy == 3 ) {
		obj.hide(t);
	} else {
		obj.show(t);
	}
}
</script>

<div id="message-container" class="dsc-wrap">
    <h2 class="dsc-wrap">
        <span class="wishlist-name wishlist-<?php echo $this->row->wishlist_id; ?>">
            <?php echo $this->row->wishlist_name; ?>
        </span>
        <a class="rename-wishlist" href="javascript:void(0);">
            <small>
            <?php echo JText::_( "COM_CITRUSCART_RENAME_WISHLIST" ); ?>
            </small>
        </a>

        <a class="delete-wishlist pull-right btn btn-danger indent-10" href="javascript:void(0);">
            <?php echo JText::_( "COM_CITRUSCART_DELETE_WISHLIST" ); ?>
        </a>

        <select name="wishlist-privacy-<?php echo $this->row->wishlist_id; ?>" id="wishlist-privacy-<?php echo $this->row->wishlist_id; ?>" class="privatize-wishlist pull-right input input-small">
            <option value="1" <?php if ($this->row->privacy == '1') { echo "selected='selected'"; } ?>><?php echo JText::_( "COM_CITRUSCART_PUBLIC" ); ?></option>
            <option value="2" <?php if ($this->row->privacy == '2') { echo "selected='selected'"; } ?>><?php echo JText::_( "COM_CITRUSCART_LINK_ONLY" ); ?></option>
            <option value="3" <?php if ($this->row->privacy == '3') { echo "selected='selected'"; } ?>><?php echo JText::_( "COM_CITRUSCART_PRIVATE" ); ?></option>
        </select>
    </h2>
</div>

<?php if( $menu ) { $menu->display(); } ?>

<div class="wishlist-items dsc-wrap">
    <?php if (!empty($items)) { ?>
    <form action="<?php echo JRoute::_('index.php?option=com_citruscart&view=wishlists&task=update&Itemid='.$router->findItemid( array('view'=>'wishlists') ) ); ?>" method="post" name="adminForm" enctype="multipart/form-data" class="dsc-wrap">

        <div class="dsc-wrap bottom-10">
            <a href="<?php echo JRoute::_('index.php?option=com_citruscart&view=wishlists&Itemid='.$router->findItemid( array('view'=>'wishlists') ) ); ?>">
                <?php echo JText::_( "COM_CITRUSCART_RETURN_TO_LIST" ); ?>
            </a>

            <a class="pull-right btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_citruscart&view=checkout&Itemid='.$this->checkout_itemid ); ?>" onclick="return CitruscartCheckUpdateCartQuantities(document.adminForm, '<?php echo JText::_('COM_CITRUSCART_CHECK_CART_UPDATE'); ?>');">
                <?php echo JText::_('COM_CITRUSCART_BEGIN_CHECKOUT'); ?>
            </a>
        </div>

        <table class="dsc-clear table item-grid">
            <thead>
                <tr>
                    <th>
                    </th>
                    <th colspan="2"></th>
                    <th><?php echo JText::_('COM_CITRUSCART_DATE_ADDED'); ?></th>
                    <th><?php echo JText::_('COM_CITRUSCART_STATUS'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $i=0; $k=0; ?>
            <?php foreach ($items as $item) : ?>

            	<?php
            		$params = new DSCParameter( trim($item->wishlistitem_params) );
            		$default_url = "index.php?option=com_citruscart&view=products&task=view&id=".$item->product_id;
            		$attributes = CitruscartHelperProduct::convertAttributesToArray( $item->product_id, $item->product_attributes );
            		for( $j = 0, $c = count( $attributes ); $j < $c; $j++ )
            		{
            			$default_url .= '&attribute_'.$attributes[$j][0].'='.$attributes[$j][1];
            		}
            		if ($itemid = $products_model->getItemid( $item->product_id ))
            		{
            		    $default_url .= "&Itemid=" . $itemid;
            		}
            		$link = $params->get('product_url', $default_url );
            		$link = JRoute::_($link);
            	?>

                <tr class="row<?php echo $k; ?> wishlistitem-<?php echo $item->wishlistitem_id; ?>">
                    <td>
                        <a class="delete-wishlistitem btn btn-danger" href="javascript:void(0);" data-wishlistitem_id="<?php echo $item->wishlistitem_id; ?>">
                            <?php echo JText::_( "COM_CITRUSCART_DELETE_WISHLISTITEM" ); ?>
                        </a>
                    </td>
                    <td class="product_thumb_container">
                        <?php $product_image = CitruscartHelperProduct::getImage($item->product_id, '', '', 'full', true, false, array(), true ); ?>
                        <?php if ($product_image) { ?>
                        <div class="dsc-wrap product_thumb frame">
                            <div class="frame-inner">
                                <a href="<?php echo $link; ?>">
                	            <img src="<?php echo $product_image; ?>" />
                	            </a>
                            </div>
                        </div>
                        <?php } ?>
                    </td>
                    <td class="wishlist-column-product">
                        <a href="<?php echo $link; ?>">
                            <?php echo $item->product_name; ?>
                        </a>
                        <br/>

                        <?php if (!empty($item->attributes_names)) : ?>
	                        <?php echo $item->attributes_names; ?>
	                        <br/>
	                    <?php endif; ?>

                        <?php if ($item->product_recurs) { ?>
                            <?php echo JText::_('COM_CITRUSCART_RECURRING_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_price); ?>
                            (<?php echo $item->recurring_payments . " " . JText::_('COM_CITRUSCART_PAYMENTS'); ?>, <?php echo $item->recurring_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIODS'); ?>)

				            <?php if( $item->subscription_prorated ) { ?>
                                <br/>
                                <?php echo JText::_('COM_CITRUSCART_INITIAL_PERIOD_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_trial_price); ?>
                                (<?php echo "1 " . JText::_('COM_CITRUSCART_PAYMENT'); ?>, <?php echo $item->recurring_trial_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIOD'); ?>)
				            <?php } else { ?>
	                            <?php if ($item->recurring_trial) { ?>
   	                                <br/>
                                    <?php echo JText::_('COM_CITRUSCART_TRIAL_PERIOD_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->recurring_trial_price); ?>
                                    (<?php echo "1 " . JText::_('COM_CITRUSCART_PAYMENT'); ?>, <?php echo $item->recurring_trial_period_interval." ". JText::_('COM_CITRUSCART_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_CITRUSCART_PERIOD'); ?>)
				                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php echo JText::_('COM_CITRUSCART_PRICE'); ?>: <?php echo CitruscartHelperBase::currency($item->product_price); ?>
                        <?php } ?>

                        <br/> <?php echo CitruscartHelperProduct::getRatingImage( $item->product_rating ); ?>  <br/>

					    <?php if (!empty($this->onDisplayCartItem) && (!empty($this->onDisplayCartItem[$i]))) : ?>
					        <div class='onDisplayCartItem_wrapper_<?php echo $i?>'>
					        <?php echo $this->onDisplayCartItem[$i]; ?>
					        </div>
					    <?php endif; ?>

                    </td>
                    <td class="wishlist-column-date">
                        <?php echo JHTML::_( 'date', $item->last_updated, 'M d' ); ?>
                    </td>
                    <td class="wishlist-column-status">
                        <span class="<?php if (empty($item->available)) { echo "wishlist_item_unavailable"; } else { echo "wishlist_item_available"; } ?>">
                        <?php if (empty($item->available)) {
                            echo JText::_('COM_CITRUSCART_WISHLIST_UNAVAILABLE');
                        } else {
                            echo JText::_('COM_CITRUSCART_WISHLIST_AVAILABLE');
                            ?>
                            <div>
                                <a class="btn btn-success add-to-cart" href="<?php echo JRoute::_("index.php?option=com_citruscart&view=wishlists&task=update&addtocart=1&cid[]=" . $item->wishlistitem_id ); ?>"><?php echo JText::_("COM_CITRUSCART_ADD_TO_CART"); ?></a>
                            </div>
                            <?php
                        } ?>
                        </span>
                    </td>
                </tr>
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
            </tbody>
	            <tfoot id="wishlist-sharing">
	                <tr>
	                    <td colspan="20" style="text-align: left;">
	                        <a href="<?php echo JRoute::_('index.php?option=com_citruscart&view=wishlists&task=share&tmpl=component&cid[]='.$this->row->wishlist_id ); ?>" class="pull-left modal btn" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><?php echo JText::_('COM_CITRUSCART_SHARE'); ?></a>
							<?php
								$display_fb = $this->defines->get( 'display_facebook_like', '1' );
								$display_tw = $this->defines->get( 'display_tweet', '1' );
								$display_gp = $this->defines->get( 'display_google_plus1', '1' );

								if( $display_fb || $display_gp || $display_tw ) : ?>
							<div class="product_share_buttons_wishlist pull-left" >
							<?php
								endif;
								if ( $display_fb ) : ?>
								<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
								<fb:like show_faces="false" width="375"></fb:like>
							<?php endif;

								if ( $display_tw ) : ?>
								<a href="http://twitter.com/share" class="twitter-share-button" data-text="<?php echo Citruscart::getInstance( )->get( 'display_tweet_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>" data-count="horizontal">Tweet</a>
								<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
							<?php endif;

								if ( $display_gp ) :
									$google_plus1_size = Citruscart::getInstance( )->get( 'display_google_plus1_size', 'medium' ); ?>
								<g:plusone <?php if( strlen( $google_plus1_size ) ) echo 'size="'.$google_plus1_size.'"' ?>></g:plusone>
								<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
							<?php endif;
								if( $display_fb || $display_gp || $display_tw ) : ?>
							</div>
							<?php endif; ?>

	                    </td>
	                </tr>
	            </tfoot>
        </table>

        <?php if (!empty($this->pagination) && method_exists($this->pagination, 'getResultsCounter')) { ?>
        <form action="<?php echo JRoute::_( $form['action']."&limitstart=".$state->limitstart )?>" method="post" name="adminForm" enctype="multipart/form-data">
        <div id="pagination-footer" class="pagination">
            <div id="results_counter"><?php echo $this->pagination->getResultsCounter(); ?></div>
                <?php

                    $html = "<div class=\"list-footer\">\n";
                    $html .= $this->pagination->getPagesLinks();
                    $html .= "\n<div class=\"counter\">" . $this->pagination->getPagesCounter() . "</div>";
                    $html .= "\n<input type=\"hidden\" name=\"limitstart\" value=\"". $this->pagination->limitstart ."\" />";
                    $html .= "\n</div>";

                    echo $html;
                ?>
        </div>
        <?php echo $this->form['validate']; ?>
        </form>
        <?php } ?>

        <div style="clear: both;"></div>

        <input type="hidden" name="boxchecked" value="" />
        <?php echo $this->form['validate']; ?>

    </form>
    <?php } else { ?>
        <p><?php echo JText::_('COM_CITRUSCART_NO_ITEMS_IN_WISHLIST'); ?></p>
    <?php } ?>
</div>