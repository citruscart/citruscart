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

// The following two lines must be defined in the component install.php file prior to including this file
//$thisextension = strtolower( "com_whatever" );
//$thisextensionname = substr ( $thisextension, 4 );

JLoader::import( 'dioscouri.library.installer', JPATH_SITE . DIRECTORY_SEPARATOR . 'libraries' );
$dscinstaller = new dscInstaller();
$dscinstaller->thisextension = $thisextension;
$dscinstaller->manifest = $this->manifest;
$dscinstaller->runInstallSQL();
$dscinstaller->fixAdminMenu( $thisextension );

// load the component language file
$language = JFactory::getLanguage();
$language->load( $thisextension );

$status = new JObject();
$status->modules = array();
$status->plugins = array();
$status->templates = array();
$status->libraries = array();

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
* // LIBRARIES INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
//$libraries = $dscinstaller->getElementByPath('libraries'); // TODO This isn't ready yet.  Finish this!  :-)  refs #16
$libraries = array();
if ( (is_a($libraries, 'JSimpleXMLElement') || is_a( $libraries, 'JXMLElement')) && !empty( $libraries ) && count($libraries->children())) {

    foreach ($libraries->children() as $library)
    {
        $name		= $dscinstaller->getAttribute('library', $library);
        $publish	= $dscinstaller->getAttribute('publish', $library);
        $client	    = JApplicationHelper::getClientInfo($dscinstaller->getAttribute('client', $library), true);

        // Set the installation path
        if (!empty ($name)) {
            $this->parent->setPath('extension_root', $client->path.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.$name);
        } else {
            $this->parent->abort(JText::_('Library').' '.JText::_('Install').': '.JText::_('Install Library File Missing'));
            return false;
        }

        /*
         * fire the dioscouriInstaller with the foldername and folder entryType
        */
        $pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$name;
        $dscInstaller = new dscInstaller();
        if (!empty($publish) && $publish == "true") {
            $dscInstaller->set( '_publishExtension', true );
        }
        $result = $dscInstaller->installExtension($pathToFolder, 'folder');

        // track the message and status of installation from dscInstaller
        if ($result)
        {
            $alt = JText::_( "Installed" );
            $status = "<img src='" . DSC::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
        } else {
            $alt = JText::_( "Failed" );
            $error = $dscInstaller->getError();
            $status = "<img src='" . DSC::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' />";
            $status .= " - ".$error;
        }

        $status->libraries[] = array('name'=>$name,'client'=>$client->name, 'status'=>$status );
    }
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * // TEMPLATES INSTALLATION SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
$templates = $dscinstaller->getElementByPath('templates');
if ( (is_a($templates, 'JSimpleXMLElement') || is_a( $templates, 'JXMLElement')) && !empty( $templates ) && count($templates->children())) {

	foreach ($templates->children() as $template)
	{
		$mname		= $dscinstaller->getAttribute('template', $template);
		$mpublish	= $dscinstaller->getAttribute('publish', $template);
		$mclient	= JApplicationHelper::getClientInfo($dscinstaller->getAttribute('client', $template), true);

		// Set the installation path
		if (!empty ($mname)) {
			$this->parent->setPath('extension_root', $mclient->path.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$mname);
		} else {
			$this->parent->abort(JText::_('Template').' '.JText::_('Install').': '.JText::_('Install Template File Missing'));
			return false;
		}

		/*
		 * fire the dioscouriInstaller with the foldername and folder entryType
		 */
		$pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$mname;
		$dscInstaller = new dscInstaller();
		if (!empty($mpublish) && $mpublish == "true") {
			$dscInstaller->set( '_publishExtension', true );
		}
		$result = $dscInstaller->installExtension($pathToFolder, 'folder');

		// track the message and status of installation from dscInstaller
		if ($result)
		{
			$alt = JText::_( "Installed" );
			$mstatus = "<img src='" . DSC::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
		} else {
			$alt = JText::_( "Failed" );
			$error = $dscInstaller->getError();
			$mstatus = "<img src='" . DSC::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' />";
			$mstatus .= " - ".$error;
		}

		$status->templates[] = array('name'=>$mname,'client'=>$mclient->name, 'status'=>$mstatus );
	}
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * MODULE INSTALLATION SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

$modules = $dscinstaller->getElementByPath('modules');
if ( (is_a($modules, 'JSimpleXMLElement') || is_a( $modules, 'JXMLElement')) && !empty( $modules ) && count($modules->children())) {

	foreach ($modules->children() as $module)
	{
		$mname		= $dscinstaller->getAttribute('module', $module);
		$mpublish	= $dscinstaller->getAttribute('publish', $module);
		$mposition	= $dscinstaller->getAttribute('position', $module);
		$mclient	= JApplicationHelper::getClientInfo($dscinstaller->getAttribute('client', $module), true);

		// Set the installation path
		if (!empty ($mname)) {
			$this->parent->setPath('extension_root', $mclient->path.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$mname);
		} else {
			$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('Install Module File Missing'));
			return false;
		}

		/*
		 * fire the dioscouriInstaller with the foldername and folder entryType
		 */
		$pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$mname;
		$dscInstaller = new dscInstaller();
		if (!empty($mpublish) && $mpublish == 'true') {
			$dscInstaller->set( '_publishExtension', true );
		}
		$result = $dscInstaller->installExtension($pathToFolder, 'folder', $mname);
//		$mname		= $dscinstaller->getModuleName( $mname );

		// track the message and status of installation from dscInstaller
		if ($result)
		{
			// set the position of the module if it is a new install and if position value exists in manifest
			if (!empty($mposition))
			{
				$db = JFactory::getDBO();
                $q = "UPDATE #__modules SET `position` = '{$mposition}' WHERE `module` = '{$result['element']}' AND `position` = '';";
                $db->setQuery($q);
				$db->query();
			}

			$alt = JText::_( "Installed" );
			$mstatus = "<img src='" . DSC::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
		} else {
			$alt = JText::_( "Failed" );
			$error = $dscInstaller->getError();
			$mstatus = "<img src='" . DSC::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' />";
			$mstatus .= " - ".$error;
		}

		$status->modules[] = array('name'=>$mname,'client'=>$mclient->name, 'status'=>$mstatus );
	}
}


/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * PLUGIN INSTALLATION SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

$plugins = $dscinstaller->getElementByPath('plugins');
if ( (is_a($plugins, 'JSimpleXMLElement') || is_a( $plugins, 'JXMLElement')) && !empty( $plugins ) && count($plugins->children())) {

	foreach ($plugins->children() as $plugin)
	{
		$pname		= $dscinstaller->getAttribute('plugin', $plugin);
		$ppublish	= $dscinstaller->getAttribute('publish', $plugin);
		$pgroup		= $dscinstaller->getAttribute('group', $plugin);
		$name		= $dscinstaller->getAttribute('element', $plugin);

		// Set the installation path
		if (!empty($pname) && !empty($pgroup)) {
			$this->parent->setPath('extension_root', JPATH_ROOT.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$pgroup);
		} else {
			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Install Plugin File Missing'));
			return false;
		}

		/*
		 * fire the dioscouriInstaller with the foldername and folder entryType
		 */
		$pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$pname;
		$dscInstaller = new dscInstaller();
		if (!empty($ppublish) && $ppublish == 'true') {
			$dscInstaller->set( '_publishExtension', true );
		}
		$result = $dscInstaller->installExtension($pathToFolder, 'folder', $name);

		// track the message and status of installation from dscInstaller
		if ($result) {
			$alt = JText::_( "Installed" );
			$pstatus = "<img src='" . DSC::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
		} else {
			$alt = JText::_( "Failed" );
			$error = $dscInstaller->getError();
			$pstatus = "<img src='" . DSC::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' /> ";
			$pstatus .= " - ".$error;
		}

		$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'status'=>$pstatus);
	}
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * SETUP DEFAULTS
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

// None

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * OUTPUT TO SCREEN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
$rows = 0;


<h2><?php echo JText::_('Installation Results'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo JText::_( $thisextension ); ?></td>
			<td class="key"><center><?php $alt = JText::_('Installed'); echo "<img src='" . DSC::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />"; ?></center></td>
		</tr>
<?php if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('Module'); ?></th>
			<th><?php echo JText::_('Client'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td class="key"><center><?php echo $module['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $plugin['name']; ?></td>
			<td class="key"><?php echo $plugin['group']; ?></td>
			<td class="key"><center><?php echo $plugin['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->templates)) : ?>
		<tr>
			<th><?php echo JText::_('Template'); ?></th>
			<th><?php echo JText::_('Client'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->templates as $template) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $template['name']; ?></td>
			<td class="key"><?php echo $template['client']; ?></td>
			<td class="key"><center><?php echo $template['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif; ?>
	</tbody>
</table>
