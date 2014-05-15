<?php
/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); ?>

<?php
if(version_compare(JVERSION,'1.6.0','ge')) { ?>
	<a target="_blank" href="http://projects.citruscart.com/projects/citruscart/"><?php echo JText::_('COM_CITRUSCART_SUBMIT_BUG'); ?></a>
<?php } else {?>
	<a href="<?php echo JRoute::_( 'index.php?option=com_citruscart&task=doTask&element=bug_report&elementTask=submitBug' ); ?>"><?php echo JText::_('COM_CITRUSCART_SUBMIT_BUG'); ?></a>
	<?php
}
