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

JHtml::_('stylesheet',  'media/citruscart/css/citruscart.css');
$lang = JFactory::getLanguage();
$lang->load( 'com_citruscart', JPATH_SITE );
?>

<table style="width: 100%;"  >
<tr>
    <td style="vertical-align: top; padding: 5px;">

        <div class='componentheading'>
            <span><?php echo JText::_('COM_CITRUSCART_RETURNING_USERS'); ?></span>
        </div>

        <!-- LOGIN FORM -->

        <?php if (JPluginHelper::isEnabled('authentication', 'openid')) :
                $lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
                $langScript =   'var JLanguage = {};'.
                                ' JLanguage.WHAT_IS_OPENID = \''.JText::_('COM_CITRUSCART_WHAT_IS_OPENID').'\';'.
                                ' JLanguage.LOGIN_WITH_OPENID = \''.JText::_('COM_CITRUSCART_LOGIN_WITH_OPENID').'\';'.
                                ' JLanguage.NORMAL_LOGIN = \''.JText::_('COM_CITRUSCART_NORMAL_LOGIN').'\';'.
                                ' var modlogin = 1;';
                $document = JFactory::getDocument();
                $document->addScriptDeclaration( $langScript );
                JHTML::_('script', 'openid.js');
        endif; ?>

        <form action="<?php echo JRoute::_( 'index.php', true, Citruscart::getInstance()->get('usesecure', '0') ); ?>" method="post" name="login" id="form-login" >

            <table>
            <tr>
                <td style="height: 40px;">
                    <?php echo JText::_('COM_CITRUSCART_USERNAME'); ?>
                </td>
                <td>
                    <input type="text" name="username" class="inputbox" size="18" alt="username" />
                </td>
            </tr>
            <tr>
                <td style="height: 40px;">
                    <?php echo JText::_('COM_CITRUSCART_PASSWORD'); ?>
                </td>
                <td>
                    <input type="password" name="passwd" class="inputbox" size="18" alt="password" />
                </td>
            </tr>
            <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
            <tr>
                <td>
                    <?php echo JText::_('COM_CITRUSCART_REMEMBER_ME'); ?>
                </td>
                <td style="text-align: right;">
                    <span style="float: left">
                        <input type="checkbox" name="remember" class="inputbox" value="yes"/>
                    </span>
                    <input type="submit" name="submit" class="btn" value="<?php echo JText::_('COM_CITRUSCART_LOGIN') ?>" />
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <td style="height: 40px;">
                    <ul>
                        <li>
                            <?php // TODO Can we do this in a lightbox or something? Why does the user have to leave? ?>
                            <a href="<?php echo JRoute::_( 'index.php?option=com_user&view=reset' ); ?>">
                            <?php echo JText::_('COM_CITRUSCART_FORGOT_YOUR_PASSWORD'); ?></a>
                        </li>
                        <li>
                            <?php // TODO Can we do this in a lightbox or something? Why does the user have to leave? ?>
                            <a href="<?php echo JRoute::_( 'index.php?option=com_user&view=remind' ); ?>">
                            <?php echo JText::_('COM_CITRUSCART_FORGOT_YOUR_USERNAME'); ?></a>
                        </li>
                    </ul>
                </td>
                <td>
                </td>
            </tr>
            </table>

            <input type="hidden" name="option" value="com_user" />
            <input type="hidden" name="task" value="login" />
            <input type="hidden" name="return" value="<?php echo $this->return; ?>" />
            <?php echo JHTML::_( 'form.token' ); ?>
        </form>

    </td>
    <td style="vertical-align: top; padding: 5px; width: 50%;">

        <div class='componentheading'>
            <span><?php echo JText::_('COM_CITRUSCART_NEW_USERS'); ?></span>
        </div>
        <!-- REGISTRATION -->

        <table>
        <tr>
            <td style="height: 40px; padding: 5px;">
                <?php echo JText::_('COM_CITRUSCART_PLEASE_REGISTER_TO_CONTINUE_SHOPPING'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="button" class="btn" onclick="window.location='<?php echo JRoute::_( "index.php?option=com_user&view=register" ); ?>'" value="<?php echo JText::_('COM_CITRUSCART_REGISTER'); ?>" />
            </td>
        </tr>
        </table>

    </td>
</tr>
</table>
