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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
 ?>
<?php $url = "http://www.citruscart.com/";
if ($amigosid = Citruscart::getInstance()->get( 'amigosid', '' ))
{
    $url .= "?amigosid=".$amigosid;
}
?>

<?php if (Citruscart::getInstance()->get('show_linkback')) : ?>
<p align="center">
	<?php // echo JText::_('COM_CITRUSCART_POWERED_BY')." <a href='{$url}' target='_blank'>".JText::_('COM_CITRUSCART_CITRUSCART_ECOMMERCE')."</a>"; ?>
</p>
<?php endif; ?>
