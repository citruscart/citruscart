<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');?>

<?php $state = $this->state; ?>
<?php $row = $this->row; ?>
<!-------- MOVE $this->loadTemplate outside the form the avoid having a form within a form since we already called the payment plugin form-------->
<?php if($this->step != 'step4'):?>
<form action="<?php echo JRoute::_( "index.php?option=com_citruscart&view=pos" )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <div class="pos">
        <?php echo $this->loadTemplate( $this->step ); ?>

        <input type="hidden" name="task" id="task" value="" />
        <input type="hidden" name="step" id="step" value="<?php echo $this->step; ?>" />
    </div>
</form>
<?php else:?>
	 <div class="pos">
        <?php echo $this->loadTemplate( $this->step ); ?>
    </div>
<?php endif;?>
