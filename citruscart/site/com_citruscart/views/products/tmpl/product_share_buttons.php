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
JHtml::_('script','media/citruscart/js/socialsharing.js');
?>

<?php if ( $display_fb || $display_tw || $display_gp ) : ?>
<div>

		<!-- social buttons unorder list starts -->

		 <?php if ( $display_tw ) : ?>
		 	<button type="button" class="btn btn-default"  onclick="socialsharing_twitter_click('<?php echo Citruscart::getInstance( )->get( 'display_tweet_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>')">
			 <img src="<?php echo JUri::root().'/media/citruscart/images/twittericon.png'?>"/>
			 </button>

			<?php endif;?>
				<?php if ( $display_fb ) : ?>

				<button type="button" class="btn btn-default "  onclick="socialsharing_facebook_click('<?php echo Citruscart::getInstance( )->get( 'display_tweet_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>')">
					<img src="<?php echo JUri::root().'/media/citruscart/images/fbicon.png'?>"/>
				 </button>

			<?php endif;?>

			<?php if ( $display_gp ) : ?>

				<button type="button" class="btn btn-default" onclick="socialsharing_google_click('<?php echo Citruscart::getInstance( )->get( 'display_tweet_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>')">
					<img src="<?php echo JUri::root().'/media/citruscart/images/gplusicon.png'?>"/>
				 </button>
			<?php endif;?>
				<button type="button" class="btn btn-default" onclick="socialsharing_pinterest_click('<?php echo Citruscart::getInstance( )->get( 'display_tweet_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>')">
					<img src="<?php echo JUri::root().'/media/citruscart/images/pinicon.png'?>"/>
				 </button>
				<button type="button" class="btn btn-default" onclick="socialsharing_linkedin_click('<?php echo Citruscart::getInstance( )->get( 'display_tweet_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>')">
					<img src="<?php echo JUri::root().'/media/citruscart/images/inicon.png'?>"/>
				 </button>

				<div class="reset"></div>
		</div>
<?php endif; ?>

