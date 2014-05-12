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
defined('_JEXEC') or die('Restricted access'); ?>
<script type="text/javascript">
<!--
    Window.onDomReady(function(){
        document.formvalidator.setHandler('passverify', function (value) { return ($('password').value == value); } );
    });
// -->
</script>

<?php
    if(isset($this->message)){
        $this->display('message');
    }
?>

<form action="<?php echo JRoute::_( 'index.php?option=com_user' ); ?>" method="post" id="josForm" name="josForm" class="form-validate">

<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->params->get('page_title')); ?></div>
<?php endif; ?>

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
<tr>
    <td height="40">
        <label id="emailmsg" for="email">
            <?php echo JText::_('COM_CITRUSCART_EMAIL'); ?>:
        </label>
    </td>
    <td>
        <input type="text" id="email" name="email" size="40" value="<?php echo $this->escape($this->user->get( 'email' ));?>" class="inputbox required validate-email" maxlength="100" /> *
    </td>
</tr>
<tr>
    <td width="30%" height="40">
        <label id="namemsg" for="name">
            <?php echo JText::_('COM_CITRUSCART_NAME'); ?>:
        </label>
    </td>
    <td>
        <input type="text" name="name" id="name" size="40" value="<?php echo $this->escape($this->user->get( 'name' ));?>" class="inputbox required" maxlength="50" /> *
    </td>
</tr>
<tr>
    <td height="40">
        <label id="usernamemsg" for="username">
            <?php echo JText::_('COM_CITRUSCART_USER_NAME'); ?>:
        </label>
    </td>
    <td>
        <input type="text" id="username" name="username" size="40" value="<?php echo $this->escape($this->user->get( 'username' ));?>" class="inputbox required validate-username" maxlength="25" /> *
    </td>
</tr>
<tr>
    <td height="40">
        <label id="pwmsg" for="password">
            <?php echo JText::_('COM_CITRUSCART_PASSWORD'); ?>:
        </label>
    </td>
    <td>
        <input class="inputbox required validate-password" type="password" id="password" name="password" size="40" value="" /> *
    </td>
</tr>
<tr>
    <td height="40">
        <label id="pw2msg" for="password2">
            <?php echo JText::_('COM_CITRUSCART_VERIFY_PASSWORD'); ?>:
        </label>
    </td>
    <td>
        <input class="inputbox required validate-passverify" type="password" id="password2" name="password2" size="40" value="" /> *
    </td>
</tr>
<tr>
    <td colspan="2" height="40">
        <?php echo JText::_('COM_CITRUSCART_REGISTER_REQUIRED'); ?>
    </td>
</tr>
</table>
    <button class="button validate" type="submit"><?php echo JText::_('COM_CITRUSCART_REGISTER'); ?></button>
    <input type="hidden" name="task" value="register_save" />
    <input type="hidden" name="id" value="0" />
    <input type="hidden" name="gid" value="0" />
    <input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->get('return'); ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
