<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
$display_fb = Citruscart::getInstance( )->get( 'display_facebook_like', '1' );
$display_tw = Citruscart::getInstance( )->get( 'display_tweet', '1' );
$display_gp = Citruscart::getInstance( )->get( 'display_google_plus1', '1' );
?>

<?php if ( $display_fb || $display_tw || $display_gp ) : ?>
<div class="product_like">
	<?php if ( $display_fb ) : ?>
	<div class="product_facebook_like">
		<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
		<fb:like show_faces="false" width="375"></fb:like>
	</div>
	<?php endif; ?>

	<div class="product_share_buttons">
	<?php if ( $display_tw ) : ?>
		<div class="product_tweet">
			<a href="http://twitter.com/share" class="twitter-share-button" data-text="<?php echo Citruscart::getInstance( )->get( 'display_tweet_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>" data-count="horizontal">Tweet</a>
			<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		</div>
	<?php endif; ?>

	<?php if ( $display_gp ) : ?>
	<?php $google_plus1_size = Citruscart::getInstance( )->get( 'display_google_plus1_size', 'medium' ); ?>
		<div class="product_google_plus1">
			<g:plusone <?php if( strlen( $google_plus1_size ) ) echo 'size="'.$google_plus1_size.'"' ?>></g:plusone>
			<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
		</div>
	<?php endif; ?>
	</div>
<div class="reset"></div>
</div>
<?php endif; ?>

