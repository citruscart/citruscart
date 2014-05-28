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
JHtml::_('behavior.keepalive');
?>
<div class="login<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	<div class="login-description">
	<?php endif ; ?>

		<?php if($this->params->get('logindescription_show') == 1) : ?>
			<?php echo $this->params->get('login_description'); ?>
		<?php endif; ?>

		<?php if (($this->params->get('login_image')!='')) :?>
			<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JTEXT::_('COM_USER_LOGIN_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	</div>
	<?php endif ; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post">

		<fieldset>
			<?php foreach ($this->form->getFieldset('credentials') as $field): ?>
				<?php if (!$field->hidden): ?>
					<div class="login-fields"><?php echo $field->label; ?>
					<?php echo $field->input; ?></div>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
			<div class="login-fields">
				<label id="remember-lbl" for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
				<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes"  alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" />
			</div>
			<?php endif; ?>
			<button type="submit" class="btn"><?php echo JText::_('JLOGIN'); ?></button>
			<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
</div>
<div>
	<ul>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
		</li>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
			<?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?></a>
		</li>
		<?php
		$usersConfig = JComponentHelper::getParams('com_users');
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
				<?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
</div>
