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
JHTML::_('stylesheet', 'menu.css', 'media/citruscart/css/');
$display_subscriptions = Citruscart::getInstance()->get( 'display_subscriptions', 1 );
$display_mydownloads = Citruscart::getInstance()->get( 'display_mydownloads', 1 );
?>

<div id="<?php echo $this->name; ?>" class="submenu">


<?php
foreach ($this->items as $item) {
		if( strpos( $item[1],'view=subscriptions' ) !== false && !$display_subscriptions )
			continue;

		if( strpos( $item[1],'view=productdownloads' ) !== false && !$display_mydownloads )
			continue;

    if ($this->hide) {

        if ($item[2] == 1) {
        ?>  <span class="nolink active"><?php echo $item[0]; ?></span> <?php
        } else {
        ?>  <span class="nolink"><?php echo $item[0]; ?></span> <?php
        }

    } else {

        if ($item[2] == 1) {
        ?> <a class="active" href="<?php echo JRoute::_( $item[1] ); ?>"><?php echo $item[0]; ?></a> <?php
        } else {
        ?> <a href="<?php echo JRoute::_( $item[1] ); ?>"><?php echo $item[0]; ?></a> <?php
        }
    }

}
?>

</div>