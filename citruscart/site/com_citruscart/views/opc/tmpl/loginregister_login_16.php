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

<?php $url = JRoute::_( "index.php?option=com_citruscart&view=opc", false ); ?>

<form action="<?php echo JRoute::_( 'index.php', true, Citruscart::getInstance()->get('usesecure', '0') ); ?>" method="post" name="login" id="form-login">

    <ul class="unstyled">
        <li>
            <label>
                <?php echo JText::_('COM_CITRUSCART_USERNAME'); ?> <span class>*</span>
            </label>
            <input id="citruscart-username" type="text" name="username" size="18" alt="username" />
        </li>
        <li>
            <label>
                <?php echo JText::_('COM_CITRUSCART_PASSWORD'); ?><span>*</span>
            </label>
            <input id="citruscart-password" type="password" name="password" size="18" alt="password" />
        </li>
        <?php if (JPluginHelper::isEnabled('system', 'remember')) { ?>
        <li>
            <label class="checkbox">
                <input id="citruscart-remember" type="checkbox" name="remember" value="yes" />
                <?php echo JText::_('COM_CITRUSCART_REMEMBER_ME'); ?>                
            </label>            
        </li>
        <?php } ?>
    </ul>

    <ul class="unstyled">
        <li> 
            <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"> 
                <?php echo JText::_('COM_CITRUSCART_FORGOT_YOUR_PASSWORD'); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"> 
                <?php echo JText::_('COM_CITRUSCART_FORGOT_YOUR_USERNAME'); ?>
            </a>
        </li>
    </ul>

    <div>
        <input type="submit" name="submit" class="btn btn-primary" value="<?php echo JText::_('COM_CITRUSCART_LOGIN') ?>" />
        <input type="hidden" name="option" value="com_users" /> 
        <input type="hidden" name="task" value="user.login" /> 
        <input type="hidden" name="return" value="<?php echo base64_encode( $url ); ?>" />    
        <?php echo JHTML::_( 'form.token' ); ?>
    </div>
</form>
