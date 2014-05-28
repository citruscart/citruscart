<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access'); ?>
<?php $preview = $vars->preview; ?>
<?php $header = array_shift($preview); ?>

<div class="note_green">
	<table>
		<thead>
		<tr>
			 <?php foreach($header as $h): ?>
			 	<th>
			 		<?php echo $h; ?>
			 	</th>
			 <?php endforeach;?>
		</tr>
		</thead>
    <?php foreach($preview as $row): ?>

    	<tr>
    		<?php foreach($row as $field): ?>
    			<td>
    				<?php
    					if(is_array($field))
    					{
    						foreach($field as $key => $subfield)
    						{
    							if(is_array($subfield))
    							{
    								echo $key;
    								echo '<ul>';
    								foreach($subfield as $detail)
    								{
    									echo '<li>'.$detail.'</li>';
    								}
    								echo '</ul>';
    							}
    							else
    							{
    								echo $subfield;
    							}
    						}
    					}
    					else
    					{
    						echo $field;
    					}
    					?>
    			</td>
    		<?php endforeach;?>
    	</tr>
    <?php endforeach;?>
    </table>
</div>