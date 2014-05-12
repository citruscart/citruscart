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


jimport('joomla.plugin.plugin');
class plgSystemCitruscart extends JPlugin
{
    function onAfterInitialise()
    {
        jimport('joomla.filesystem.file');
        if (!version_compare(JVERSION,'1.6.0','ge'))
        {
            // Joomla! 1.5 code here
            if (JFile::exists(JPATH_SITE.'/plugins/system/citruscart/citruscart.php'))
            {
                $this->attemptInstallation();
            }
        }

		// Determine Joomla! version
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			define('DSC_JVERSION', '30');
		}
		else
		if (version_compare(JVERSION, '2.5', 'ge'))
		{
			define('DSC_JVERSION', '25');
		}
		else
		{
			define('DSC_JVERSION', '15');
		}

		//TODO Clear out all DS in components, Define the DS constant under Joomla! 3.0
		if (!defined('DS'))
		{
			define('DS', DIRECTORY_SEPARATOR);
		}

		// Import Joomla! classes

		jimport('joomla.application.component.controller');
		jimport('joomla.application.component.model');
		jimport('joomla.application.component.view');

		// Get application
		$mainframe = JFactory::getApplication();

		// Load the Base classes
		JLoader::register('DSCTableBase', JPATH_SITE.'/libraries/citruscart/library/compatibility/table.php');
		JLoader::register('DSCControllerBase', JPATH_SITE.'/libraries/citruscart/library/compatibility/controller.php');
		JLoader::register('DSCModelBase', JPATH_SITE.'/libraries/citruscart/library/compatibility/model.php');
		JLoader::register('DSCViewBase', JPATH_SITE.'/libraries/citruscart/library/compatibility/view.php');

		if (!class_exists('DSC'))
		{
			if (!JFile::exists(JPATH_SITE.'/libraries/citruscart/citruscart.php')) {
				return false;
			}
			require_once JPATH_SITE.'/libraries/citruscart/citruscart.php';
		}
		return DSC::loadLibrary();
    }

    protected function attemptInstallation()
    {
        $return = false;

        // attempt to install the files manually (primarily for J1.5)

        if (!JFile::exists(JPATH_SITE.'/plugins/system/citruscart/citruscart.php')) {
            return $return;
        }

        jimport('joomla.filesystem.folder');

        $src = '/plugins/system/citruscart/';
        $dest = '/libraries/citruscart/';
        $src_folders = JFolder::folders(JPATH_SITE.'/plugins/system/citruscart', '.', true, true);
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
        $src = '/plugins/system/citruscart/';
        $dest = '/libraries/citruscart/';
        $src_files = JFolder::files(JPATH_SITE.'/plugins/system/citruscart', '.', true, true);
        if (!empty($src_files)) {
            foreach ($src_files as $src_file) {
                $src_filename = str_replace(JPATH_SITE, '', $src_file);
                $dest_filename = str_replace( $src, '', $src_filename);
                JFile::move(JPATH_SITE.$src_filename, JPATH_SITE.$dest.$dest_filename);
            }

            JFolder::delete(JPATH_SITE.'/plugins/system/citruscart');
        }

        // move the media files from libraries to media
        $src = '/libraries/citruscart/media/';
        $dest = '/media/citruscart/';
        $src_files = JFolder::files(JPATH_SITE.'/libraries/citruscart/media', '.', true, true);
        if (!empty($src_files)) {
            foreach ($src_files as $src_file) {
                $src_filename = str_replace(JPATH_SITE, '', $src_file);
                $dest_filename = str_replace( $src, '', $src_filename);
                JFile::move(JPATH_SITE.$src_filename, JPATH_SITE.$dest.$dest_filename);
            }
            JFolder::delete(JPATH_SITE.'/libraries/citruscart/media');
        }

        // move the lang files from libraries to language
        $src_files = JFolder::files(JPATH_SITE.'/libraries/citruscart/language', '.', true, true);
        $src = '/libraries/citruscart/language/';
        $dest = '/language/';
        if (!empty($src_files)) {
            foreach ($src_files as $src_file) {
                $src_filename = str_replace(JPATH_SITE, '', $src_file);
                $dest_filename = str_replace( $src, '', $src_filename);
                JFile::move(JPATH_SITE.$src_filename, JPATH_SITE.$dest.$dest_filename);
            }
            JFolder::delete(JPATH_SITE.'/libraries/citruscart/language');
        }

        if (JFile::exists(JPATH_SITE.'/libraries/citruscart/citruscart.php')) {
            $return = true;
        }

        return $return;
    }

	function onAfterRoute() {
		$doc = JFactory::getDocument();

		if($this->params->get('activeAdmin')==1) {
			$juri = JFactory::getURI();
			if(strpos($juri->getPath(),'/administrator/')!==false) return;
		}

		if($value=$this->params->get('embedjquery')) {
			DSC::loadJQuery('latest',$this->params->get('jquerynoconflict'));
		}

		JHTML::_('script', 'colorbox.js', 'media/com_citruscart/colorbox/');
		if($value=$this->params->get('embedbootstrap')) {
			DSC::loadBootstrap($this->params->get('bootstrapversion'), $this->params->get('bootstrapjoomla'));
		}


	}
}
