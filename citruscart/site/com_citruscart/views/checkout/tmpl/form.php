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

defined('_JEXEC') or die('Restricted access');?>
<?php $url = JRoute::_( "index.php?option=com_citruscart&view=checkout", false ); ?>

<table style="width: 100%;">
<tr>
    <td style="vertical-align: top; padding: 5px; border-right: 1px solid #CCC;">
    
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
        
        <?php
        
        $modules = JModuleHelper::getModules("Citruscart_checkout_login");
		$document	= JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$attribs 	= array();
		$attribs['style'] = 'xhtml';
		
		foreach ( $modules as $mod ) 
		{
			echo $renderer->render($mod, $attribs);
		}
        
		
         ?>
        <?php if(empty($modules)) : ?>
        
        <?php echo  $this->loadTemplate('login'); ?>
       
    <?php endif; ?>
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
            <?php if (Citruscart::getInstance()->get('one_page_checkout')){ ?>	
             	<input type="button" class="btn" onclick="CitruscartGetRegistrationForm( 'Citruscart_checkout_method', '', '' ); " value="<?php echo JText::_('COM_CITRUSCART_REGISTER'); ?>" />
            <?php }else{?>	
                <input type="button" class="btn" onclick="window.location='<?php echo JRoute::_( "index.php?option=com_citruscart&view=checkout&register=1&Itemid=".$this->checkout_itemid, false ); ?>'" value="<?php echo JText::_('COM_CITRUSCART_REGISTER'); ?>" />
            <?php }?>
            </td>
        </tr>
        </table>

        <div class="reset"></div>
        
        <?php if (Citruscart::getInstance()->get('guest_checkout_enabled')) : ?>
            <div class='componentheading' style="margin-top:15px;">
                <span><?php echo JText::_('COM_CITRUSCART_CHECKOUT_AS_A_GUEST'); ?></span>
            </div>
            <!-- REGISTRATION -->
        
            <table>
            <tr>
                <td style="height: 40px; padding: 5px;">
                    <?php echo JText::_('COM_CITRUSCART_CHECKOUT_AS_A_GUEST_DESC'); ?>
                </td>
            </tr>
            <tr>
                <td>
                <?php  if (Citruscart::getInstance()->get('one_page_checkout')){?>
				<input id="citruscart_btn_register" type="button" class="btn" onclick="CitruscartGetCustomerInfo( 'onShowCustomerInfo');" value="<?php echo JText::_('COM_CITRUSCART_CHECKOUT_AS_A_GUEST'); ?>" />
          
				<?php }else{?>
                    <input type="button" class="btn" onclick="window.location='<?php echo JRoute::_( "index.php?option=com_citruscart&view=checkout&guest=1&Itemid=".$this->checkout_itemid, false ); ?>'" value="<?php echo JText::_('COM_CITRUSCART_CHECKOUT_AS_A_GUEST'); ?>" />
               	<?php }?>
                </td>
            </tr>
            </table>
        <?php endif; ?>        
    </td>
</tr>
</table>