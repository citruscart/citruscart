<?php 
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

	defined('_JEXEC') or die('Restricted access'); 
	$min_length = Citruscart::getInstance()->get( 'password_min_length', 5 );
	$req_num = Citruscart::getInstance()->get( 'password_req_num', 1 );
	$req_alpha = Citruscart::getInstance()->get( 'password_req_alpha', 1 );
	$req_spec = Citruscart::getInstance()->get( 'password_req_spec', 1 );
	Citruscart::load('CitruscartHelperImage', 'helpers.image');
	$image = CitruscartHelperImage::getLocalizedName("help_tooltip.png", Citruscart::getPath('images'));
	
$js_strings = array( 'COM_CITRUSCART_PASSWORD_VALID', 'COM_CITRUSCART_PASSWORD_INVALID', 'COM_CITRUSCART_PASSWORD_DO_NOT_MATCH', 'COM_CITRUSCART_PASSWORD_MATCH', 'COM_CITRUSCART_SUCCESS', 'COM_CITRUSCART_ERROR', 'COM_CITRUSCART_PASSWORD_MIN_LENGTH', 'COM_CITRUSCART_PASSWORD_REQ_ALPHA', 'COM_CITRUSCART_PASSWORD_REQ_NUMBER', 'COM_CITRUSCART_PASSWORD_REQ_SPEC' );
CitruscartHelperImage::addJsTranslationStrings( $js_strings );
?>

<div style="clear: both;width:100%;">
	<div class="form_item">
		<div class="form_key">
			<?php echo JText::_('COM_CITRUSCART_EMAIL').': '.CitruscartGrid::required(); ?>
		</div>
		<div class="form_input">
			<!--   Email Address   --> 
			<input id="email_address" name="email_address" type="text" onchange="CitruscartCheckoutCheckEmail( 'message-email', this.form );" class="inputbox_required" size="30" maxlength="250" value="" />			
		</div>
		<div class="form_message" id="message-email"></div>
	</div>
	<div class="form_item">
		<div class="form_key"> 
			<?php echo JText::_('COM_CITRUSCART_NAME').': '.CitruscartGrid::required(); ?>
		</div>
		<div class="form_input">
			<!--   Name   -->
			<input id="name"  name="name" type="text" size="30" value="" class="inputbox_required" maxlength="250" />			
		</div>
	</div>
	<div class="form_item">
		<div class="form_key">
			<?php echo JText::_('COM_CITRUSCART_USERNAME').': '.CitruscartGrid::required(); ?>
		</div>
		<div class="form_input">
			<!--   Username   -->
			<input id="username" name="username" type="text" class="inputbox_required" size="30"	value="" maxlength="25" />			
		</div>
		<div class="form_message" id="message-username"></div>
	</div>
	<div class="form_item">
		<div class="form_key">
			<?php echo JText::_('COM_CITRUSCART_PASSWORD').': '.CitruscartGrid::required(); ?>
				<a class="img_tooltip" href="" > 
					<img src="<?php echo Citruscart::getUrl('images').$image; ?>" alt='<?php echo JText::_('COM_CITRUSCART_HELP'); ?>' />
					<span>
						<?php echo JText::_('COM_CITRUSCART_PASSWORD_REQUIREMENTS'); ?>: <br />
						<?php 
							echo '- '.JText::sprintf( "COM_CITRUSCART_PASSWORD_MIN_LENGTH", $min_length ).'<br />';
							if( $req_num )
								echo '- '.JText::_('COM_CITRUSCART_PASSWORD_REQ_NUMBER').'<br />';
							if( $req_alpha )
								echo '- '.JText::_('COM_CITRUSCART_PASSWORD_REQ_ALPHA').'<br />';
							if( $req_spec )
								echo '- '.JText::_('COM_CITRUSCART_PASSWORD_REQ_SPEC').'<br />';
						?>
					</span>
				</a>
		</div>
		<div class="form_input">
			<!--   Password 1st   -->
			<input id="password" name="password" type="password" onblur="CitruscartCheckPassword( 'message-password', this.form, 'password', <?php echo $min_length ?>, <?php echo $req_num; ?>, <?php echo $req_alpha; ?>, <?php echo $req_spec; ?>  );"  class="inputbox_required" size="30" value="" />			
		</div>
		<div class="form_message" id="message-password"></div>
	</div>
	<div class="form_item">
		<div class="form_key">
			<?php echo JText::_('COM_CITRUSCART_VERIFY_PASSWORD').': '.CitruscartGrid::required(); ?>
		</div>
		<div class="form_input">
			<!--   Password 2nd   -->
			<input id="password2" name="password2" type="password" onblur="CitruscartCheckPassword2( 'message-password2', this.form, 'password', 'password2' );" class="inputbox_required" size="30" value="" />			
		</div>
		<div class="form_message" id="message-password2"></div>
	</div>
</div>
<input type="hidden" id="citruscart_target" name="target" value="" />
