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

JLoader::import( 'dioscouri.library.installer', JPATH_SITE . '/libraries' );
$dscinstaller = new dscInstaller();
$dscinstaller->thisextension = $thisextension;
$dscinstaller->manifest = !empty($this->manifest) ? $this->manifest : $dscinstaller->getComponentManifestFile($thisextension);

// load the component language file
$language = JFactory::getLanguage();
$language->load( $thisextension );

$status = new JObject();
$status->modules = array();
$status->plugins = array();
$status->templates = array();

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* // TEMPLATES UNINSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$templates = $dscinstaller->getElementByPath('templates');
if ( (is_a($templates, 'JSimpleXMLElement') || is_a( $templates, 'JXMLElement')) && !empty( $templates ) && count($templates->children())) {

    foreach ($templates->children() as $template)
    {
        $mname		= $dscinstaller->getAttribute('template', $template);
        $mpublish	= $dscinstaller->getAttribute('publish', $template);
        $mclient	= JApplicationHelper::getClientInfo($dscinstaller->getAttribute('client', $template), true);

        $package    = array();
        $package['type'] = 'template';
        $package['group'] = '';
        $package['element'] = $mname;
        $package['client'] = $dscinstaller->getAttribute('client', $template);

        /*
         * fire the dioscouriInstaller with the foldername and folder entryType
        */
        $dscInstaller = new dscInstaller();
        $result = $dscInstaller->uninstallExtension($pathToFolder, 'folder');

        // track the message and status of installation from dscInstaller
        if ($result)
        {
            $alt = JText::_( "Uninstalled" );
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
 * MODULE UNINSTALLATION SECTION
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

        $package    = array();
        $package['type'] = 'module';
        $package['group'] = '';
        $package['element'] = str_replace('modules/', '', $mname);
        $package['client'] = $dscinstaller->getAttribute('client', $module);

        /*
         * fire the dioscouriInstaller
         */
        $dscInstaller = new dscInstaller();
        $result = $dscInstaller->uninstallExtension($package);

        // track the message and status of installation from dscInstaller
        if ($result)
        {
            $alt = JText::_( "Uninstalled" );
            $mstatus = "<img src='" . DSC::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
        }
            else
        {
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

        $package    = array();
        $package['type'] = 'plugin';
        $package['group'] = $pgroup;
        $package['element'] = $name;
        $package['client'] = '';

        /*
         * fire the dioscouriInstaller
         */
        $dscInstaller = new dscInstaller();
        $result = $dscInstaller->uninstallExtension($package);

        // track the message and status of installation from dscInstaller
        if ($result)
        {
            $alt = JText::_( "Uninstalled" );
            $pstatus = "<img src='" . DSC::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
        }
            else
        {
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
 * OUTPUT TO SCREEN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
 $rows = 0;
?>

<h2><?php echo JText::_('Uninstallation Results'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
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
			<td class="key" colspan="2"><?php echo JText::_('Component'); ?></td>
			<td><center><strong><?php echo JText::_('Removed'); ?></strong></center></td>
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
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td class="key"><center><?php echo $plugin['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif; ?>
	</tbody>
</table>
