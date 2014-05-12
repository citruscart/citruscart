<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

defined('_JEXEC') or die;

// this redefinition is required for this to work.  sorry about the notice; that's why constants are terrible
// maybe get error_reporting level as a variable, set error_reporting to none, redefine the constant, then set error reporting back to what it was? 
$error_lvl = error_reporting();
ini_set( 'display_errors', 0 );
define( 'JPATH_COMPONENT', dirname(__FILE__) );
ini_set( 'display_errors', 1 );

require_once JPATH_COMPONENT.'/helpers/route.php';
error_reporting($error_lvl);

// Launch the controller.
$controller = JControllerLegacy::getInstance('Users');
$controller->execute(JRequest::get('task', 'display'));
$controller->redirect();
