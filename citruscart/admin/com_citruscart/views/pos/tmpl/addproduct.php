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
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php JHtml::_('stylesheet', 'media/citruscart/css/pos.css'); ?>
<?php JHtml::_('stylesheet', 'media/citruscart/css/component.css'); ?>
<?php $state = $this->state; ?>
<?php $row = $this->row; ?>

<form action="index.php?option=com_citruscart&view=pos&tmpl=component" method="post" name="adminForm" enctype="multipart/form-data">
    <div class="pos">
        <?php echo $this->loadTemplate( 'search' ); ?>
        <br/>
        <?php echo $this->loadTemplate( 'results' ); ?>

        <input type="hidden" name="task" id="task" value="addproducts" />
    </div>
</form>
<?php 
$app = JFactory::getApplication();
$added = $app->input->getInt('addded', '0');
//$added=JRequest::getInt('added', '0')?>
<?php if($added):?>
<script type="text/javascript">
	window.onload = window.top.document.location.reload(true);
</script>
<?php endif;?>