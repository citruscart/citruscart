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
defined('_JEXEC') or die('Restricted access'); ?>
<?php $errors = $vars->_errors; ?>
<p><?php echo JText::_('COM_CITRUSCART_THIS_TOOL_INSTALL_SAMPLE_DATA_TO_CITRUSCART'); ?></p>
<div style="margin-bottom: 10px; background-color:#EFE7B8;border-bottom-color:#F0DC7E;border-bottom-style:solid;border-bottom-width:3px;border-top-color:#F0DC7E;border-top-style:solid;border-top-width:3px;color:#CC0000;padding-left: 10px;">
	<p><?php echo JText::_('COM_CITRUSCART_ERROR_INSTALLING_SAMPLE_DATA_TO_CITRUSCART'); ?></p>
</div>
<table class="adminlist" style="clear: both;">
	<thead>
    	<tr>
        	<th style="width: 5px;">
            	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
            </th>
            <th>
            	<?php echo JText::_('COM_CITRUSCART_MESSAGE'); ?>
            </th>
            <th>
                <?php echo JText::_('COM_CITRUSCART_SQL_QUERY'); ?>
            </th>
        </tr>
        <tbody>
        	<?php $i=0; $k=0; ?>
        	<?php foreach($errors as $error):?>
			<tr class='row<?php echo $k; ?>'>
				<td align="center">
                    <?php echo $i + 1; ?>
                </td>
			    <td>
			    	<?php echo $error['msg']; ?>
			    </td>
			    <td>
			    	<?php echo $error['sql']; ?>
			    </td>
			</tr>
			 <?php ++$i; $k = (1 - $k); ?>
			<?php endforeach;?>

        </tbody>
</table>
