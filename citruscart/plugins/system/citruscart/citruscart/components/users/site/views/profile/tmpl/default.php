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
JHtml::_('behavior.tooltip');
?>
<div class="profile<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<?php echo $this->loadTemplate('core'); ?>

<?php echo $this->loadTemplate('params'); ?>

<?php echo $this->loadTemplate('custom'); ?>

<?php if (JFactory::getUser()->id == $this->data->id) : ?>
<a href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>">
	<?php echo JText::_('COM_USERS_Edit_Profile'); ?></a>
<?php endif; ?>
</div>
