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

<div class="dsc-table dsc-full">
    <div class="dsc-row">
        <div class="dsc-cell dsc-half">

            <h4>
                <?php echo JText::_('COM_CITRUSCART_RETURNING_USERS'); ?>
            </h4>

            <!-- LOGIN FORM -->

            <?php
            if (JPluginHelper::isEnabled('authentication', 'openid'))
            {
                $lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
                $langScript =   'var JLanguage = {};'.
                        ' JLanguage.WHAT_IS_OPENID = \''.JText::_('COM_CITRUSCART_WHAT_IS_OPENID').'\';'.
                        ' JLanguage.LOGIN_WITH_OPENID = \''.JText::_('COM_CITRUSCART_LOGIN_WITH_OPENID').'\';'.
                        ' JLanguage.NORMAL_LOGIN = \''.JText::_('COM_CITRUSCART_NORMAL_LOGIN').'\';'.
                        ' var modlogin = 1;';
                $document = JFactory::getDocument();
                $document->addScriptDeclaration( $langScript );
                JHTML::_('script', 'openid.js');
            }

            $modules = JModuleHelper::getModules("citruscart_checkout_login");
    		$document	= JFactory::getDocument();
    		$renderer	= $document->loadRenderer('module');
    		$attribs 	= array();
    		$attribs['style'] = 'xhtml';

    		foreach ( $modules as $mod )
    		{
    			echo $renderer->render($mod, $attribs);
    		}

    		if (empty($modules)) {
    		    echo $this->loadTemplate('login');
            }
            ?>

        </div>

        <div class="dsc-cell dsc-half">
            <form id="opc-checkout-method-form" name="opc-checkout-method-form" action="" method="post">

                <h4>
                    <?php echo JText::_('COM_CITRUSCART_NEW_USERS'); ?>
                </h4>

                <ul id="guest-or-register" class="unstyled">
                    <?php if (Citruscart::getInstance()->get('guest_checkout_enabled')) : ?>
                    <li class="control">
                        <label for="checkout-method-guest" class="radio">
                            <input type="radio" value="guest" id="checkout-method-guest" name="checkout_method">
                            <?php echo JText::_('COM_CITRUSCART_CHECKOUT_AS_A_GUEST'); ?>
                        </label>
                    </li>
                    <?php endif; ?>

                    <li class="control">
                        <label for="checkout-method-register" class="radio">
                            <input type="radio" value="register" id="checkout-method-register" name="checkout_method">
                            <?php echo JText::_( "COM_CITRUSCART_REGISTER" ); ?>
                        </label>
                    </li>
                </ul>

                <div id="email-password" class="opc-hidden control-group">
                    <label><?php echo JText::_( "COM_CITRUSCART_EMAIL_ADDRESS" ); ?></label>
                    <input type="text" name="email_address" class="required" />
                </div>

                <fieldset id="register-password" class="opc-hidden control-group">
                    <label><?php echo JText::_( "COM_CITRUSCART_PASSWORD" ); ?></label>
                    <input type="password" name="register-new-password" autocomplete="off" />

                    <label><?php echo JText::_( "COM_CITRUSCART_PASSWORD_CONFIRM" ); ?></label>
                    <input type="password" name="register-new-password2" autocomplete="off" />
                </fieldset>

                <div id="reasons-to-register">
                    <?php echo JText::_('COM_CITRUSCART_PLEASE_REGISTER_TO_CONTINUE_SHOPPING'); ?>
                </div>

                <a id="opc-checkout-method-button" class="btn btn-primary"><?php echo JText::_('COM_CITRUSCART_CONTINUE') ?></a>

            </form>
        </div>
    </div>
</div>
