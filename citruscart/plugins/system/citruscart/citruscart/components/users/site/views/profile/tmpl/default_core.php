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

?>

<fieldset id="users-profile-core">
	<legend>
		<?php echo JText::_('COM_USERS_PROFILE_CORE_LEGEND'); ?>
	</legend>
	<dl>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?>
		</dt>
		<dd>
			<?php echo $this->data->name; ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>
		</dt>
		<dd>
			<?php echo htmlspecialchars($this->data->username); ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
		</dt>
		<dd>
			<?php echo JHtml::_('date', $this->data->registerDate); ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>
		</dt>

		<?php if ($this->data->lastvisitDate != '0000-00-00 00:00:00'){?>
			<dd>
				<?php echo JHtml::_('date', $this->data->lastvisitDate); ?>
			</dd>
		<?php }
		else {?>
			<dd>
				<?php echo JText::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
			</dd>
		<?php } ?>

	</dl>
</fieldset>
