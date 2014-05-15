<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = $this->form; ?>
<?php $row = $this->row; JFilterOutput::objectHTMLSafe( $row ); ?>

<form action ="index.php?option=com_citruscart&view=subscriptions&task=unsubscribe"  method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >
<input type="hidden" name="id" value="<?php echo $row->subscription_id; ?>" />
<div>

</div>
<input type="submit" value="Unsubscribe">
</form>
