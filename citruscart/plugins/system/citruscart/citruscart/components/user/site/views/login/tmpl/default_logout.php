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
defined('_JEXEC') or die('Restricted access');
 ?>
<?php /** @todo Should this be routed */ ?>
<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" name="login" id="login">
<?php if ( $this->params->get( 'show_logout_title' ) ) : ?>
<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<?php echo $this->escape($this->params->get( 'header_logout' )); ?>
</div>
<?php endif; ?>
<table border="0" align="center" cellpadding="4" cellspacing="0" class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" width="100%">
<tr>
	<td valign="top">
		<div>
		<?php echo $this->image; ?>
		<?php
			if ($this->params->get('description_logout')) :
				echo $this->escape($this->params->get('description_logout_text'));
			endif;
		?>
		</div>
	</td>
</tr>
<tr>
	<td align="center">
		<div align="center">
			<input type="submit" name="Submit" class="btn" value="<?php echo JText::_('COM_CITRUSCART_LOGOUT'); ?>" />
		</div>
	</td>
</tr>
</table>

<br /><br />

<input type="hidden" name="option" value="com_user" />
<input type="hidden" name="task" value="logout" />
<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
</form>
