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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Registration component
 *
 * @package     Joomla
 * @subpackage  Registration
 * @since 1.0
 */
class UserViewRegister extends JView
{
    function display($tpl = null)
    {
        global $mainframe;

        // Check if registration is allowed
        $usersConfig = JComponentHelper::getParams( 'com_users' );
        if (!$usersConfig->get( 'allowUserRegistration' )) {
            JError::raiseError( 403, JText::_('COM_CITRUSCART_ACCESS_FORBIDDEN'));
            return;
        }

        $pathway  = $mainframe->getPathway();
        $document = JFactory::getDocument();
        $params = &$mainframe->getParams();

        // Page Title
        $menus  = JSite::getMenu();
        $menu   = $menus->getActive();

        // because the application sets a default page title, we need to get it
        // right from the menu item itself
        if (is_object( $menu )) {
            $menu_params = new DSCParameter( $menu->params );
            if (!$menu_params->get( 'page_title')) {
                $params->set('page_title',  JText::_('COM_CITRUSCART_REGISTRATION'));
            }
        } else {
            $params->set('page_title',  JText::_('COM_CITRUSCART_REGISTRATION'));
        }
        $document->setTitle( $params->get( 'page_title' ) );

        $pathway->addItem( JText::_('COM_CITRUSCART_NEW'));

        // Load the form validation behavior
        JHTML::_('behavior.formvalidation');

        $user = JFactory::getUser();
        $this->assignRef('user', $user);
        $this->assignRef('params',      $params);
        parent::display($tpl);
    }
}
