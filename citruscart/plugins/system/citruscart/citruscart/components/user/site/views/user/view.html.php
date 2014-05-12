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
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * HTML View class for the Users component
 *
 * @static
 * @package     Joomla
 * @subpackage  Weblinks
 * @since 1.0
 */
class UserViewUser extends JView
{
    function display( $tpl = null)
    {
        global $mainframe;

        $layout = $this->getLayout();
        if( $layout == 'form') {
            $this->_displayForm($tpl);
            return;
        }

        if ( $layout == 'login' ) {
            parent::display($tpl);
            return;
        }

        $user = JFactory::getUser();

        // Get the page/component configuration
        $params = $mainframe->getParams();

        $menus  = JSite::getMenu();
        $menu   = $menus->getActive();

        // because the application sets a default page title, we need to get it
        // right from the menu item itself
        if (is_object( $menu )) {
            $menu_params = new DSCParameter( $menu->params );
            if (!$menu_params->get( 'page_title')) {
                $params->set('page_title',  JText::_('COM_CITRUSCART_REGISTERED_AREA'));
            }
        } else {
            $params->set('page_title',  JText::_('COM_CITRUSCART_REGISTERED_AREA'));
        }
        $document   = JFactory::getDocument();
        $document->setTitle( $params->get( 'page_title' ) );

        // Set pathway information
        $this->assignRef('user'   , $user);
        $this->assignRef('params',      $params);

        parent::display($tpl);
    }

    function _displayForm($tpl = null)
    {
        global $mainframe;

        // Load the form validation behavior
        JHTML::_('behavior.formvalidation');

        $user     = JFactory::getUser();
        $params = $mainframe->getParams();

        // check to see if Frontend User Params have been enabled
        $usersConfig = JComponentHelper::getParams( 'com_users' );
        $check = $usersConfig->get('frontend_userparams');

        if ($check == '1' || $check == 1 || $check == NULL)
        {
            if($user->authorize( 'com_user', 'edit' )) {
                $params     = $user->getParameters(true);
            }
        }
        $params->merge( $params );
        $menus  = JSite::getMenu();
        $menu   = $menus->getActive();

        // because the application sets a default page title, we need to get it
        // right from the menu item itself
        if (is_object( $menu )) {
            $menu_params = new DSCParameter( $menu->params );
            if (!$menu_params->get( 'page_title')) {
                $params->set('page_title',  JText::_('COM_CITRUSCART_EDIT_YOUR_DETAILS'));
            }
        } else {
            $params->set('page_title',  JText::_('COM_CITRUSCART_EDIT_YOUR_DETAILS'));
        }
        $document   = JFactory::getDocument();
        $document->setTitle( $params->get( 'page_title' ) );

        $this->assignRef('user'  , $user);
        $this->assignRef('params', $params);

        parent::display($tpl);
    }
}
