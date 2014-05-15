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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');


$app = JFactory::getApplication();
// Check the registry to see if our Citruscart class has been overridden
if ( !class_exists('Citruscart') )
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

// load the config class
Citruscart::load( 'Citruscart', 'defines' );

JHtml::_('jQuery.framework');

// set the options array
$options = array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_citruscart' );

// Require the base controller
Citruscart::load( 'CitruscartController', 'controller', $options );

// Load Custom Language File if needed (com_citruscart_custom)
if(Citruscart::getInstance()->get('custom_language_file', '0'))
{
	$lang = JFactory::getLanguage();
	$extension = 'com_citruscart_custom';
	$base_dir = JPATH_SITE;
	$lang->load($extension, $base_dir, null, true);
}

// Check if protocol is specified
$protocol = $app->input->getWord('protocol', '');

// Require specific controller if requested
$controller = $app->input->getWord('controller', $app->input->get( 'view' ) );

// if protocol is specified, try to load the specific controller
if(strlen($protocol))
{
	// file syntax: controller_json.php
	if (Citruscart::load( 'CitruscartController'.$controller.$protocol, "controllers.".$controller."_".$protocol, $options ))
    	$controller .=  $protocol;
}
else
{
	if (!Citruscart::load( 'CitruscartController'.$controller, "controllers.$controller", $options ))
    	$controller = '';
}

if (empty($controller))
{
    // redirect to default
    $redirect = "index.php?option=com_citruscart&view=products";
    $redirect = JRoute::_( $redirect, false );
    JFactory::getApplication()->redirect( $redirect );
}

$doc = JFactory::getDocument();
$js = "var com_citruscart = {};\n";
$js.= "com_citruscart.jbase = '".Citruscart::getUriRoot()."';\n";
$doc->addScriptDeclaration($js);
JHtml::_('script', 'media/citruscart/js/common.js', false, false);

require_once(JPATH_SITE.'/libraries/dioscouri/loader.php');
$parentPath = JPATH_ADMINISTRATOR . '/components/com_citruscart/helpers';
DSCLoader::discover('CitruscartHelper', $parentPath, true);

$parentPath = JPATH_ADMINISTRATOR . '/components/com_citruscart/library';
DSCLoader::discover('Citruscart', $parentPath, true);

$parentPath = JPATH_ADMINISTRATOR . '/components/com_citruscart/tables';
DSCLoader::discover('CitruscartTable', $parentPath, true);

// load the plugins
JPluginHelper::importPlugin( 'citruscart' );

// Check Json Class Existance
if ( !function_exists('json_decode') )
{
	// This should load not only the class, but also json_encode / json_decode
	Citruscart::load('Services_JSON', 'library.json');
}

// Create the controller
$classname = 'CitruscartController'.$controller;
$controller = Citruscart::getClass( $classname );

// ensure a valid task exists
//$task = JRequest::getVar('task');
$task =$app->input->getString('task');
if (empty($task))
{
    $task = 'display';
}
$app->input->set( 'task', $task );

// Perform the requested task
$controller->execute( $task );

// Redirect if set by the controller
$controller->redirect();
