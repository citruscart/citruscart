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
$doc = JFactory::getDocument();

$doc->addStyleSheet(JUri::root().'/media/citruscart/css/leftmenu_admin.css');

?>

<div id="<?php echo $this->name; ?>" class="leftmenu-navigation">

    <ul class="nav nav-pills nav-stacked">
    <?php
  		  foreach ($this->items as $item) {
        ?>
        <li <?php  if ($item[2] == 1) {echo 'class="active"'; } ?> >
        <?php

        if ($this->hide) {

            if ($item[2] == 1) {
            ?>  <span class="nolink active"><?php echo $item[0]; ?></span> <?php
            } else {
            ?>  <span class="nolink"><?php echo $item[0]; ?></span> <?php
            }

        } else {
            $u = JURI::getInstance();
            $u->parse($item[1]);
            $class = '';
            if ($u->getVar('view'))
            {
                $class = 'view-'.$u->getVar('view');
            }

            if ($item[2] == 1) {
            ?> <a class="active <?php echo $class; ?>" href="<?php echo $item[1]; ?>"><?php echo $item[0]; ?></a> <?php
            } else {
            ?> <a class="<?php echo $class; ?>" href="<?php echo $item[1]; ?>"><?php echo $item[0]; ?></a> <?php
            }
        }
		?>
		</li>
		<?php
    }
    ?>
    </ul>
</div>