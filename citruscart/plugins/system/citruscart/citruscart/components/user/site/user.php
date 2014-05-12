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

// this redefinition is required for this to work.  sorry about the notice; that's why constants are terrible
// maybe get error_reporting level as a variable, set error_reporting to none, redefine the constant, then set error reporting back to what it was?
define( 'JPATH_COMPONENT', dirname(__FILE__) );

// Require the defines
//require_once( dirname(__FILE__).'/defines.php' );

// Require the base controller
require_once( dirname(__FILE__).'/controller.php' );

$input = JFactory::getApplication()->input;

// Require specific controller if requested
if ($controller = $input->getWord('controller', $input->get( 'view' ) ))
{
    $path = dirname(__FILE__).'/controllers/'.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

// Create the controller
$controller_name = $controller;
$classname    = 'UserController'.$controller;
$controller   = new $classname();
if (empty($controller_name))
{
    $controller_name = $controller->get('_defaultView');
}

// include the model override
    $path = dirname(__FILE__).'/models/'.$controller_name.'.php';
    if (file_exists($path)) {
        require_once $path;
    }
        else
    {
        // TODO Include the core/default?
    }

// include the view override
    // TODO make this support view.pdf.php etc
    $path = dirname(__FILE__).'/views/'.$controller_name.'/view.html.php';
    if (file_exists($path)) {
        require_once $path;
    }
        else
    {
        // TODO Include the core/default?
    }

// include lang files
$element = strtolower( 'com_user' );
$lang = JFactory::getLanguage();
$lang->load( $element, JPATH_BASE );
$lang->load( $element, JPATH_ADMINISTRATOR );

// before executing any tasks, check the integrity of the installation
// TODO Here you could call some method for checking that DB tables exist, etc
//$diagnostic = new UserHelperDiagnostics();
//$diagnostic->checkInstallation();

//// load the plugins
JPluginHelper::importPlugin( 'user' );

// Perform the requested task
$controller->execute( $input->getString( 'task' ) );

// Redirect if set by the controller
$controller->redirect();

