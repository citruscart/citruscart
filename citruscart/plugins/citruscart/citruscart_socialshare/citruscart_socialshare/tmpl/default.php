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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

$display_tw  = $this->params->get('twitter_share');
$display_fb  = $this->params->get('fb_share');
$display_gp = $this->params->get('gplus_share');
$display_pinterest  = $this->params->get('pinterest_share');
$display_linkedin  = $this->params->get('pinterest_share');
$doc = JFactory::getDocument();
$doc->addScript(JUri::root().'plugins/citruscart/citruscart_socialshare/citruscart_socialshare/media/js/socialshare.js');
$doc->addStyleSheet(JUri::root().'plugins/citruscart/citruscart_socialshare/citruscart_socialshare/media/css/socialshare.css');
?>

<?php if ( $display_fb || $display_tw || $display_gp  || $display_linkedin || $display_pinterest) : ?>
<div id="citruscart_shareButton">
	 <?php if ( $display_tw ) : ?>
		 	<button type="button" class="btn btn-default socialshare"  onclick="socialsharing_twitter_click('<?php echo Citruscart::getInstance( )->get( 'display_tweet_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>')">
			 <img src="<?php echo JUri::root().'plugins/citruscart/citruscart_socialshare/citruscart_socialshare/media/images/twittericon.png'?>" />
			 </button>

			<?php endif;?>

			<?php if ( $display_fb ) : ?>
				<button type="button" class="btn btn-default socialshare"  onclick="socialsharing_facebook_click('<?php echo Citruscart::getInstance( )->get( 'display_fb_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>')">
					<img src="<?php echo JUri::root().'plugins/citruscart/citruscart_socialshare/citruscart_socialshare/media/images/fbicon.png'?>"/>
				 </button>
			<?php endif;?>

			<?php if ( $display_gp ) : ?>
				<button type="button" class="btn btn-default socialshare" onclick="socialsharing_google_click('<?php echo Citruscart::getInstance( )->get( 'display_gplus_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>')">
					<img src="<?php echo JUri::root().'plugins/citruscart/citruscart_socialshare/citruscart_socialshare/media/images/gplusicon.png'?>"/>
				 </button>
			<?php endif;?>
			<?php if ( $display_pinterest ):?>
				<button type="button" class="btn btn-default socialshare" onclick="socialsharing_pinterest_click('<?php echo Citruscart::getInstance( )->get( 'display_pinterest_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>')">
					<img src="<?php echo JUri::root().'plugins/citruscart/citruscart_socialshare/citruscart_socialshare/media/images/pinicon.png'?>"/>
				 </button>
			<?php endif;?>
			<?php if ( $display_linkedin ) : ?>
				<button type="button" class="btn btn-default socialshare" onclick="socialsharing_linkedin_click('<?php echo Citruscart::getInstance( )->get( 'display_linkedin_message', 'Check this out!' ).' '.CitruscartHelperProduct::getSocialBookMarkUri(); ?>')">
					<img src="<?php echo JUri::root().'plugins/citruscart/citruscart_socialshare/citruscart_socialshare/media/images/inicon.png'?>"/>
				 </button>
			<?php endif; ?>
				<div class="reset"></div>
		</div>
<?php endif; ?>

