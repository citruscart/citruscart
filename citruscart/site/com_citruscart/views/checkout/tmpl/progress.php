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
JHTML::_('stylesheet', 'menu.css', 'media/com_citruscart/css/');
$steps = $this->steps;
$current_step = $this->current_step;
?>

<div class="progressbar">
	<?php 
		$i = 0;
		foreach ($steps as $step)
		{
            ?>
    		<span class="step <?php if($i == $current_step) echo 'current-step'; ?>">
                <?php echo ($i+1).". ".JText::_( $step ); ?>
    		</span>
            <?php
    		$i++;
		}
	?>
</div>
