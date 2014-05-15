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

defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = $this->form; ?>
<?php $row = $this->row; ?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >

	<h3>
	    <?php echo $row->name ?>
	</h3>
	
	<?php
		
		$results = JFactory::getApplication()->triggerEvent( 'onGetToolView', array( $row ) );

        for ($i=0; $i<count($results); $i++) 
        {
            $result = $results[$i];
            echo $result;
        }
	?>
	
	<?php
	    echo $form['validate'];
	?>   
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="task" id="task" value="" />
	
</form>