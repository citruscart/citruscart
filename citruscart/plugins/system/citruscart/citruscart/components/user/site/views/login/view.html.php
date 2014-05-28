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

jimport( 'joomla.application.component.view');

/**
 * User component login view class
 *
 * @package		Joomla
 * @subpackage	Users
 * @since	1.0
 */
class UserViewLogin extends JView
{
	function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;

		global $mainframe, $option;

		// Initialize variables
		$document	=JFactory::getDocument();
		$user		=JFactory::getUser();
		$pathway	=$mainframe->getPathway();
		$image		= '';

		$menu   = JSite::getMenu();
		$item   = $menu->getActive();
		if($item)
			$params	= $menu->getParams($item->id);
		else
			$params	= $menu->getParams(null);


		$type = (!$user->get('guest')) ? 'logout' : 'login';

		// Set some default page parameters if not set
		$params->def( 'show_page_title', 				1 );
		if (!$params->get( 'page_title')) {
				$params->set('page_title',	JText::_('COM_CITRUSCART_LOGIN'));
			}
		if(!$item)
		{
			$params->def( 'header_login', 			'' );
			$params->def( 'header_logout', 			'' );
		}

		$params->def( 'pageclass_sfx', 			'' );
		$params->def( 'login', 					'index.php' );
		$params->def( 'logout', 				'index.php' );
		$params->def( 'description_login', 		1 );
		$params->def( 'description_logout', 		1 );
		$params->def( 'description_login_text', 	JText::_('COM_CITRUSCART_LOGIN_DESCRIPTION') );
		$params->def( 'description_logout_text',	JText::_('COM_CITRUSCART_LOGOUT_DESCRIPTION') );
		$params->def( 'image_login', 				'key.jpg' );
		$params->def( 'image_logout', 				'key.jpg' );
		$params->def( 'image_login_align', 			'right' );
		$params->def( 'image_logout_align', 		'right' );
		$usersConfig = JComponentHelper::getParams( 'com_users' );
		$params->def( 'registration', 				$usersConfig->get( 'allowUserRegistration' ) );

		if ( !$user->get('guest') )
		{
			$title = JText::_('COM_CITRUSCART_LOGOUT');

			// pathway item
			$pathway->addItem($title, '' );
			// Set page title
			$document->setTitle( $title );
		}
		else
		{
			$title = JText::_('COM_CITRUSCART_LOGIN');

			// pathway item
			$pathway->addItem($title, '' );
			// Set page title
			$document->setTitle( $title );
		}

		// Build login image if enabled
		if ( $params->get( 'image_'.$type ) != -1 ) {
			$image = 'images/stories/'.$params->get( 'image_'.$type );
			$image = '<img src="'. $image  .'" align="'. $params->get( 'image_'.$type.'_align' ) .'" hspace="10" alt="" />';
		}

		// Get the return URL
		if (!$url = $input->get('return', '', 'method', 'base64')) {
			$url = base64_encode($params->get($type));
		}

		$errors = JError::getErrors();

		$this->assign('image' , $image);
		$this->assign('type'  , $type);
		$this->assign('return', $url);

		$this->assignRef('params', $params);


		parent::display($tpl);
	}
}

