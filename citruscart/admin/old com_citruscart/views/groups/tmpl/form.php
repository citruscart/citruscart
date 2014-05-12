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

defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = $this->form; ?>
<?php 

//$row = $this->row;

/* convert object into array format */
$row = JArrayHelper::fromObject($this->row);

JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, array( 'group_description' ) );

?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >


			<table class="table table-striped table-bordered">
				
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_('COM_CITRUSCART_NAME'); ?>:
					</td>
					<td>
						<input type="text" name="group_name" id="group_name" value="<?php echo $row['group_name']; ?>" size="48" maxlength="250" />
					</td>
				</tr>
				
				<tr>
    				<td style="width: 100px; text-align: right;" class="key">
    					<label for="group_description">
    					<?php echo JText::_('COM_CITRUSCART_DESCRIPTION'); ?>:
    					</label>
    				</td>
    				<td>
    					<?php $editor = JFactory::getEditor(); ?>
    					<?php echo $editor->display( 'group_description', $row['group_description'], '100%', '450', '100', '20' ) ; ?>
    				</td>
    			</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $row['group_id']; ?>" />
			<input type="hidden" name="task" value="" />
	
</form>