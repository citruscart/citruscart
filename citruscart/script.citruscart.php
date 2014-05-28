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
    public $lib_url = 'http://updates.dioscouri.com/library/downloads/latest.zip';
    public $plugin_url = 'http://updates.dioscouri.com/plg_system_dioscouri/downloads/latest.zip';
    public $plugin_url_j15 = 'http://updates.dioscouri.com/plg_system_dioscouri/downloads/j15/latest.zip';
    public $min_php_required = '5.3.0';


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
     * Load the library -- installing it if necessary
     *
     * @return boolean result of install & load
     */
    public function getLibrary()
    {
        if (!$this->checkPHPVersion()) {
            $this->setError( "You do not meet the minimum system requirements.  You must have at least PHP version: " . $this->min_php_required . " but you are using " . PHP_VERSION );
            return false;
        }

        jimport('joomla.filesystem.file');
        if (!class_exists('DSC')) {
            if (!JFile::exists(JPATH_SITE.'/libraries/dioscouri/dioscouri.php'))
            {
                JModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_installer/models' );
                if ($this->install('library'))
                {
                    // if j15, move files
                    if(!version_compare(JVERSION,'1.6.0','ge')) {
                        // Joomla! 1.5 code here
                        if (JFile::exists(JPATH_SITE.'/plugins/system/dioscouri/dioscouri.php')) {
                            $this->manuallyInstallLibrary();
                        }
                    }
                        else
                    {
                        if (!$this->install('plugin'))
                        {
                            $this->setError( "Could not install Dioscouri System Plugin" );
                        }
                    }

                    if (!$this->enablePlugin())
                    {
                        $this->setError( "Could not enable the Dioscouri System Plugin" );
                    }

                    if (JFile::exists(JPATH_SITE.'/libraries/dioscouri/dioscouri.php'))
                    {
                        require_once JPATH_SITE.'/libraries/dioscouri/dioscouri.php';
                        if (!DSC::loadLibrary()) {
                            $this->setError( "Could not load Dioscouri Library after installing it" );
                            return false;
                        }
                        return true;
                    }
                }
                    else
                {
                    $this->setError( "Could not install Dioscouri Library" );
                    return false;
                }
            }
            else
            {
                require_once JPATH_SITE.'/libraries/dioscouri/dioscouri.php';
                if (!DSC::loadLibrary()) {
                    $this->setError( "Could not load Dioscouri Library" );
                    return false;
                }
                return true;
            }
        }

        return true;
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
    * Get the package from the updates server
    *
    * @return	Package details or false on failure
    */
    protected function getPackageFromUrl( $type='library' )
    {
        jimport('joomla.installer.helper');

        // Get a database connector
        $db = JFactory::getDbo();

        // Get the URL of the package to install
        if(version_compare(JVERSION,'1.6.0','ge')) {
            // Joomla! 1.6+ code here
            switch($type) {
                case "plugin":
                    $url = $this->plugin_url;
                    break;
                case "library":
                default:
                    $url = $this->lib_url;
                    break;
            }

        } else {
            // Joomla! 1.5 code here
            $url = $this->plugin_url_j15;
        }

        // Download the package at the URL given
        $p_file = JInstallerHelper::downloadPackage($url);

        // Was the package downloaded?
        if (!$p_file) {
            $this->setError( JText::_('Could not download library installation package') );
            return false;
        }

        $config		= JFactory::getConfig();
        if(version_compare(JVERSION,'1.6.0','ge')) {
            // Joomla! 1.6+ code here
            $tmp_dest	= $config->get('tmp_path');
        } else {
            // Joomla! 1.5 code here
            $tmp_dest 	= $config->getValue('config.tmp_path');
        }

        // Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest . '/' . $p_file);

        return $package;
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
    protected function enablePlugin()
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
