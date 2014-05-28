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

jimport('joomla.filesystem.file');
class Com_CitruscartInstallerScript{

	public function postflight($type, $parent)
	{
		$db = JFactory::getDBO();
		$app = JFactory::getApplication('site');
		$status = new stdClass;
        $status->plugins = array();
        $src = $parent->getParent()->getPath('source');
		$manifest = $parent->getParent()->manifest;
		$modules = $manifest->xpath('modules/module');
		foreach ($modules as $module)
		{
			$name = (string)$module->attributes()->module;
			$client = (string)$module->attributes()->client;

			if (is_null($client))
			{
				$client = 'site';
			}
			$path = $src.'/modules/'.$name;
			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
		}

		$plugins = $manifest->xpath('plugins/plugin');
		foreach ($plugins as $plugin)
		{
			$name = (string)$plugin->attributes()->plugin;
			$group = (string)$plugin->attributes()->group;
			$path = $src.'/plugins/'.$group;

			if (JFolder::exists($src.'/plugins/'.$group.'/'.$name))
			{
				$path = $src.'/plugins/'.$group.'/'.$name;
			}

			$installer = new JInstaller;

			$result = $installer->install($path);

			if($type !='update') {
				$query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=".$db->q($name)." AND folder=".$db->q($group);
				$db->setQuery($query);
				$db->query();
			}

			$status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
		}

		$libInstaller = new CitruscartInstaller();
		$libInstaller->manuallyInstallLibrary();
		$libInstaller->enablePlugin();

	}


    public function uninstall($parent)
    {
       $db = JFactory::getDBO();
		$status = new stdClass;
		$status->modules = array();
		$status->plugins = array();
		$manifest = $parent->getParent()->manifest;
		$plugins = $manifest->xpath('plugins/plugin');
		foreach ($plugins as $plugin)
		{
			$name = (string)$plugin->attributes()->plugin;
			$group = (string)$plugin->attributes()->group;
			$query = "SELECT `extension_id` FROM #__extensions WHERE `type`='plugin' AND element = ".$db->q($name)." AND folder = ".$db->q($group);
			$db->setQuery($query);
			$extensions = $db->loadColumn();
			if (count($extensions))
			{
				foreach ($extensions as $id)
				{
					$installer = new JInstaller;
					$result = $installer->uninstall('plugin', $id);
				}
				$status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
			}

		}
		$modules = $manifest->xpath('modules/module');
		foreach ($modules as $module)
		{
			$name = (string)$module->attributes()->module;
			$client = (string)$module->attributes()->client;
			$db = JFactory::getDBO();
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='module' AND element = ".$db->q($name)."";
			$db->setQuery($query);
			$extensions = $db->loadColumn();
			if (count($extensions))
			{
				foreach ($extensions as $id)
				{
					$installer = new JInstaller;
					$result = $installer->uninstall('module', $id);
				}
				$status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
			}

		}

    }


	}

class CitruscartInstaller extends JObject
{

    /**
     * Checks the minimum required php version
     * @return boolean
     * preflight
     */
    protected function checkPHPVersion()
    {
        if (version_compare(PHP_VERSION, $this->min_php_required) >= 0) {
            return true;
        }

        return false;
    }




    /**
    * Install the library package
    *
    * @return	boolean result of install
    */
    function install( $type='library' )
    {
        jimport('joomla.installer.installer');
        jimport('joomla.installer.helper');

        $app = JFactory::getApplication();
        $package = $this->getPackageFromUrl($type);

        // Was the package unpacked?
        if (!$package) {
            $this->setError( JText::_('Could not find unpacked installation package') );
            return false;
        }

        // Get an installer instance
        $installer = new JInstaller();

        // Install the package
        if (!$installer->install($package['dir'])) {
            // There was an error installing the package
            $this->setError( 'There was an error installing the package' );
            $result = false;
        } else {
            // Package installed sucessfully
            $result = true;
        }

        // Cleanup the install files
        if (!is_file($package['packagefile'])) {
            $config = JFactory::getConfig();
            if(version_compare(JVERSION,'1.6.0','ge')) {
                // Joomla! 1.6+ code here
                $tmp_dest	= $config->get('tmp_path');
            } else {
                // Joomla! 1.5 code here
                $tmp_dest 	= $config->getValue('config.tmp_path');
            }
            $package['packagefile'] = $tmp_dest . '/' . $package['packagefile'];
        }

        JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

        return $result;
    }


    /**
     * Install the library files manually (only for J1.5)
     * @return boolean
     */
	public function manuallyInstallLibrary()
    {
        jimport('joomla.filesystem.file');


        $return = false;

        if (!JFile::exists(JPATH_SITE.'/plugins/system/dioscouri/dioscouri.php')) {
            return $return;
        }
            jimport('joomla.filesystem.folder');

        $src = '/plugins/system/dioscouri/dioscouri/';
        $dest = '/libraries/dioscouri/';
        $src_folders = JFolder::folders(JPATH_SITE.'/plugins/system/dioscouri', '.', true, true);
        if (!empty($src_folders)) {
            foreach ($src_folders as $src_folder) {
                $src_folder = str_replace(JPATH_SITE, '', $src_folder);
                $dest_folder = str_replace( $src, '', $src_folder);
                if (!JFolder::exists(JPATH_SITE.$dest.$dest_folder)) {
                    JFolder::create(JPATH_SITE.$dest.$dest_folder);
                }
            }
        }

        // move files from plugins to libraries
        $src = '/plugins/system/dioscouri/dioscouri/';
        $dest = '/libraries/dioscouri/';
        $src_files = JFolder::files(JPATH_SITE.'/plugins/system/dioscouri', '.', true, true);
        if (!empty($src_files)) {
            foreach ($src_files as $src_file) {
              $src_filename = str_replace(JPATH_SITE, '', $src_file);

                $dest_filename = str_replace( $src, '', $src_filename);
                JFile::move(JPATH_SITE.$src_filename, JPATH_SITE.$dest.$dest_filename);
            }
            JFolder::delete(JPATH_SITE.'/plugins/system/dioscouri/dioscouri/');
        }
       return $return;
    }
    /**
     * Enables the system plugin after installation
     *
     * @return boolean
     */
    public function enablePlugin()
    {
        if(version_compare(JVERSION,'1.6.0','ge')) {
            // Joomla! 1.6+ code here
            $query	= "UPDATE #__extensions SET `enabled` = '1' WHERE `type` = 'plugin' AND `folder` = 'system' AND `element` = 'dioscouri';";
        } else {
            // Joomla! 1.5 code here
            $query	= "UPDATE #__plugins SET `published` = '1' WHERE `folder` = 'system' AND `element` = 'dioscouri';";
        }

        $db = JFactory::getDBO();
        $db->setQuery( $query );
        if (!$db->query())
        {
            return false;
        }

        return true;
    }
}
