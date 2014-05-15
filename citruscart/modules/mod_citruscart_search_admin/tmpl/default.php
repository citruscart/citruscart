<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
$doc = JFactory::getDocument();
$doc->addStyleSheet( JURI::root(true).'/administrator/modules/mod_citruscart_search_admin/tmpl/stylesheet.css');
?>
<style type="text/css">
div.mod_citruscart_search_admin {
	text-align: center;
	margin-bottom: 10px;
}

input.Citruscart_search_admin_keyword {
	width: 125px;
}
</style>

<form action="index.php?option=com_citruscart&view=dashboard&task=search" method="post">
    <div class="mod_citruscart_search_admin<?php echo $class_suffix; ?>">
		<!-- class="citruscart_search_admin_keyword  echo $class_suffix; -->
        <input type="text" class="input-small" name="Citruscart_search_admin_keyword" value="" />
        <?php echo CitruscartSelect::view( "", "Citruscart_search_admin_view",array("class"=>"input-small") ); ?>
        <button type="submit" class="btn btn-primary" name="Citruscart_search_admin_submit" ><?php echo JText::_('COM_CITRUSCART_QUICK_SEARCH'); ?></button>
        <?php if (empty($display_outside)) : ?>
            <input type="hidden" class="input-mini" name="task" value="search" />
        <?php endif; ?>
    </div>
</form>
