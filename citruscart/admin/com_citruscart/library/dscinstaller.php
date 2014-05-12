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

//import nessecary libararies
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.path');
jimport('joomla.installer.installer' );
jimport('joomla.installer.helper' );

/** CHECK FIRST to see if this exists already */
if (!class_exists( 'dscInstaller' )) {

	/**
	 * Dioscouri Installer Class
	 * Installs all of the joomla extensions in a given directory
	 *
	 * Several of the ideas in this code were taken from the Core Joomla installer class
	 *
	 * @author Dioscouri Design
	 * @author Joomla Core Team
	 * @link http://www.dioscouri.com
	 * @copyright Copyright (C) 2008 Dioscouri Design. All rights reserved.
	 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 */
	class dscInstaller extends JObject {

		/**
		 * The path to the folder which contains the packages to install
		 *
		 * @var string
		 */
		var $_extensionsPath = null;

		/**
		 * True if any previous data in the params of an extension should be saved
		 *
		 * @var boolean
		 */
		var $_saveParameters = true;

		/**
		 * True if the installer should block any custom uninstall scripts
		 *
		 * @var boolean
		 */
		var $_preventUninstallScript = true;

		/**
		 * True if the local copy of the package file should NOT be deleted after install
		 *
		 * @var boolean
		 */
		var $_keepLocalCopy = false;

		/**
		 * An array contaning any extensions that should NOT be auto published/enabled
		 *
		 * @var array
		 */
		var $_doNotPublishList = array();

		/**
		 * The joomla message object allow message output
		 *
		 * @var object
		 */
		var $msg = null;

		/**
		 * A database connector object
		 *
		 * @var object
		 */
		var $_db = null;

		/**
		 * an array containing the list of package files to operate on
		 *
		 * @var array
		 */
		var $_packageFiles = array();

		/**
		 * an array containing the list of extensions that were installed
		 *
		 * @var array
		 */
		var $_installedExtensions = array();

		/**
		 * Constructor
		 *
		 */
		function __construct()
		{
			$this->msg = new stdClass();
			$this->_db = JFactory::getDBO();
		}

		/**
		 * Attempts to install and joomla extensions packages in the given directory
		 *
		 * @access	public
		 * @param	string $path Path to extensions folder
		 * @return	boolean	True if successful, False if error
		 */
		function installExtensions($path=null) {
			//set the extensions path if nessecary
			if ($path && JFolder::exists($path)) {
				$this->setExtensionsPath($path);
			} else {
				//$this->abort(JText::_('COM_CITRUSCART_EXTENSIONS_PATH_DOES_NOT_EXIST'));
				$this->msg->type = 'notice';
				$this->msg->message = JText::_('COM_CITRUSCART_EXTENSIONS_PATH_DOES_NOT_EXIST');
				return false;
			}

			//prepare for the installation
			if (!$this->setupExtensionsInstall()) {
				//$this->abort(JText::_('COM_CITRUSCART_UNABLE_TO_FIND_ANY_PACKAGES'));
				$this->msg->type = 'notice';
				$this->msg->message = JText::_('COM_CITRUSCART_NO_PACKAGES_FOUND_AT')." ".$this->_extensionsPath;
				return false;
			}

			//iterate through package files
			foreach ($this->_packageFiles as $file) {
				//launch the install funtion
				$result = $this->installExtension($file);

				//if the install was successfull add to keep track of the installed extensions
				if($result != false) {
					$this->_addInstalledExtension($result);
				}
			}

			//format the output message
			$this->_formatMessage();
			return true;
		}

		/**
		 * Prepares for the installation by finding the package files
		 *
		 * @access public
		 * @return boolean True on success, False on error
		 */
		function setupExtensionsInstall() {
			// We need to find the files to install
			if (!$this->_findExtensionFiles()) {
				return false;
			}
			return true;
		}

		/**
		 * Tries to find any installer packages to install
		 *
		 * @access private
		 * @return boolean True on success, False on error
		 */
		function _findExtensionFiles() {

			if (empty($this->_extensionsPath)) {
				return false;
			}

			//make sure we only get the package files in that directory
			$zipFiles = JFolder::files($this->_extensionsPath, '\.zip$', false, false);
			$gzFiles = JFolder::files($this->_extensionsPath, '\.gz$', false, false);
			$bz2Fies = JFolder::files($this->_extensionsPath, '\.bz2$', false, false);
			$files = array_merge($zipFiles, $gzFiles, $bz2Fies);

			if (count($files) > 0) {
				$this->_packageFiles = $files;
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Creates a clone of the array returned by JInstallerHelper::unpack()
		 * from a folder (i.e. a joomla extension zipfile that's already extracted)
		 *
		 * @param $folder
		 * @return Array Two elements - extractdir and packagefile
		 */
		function createVirtualPackage( $folder )
		{
			$retval = array();

			// folder needs to be the full path to
			// Path to the archive
			$archivename = $folder;

			// Temporary folder to extract the archive into
			$tmpdir = uniqid('install_');

			// Clean the paths to use for archive extraction
			$extractdir = JPath::clean(dirname($folder).DS.$tmpdir);
			$archivename = JPath::clean($archivename);

			// copy the contents of the $folder to the extractdir
			$result = JFolder::copy($archivename, $extractdir);

			if ( $result === false ) {
				return false;
			}


			/*
			 * Lets set the extraction directory and package file in the result array so we can
			 * cleanup everything properly later on.
			 */
			$retval['extractdir'] = $extractdir;
			$retval['packagefile'] = $archivename;

			/*
			 * Try to find the correct install directory.  In case the package is inside a
			 * subdirectory detect this and set the install directory to the correct path.
			 *
			 * List all the items in the installation directory.  If there is only one, and
			 * it is a folder, then we will set that folder to be the installation folder.
			 */
			$dirList = array_merge(JFolder::files($extractdir, ''), JFolder::folders($extractdir, ''));

			if (count($dirList) == 1)
			{
				if (JFolder::exists($extractdir.DS.$dirList[0]))
				{
					$extractdir = JPath::clean($extractdir.DS.$dirList[0]);
				}
			}

			/*
			 * We have found the install directory so lets set it and then move on
			 * to detecting the extension type.
			 */
			$retval['dir'] = $extractdir;

			/*
			 * Get the extension type and return the directory/type array on success or
			 * false on fail.
			 */
			if ($retval['type'] = JInstallerHelper::detectType($extractdir))
			{
				return $retval;
			} else
			{
				return false;
			}
		}

		/**
		 * Uses the joomla installer framework to install an extension from a package file
		 *
		 * @param $file String the filename of the file to be installed
		 * @return the extension information if the install is successfull, false otherwise
		 */
		function installExtension($entry, $entryType='archive')
		{
            switch(strtolower($entryType))
			{
				case "Folder":
				case "folder":
					// create a package array based on contents of folder

					$package = $this->createVirtualPackage( $entry );
				  break;
				default:
					//Build the appropriate paths
					$config = JFactory::getConfig();
					$packageFile = $this->_extensionsPath.DS.$entry;

					//Unpack the package file
					$package = JInstallerHelper::unpack($packageFile);
				  break;
			}

			//Get an installer instance, always get a new one
			$installer = new JInstaller();

			//setup for the install
			if ($package['dir'] && JFolder::exists($package['dir'])) {
				$installer->setPath('source', $package['dir']);
			} else {
				$this->setError( "dscInstaller::installExtension: ".JText::_('COM_CITRUSCART_PACKAGE_DIR_DOES_NOT_EXIST') );
				return false;
			}

			//this makes sure the manifest file is loaded into the installer object
			if (!$installer->setupInstall())
			{
				$this->setError( "dscInstaller::installExtension: ".JText::_('COM_CITRUSCART_COULD_NOT_LOAD_MANIFEST_FILE') );
				return false;
			}

			//grab the manifest information
			$manifestInformation = $this->getManifestInformation($installer);
            $savedParameters = new stdClass();

			//check if the extension is installed already and if so uninstall it
			$elementID = $this->checkIfInstalledAlready($manifestInformation);

			// This is not necessary if everything has method=upgrade
            //			if ($elementID != 0) {
            //				//save the extensions parameters if requested
            //				$savedParameters = $this->saveParameters($manifestInformation);
            //
            //				//prevent any custom uninstall scripts if requested for components
            //				if (($this->_preventUninstallScript) && ($manifestInformation["type"] == "component")) {
            //					$this->preventCustomUninstall($installer);
            //				}
            //				//uninstall the extension using the joomla uninstaller
            //				$installer->uninstall($manifestInformation["type"], $elementID);
            //			}

			//set the installer to overwrite just encase any files were left on the server
			$installer->setOverwrite(true);
			//now that the extension was uninstalled if nessecary we can install it
			if (!$installer->install($package['dir'])) {
				//something blew up with the install if we get here
				$this->setError( "dscInstaller::installExtension: ".$installer->getError() );
				$result = false;
			} else {
				// Package installed sucessfully so publish the extension if set to yes
				$manifestInformation = $this->getManifestInformation($installer);
				$publishExtension = $this->get( '_publishExtension', false );

				if ($publishExtension)
				{
					$this->publishExtension($manifestInformation);
				}

				//restore extension parameters if requested
				if ($this->_saveParameters && isset($savedParameters->params)) {
					$this->restoreParameters($manifestInformation, $savedParameters);
				}
				$result = true;
			}

			// Cleanup the install files
			if (!is_file($package['packagefile'])) {
				$config = JFactory::getConfig();
				$package['packagefile'] = $config->getValue('config.tmp_path').DS.$package['packagefile'];
			}

			//decide whether or not to delete the local copy
			if (!$this->_keepLocalCopy) {
				//delete temporary directory and install file
				JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
			} else {
				//just delete the temporary directory
				JInstallerHelper::cleanupInstall("", $package['extractdir']);
			}

			//check to see if the install was successfull and if so return the manifestinformation
			if ($result) {
				return $manifestInformation;
			} else {
				// error message is already set
				return false;
			}
		}

	    /**
	     * Attempts to uninstall the specified extension
	     *
	     * @param array $pkg the paket package information
	     * @return Boolean True if successful, false otherwise
	     */
	    function uninstallExtension($package)
	    {
	        //Get an installer instance, always get a new one
	        $installer = new JInstaller();

	        //attemp to load the manifest file
	        $file = $this->findManifest($package);

	        //check to see if the manifest was found
	        if (isset($file)) {
	            // Is it a valid joomla installation manifest file?
	            $manifest = $installer->_isManifest($file);

	            if (!is_null($manifest)) {

	                // If the root method attribute is set to upgrade, allow file overwrite
	                $root = $manifest->document;
	                if ($root->attributes('method') == 'upgrade') {
	                    $installer->_overwrite = true;
	                }

	                // Set the manifest object and path
	                $installer->_manifest = $manifest;
	                $installer->setPath('manifest', $file);

	                // Set the installation source path to that of the manifest file
	                $installer->setPath('source', dirname($file));
	            }
	        } else {
	            $this->setError(JText::_('COM_CITRUSCART_UNABLE_TO_LOCATE_MANIFEST_FILE'));
	            return false;
	        }

	        //check if the extension is installed already and if so uninstall it
	        //$manifestInformation = $this->paketItemToManifest($package);
	        $manifestInformation = $package;
	        $elementID = $this->checkIfInstalledAlready($manifestInformation);

	        if (!empty($elementID))
	        {
	        	$clientid = 0;
	        	if ($package['client'] == 'administrator') { $clientid = '1'; }

	            //uninstall the extension using the joomla uninstaller
	            if ($installer->uninstall($manifestInformation["type"], $elementID, $clientid))
	            {
	            	$this->setError(JText::_('COM_CITRUSCART_ELEMENT_UNINSTALLED'));
	            	return true;
	            }
                   else
                {
                    $this->setError(JText::_('COM_CITRUSCART_ELEMENT_UNINSTALL_FAILED')." :: ".$installer->getError());
                    return false;
                }
            }

            $this->setError(JText::_('COM_CITRUSCART_ELEMENT_NOT_UNINSTALLED'));
            return false;
	    }

	    /**
	     * Attempts to find the manifest file stored on the server
	     * @param array $pkg
	     * @return string       path of the xml file for the extension
	     */
	    function findManifest($package)
	    {
	        switch ($package["type"])
	        {
	                // path to module directory
	            case "module":
	            	switch ($package['client'])
	            	{
	            		case "1":
	            		case "administrator":
	            			$moduleBaseDir = JPATH_ADMINISTRATOR."/modules";
	            			break;
	                    case "0":
	                    case "site":
	                    default:
	                        $moduleBaseDir = JPATH_SITE."/modules";
	                        break;

	            	}

	                // xml file for module
	                $xmlfile = $moduleBaseDir . DS . $package['element'] .DS. $package['element'] .".xml";

	                if (file_exists($xmlfile))
	                {
	                    if ($data = JApplicationHelper::parseXMLInstallFile($xmlfile)) {
	                        //return $data;
	                        return $xmlfile;
	                    }
	                }
	                break;
	            case "plugin":
	                // Get the plugin base path
	                $baseDir = JPATH_SITE.'/plugins';

	                // Get the plugin xml file
	                $xmlfile = $baseDir.DS.$package['group'].DS.$package['element'].".xml";

	                if (file_exists($xmlfile)) {
	                    if ($data = JApplicationHelper::parseXMLInstallFile($xmlfile)) {
	                        //return $data;
	                        return $xmlfile;
	                    }
	                }
	                break;
	            case "template":
	                switch ($package['client'])
                    {
                        case "1":
                        case "administrator":
                            $baseDir = JPATH_ADMINISTRATOR."/templates";
                            break;
                        case "0":
                        case "site":
                        default:
                            $baseDir = JPATH_SITE."/templates";
                            break;

                    }

                    // xml file for template
                    $xmlfile = $baseDir . DS . $package['element'] .DS. $package['manifest'] .".xml";

                    if (file_exists($xmlfile))
                    {
                        if ($data = JApplicationHelper::parseXMLInstallFile($xmlfile)) {
                            //return $data;
                            return $xmlfile;
                        }
                    }
	                return null;
	                break;
	            case "language":
	                return null;
	                break;
	            default: //TODO set error here
	            return null;
	        }

	    }

	    /**
	     * Searches the database to see if the given extension is installed already
	     *
	     * @param Array $manifestInformation an array contining the extension type and element name
	     * @return Int 0 if the extension does not exisit, the extension id if it does exist
	     */
	    function checkIfInstalledAlready($manifestInformation) {
	        //select the right query based on the manifest information
	        switch ($manifestInformation["type"]) {
	            case "component":
	                $query = "SELECT `id` FROM #__components WHERE `option` = '".$manifestInformation["element"]."'";
	                break;
	            case "module":
	                $query = "SELECT `id` FROM #__modules WHERE `module` = '".$manifestInformation["element"]."'";
	                break;
	            case "plugin":
	                $query = "SELECT `id` FROM #__plugins WHERE `folder` = '".$manifestInformation["group"]."' AND `element` = '".$manifestInformation["element"]."'";
	                break;
                case "template":
                    return $manifestInformation["element"];
                    break;
	            default:
	                $query = "";
	        }
	        //run the query if it was formed
	        if ($query != "") {
	            $this->_db->setQuery($query);
	            $extension_id = intval($this->_db->loadResult());

	            //return 0 if the extension was not found
	            if (intval($extension_id) < 1) {
	                return 0;
	            }
	            return $extension_id;
	        } else {
	            return 0;
	        }
	    }

		/**
		 * Adds a successfully installed extension to the installed list
		 *
		 * @access private
		 * @param array $result the manifest information of the extension
		 */
		function _addInstalledExtension($result) {
			$this->_installedExtensions[] = $result;
		}

		/**
		 * Retrieves usefull information from an extenstion install manifest file
		 *
		 * @param $installer JInstaller object setup to install an extension
		 * @return Array manifestInformation([type],[group],[element])
		 */
		function getManifestInformation($installer) {
			// Get the extension manifest object
			$manifest = $installer->getManifest();
			$manifestFile = $manifest->document;

			//final information that we need about the extension
			$type = $manifestFile->attributes('type');

			//check to see if the type is component
			if(strcasecmp($type, "component") == 0) {

				// Set the extensions name
				$name = $manifestFile->getElementByPath('name');
				$name = JFilterInput::clean($name->data(), 'cmd');
				$elementName = $name;
			} else {
				//otherwise it is a plugin or module
				$group = $manifestFile->attributes('group');

				$name = $manifestFile->getElementByPath('name');
				$name = JFilterInput::clean($name->data(), 'string');

				//find the actual element name for the database
				$element = $manifestFile->getElementByPath('files');
				if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
					$files = $element->children();
					foreach ($files as $file) {
						if ($file->attributes($type)) {
							$elementName = $file->attributes($type);
							break;
						}
					}
				}
			}

			//create the array of information to return
			$manifestInformation = array();
			$manifestInformation["type"] = @$type;
			$manifestInformation["group"] = @$group;
			$manifestInformation["element"] = @$elementName;

			return $manifestInformation;
		}

	        /**
         * Component: returns the "params" and "enabled" fields of the #__components table
         * Module: returns the "access", "published" and "params" fields of the #__modules table
         * Plugin: returns the "access", "published" and "params" fields of the #__plugins table
         *
         * @access public
         * @param Array $manifestInformation an array of information identifying the extension
         * @return String the information that was in the params row in the database
         */
        function saveParameters($manifestInformation) {
            //select the right query based on the manifest information
            switch ($manifestInformation["type"]) {
                case "component":
                    $query = "SELECT `enabled`,`params` FROM `#__components`".
                            " WHERE `name` = '".$this->_db->escape($manifestInformation["element"])."'";
                    break;
                case "module":
                    $query = "SELECT `access`,`published`,`params` FROM `#__modules`".
                            " WHERE `module` = '".$this->_db->escape($manifestInformation["element"])."'";
                    break;
                case "plugin":
                    $query = "SELECT `access`,`published`,`params` FROM `#__plugins`".
                            " WHERE `folder` = '".$this->_db->escape($manifestInformation["group"])."' &&  ".
                            "`element` = '".$this->_db->escape($manifestInformation["element"])."'";
                    break;
                default:
                    $query = "";
            }
            //run the query if it was formed
            if ($query != "") {
                $this->_db->setQuery($query);
                $savedParameters = $this->_db->loadObject();
                return $savedParameters;
            } else {
                return (object) array();
            }
        }

        /**
         * Restores the parameters saved of a given extension in the database
         *
         * @access public
         * @param Array $manifestInformation the infomration identidying the extension
         * @param String $savedParameters the previously saved parameters
         */
        function restoreParameters($manifestInformation, $savedParameters)
        {
            // Load the new settings
            switch ($manifestInformation["type"]) {
                case "component":
                    $qry_load = "SELECT * FROM `#__components`".
                                " WHERE `name` = '".$this->_db->escape($manifestInformation["element"])."'";

                    break;
                case "module":
                    $qry_load = "SELECT * FROM `#__modules`".
                                " WHERE `module` = '".$this->_db->escape($manifestInformation["element"])."'";

                    break;
                case "plugin":
                    $qry_load = "SELECT * FROM `#__plugins`".
                                " WHERE `folder` = '".$this->_db->escape($manifestInformation["group"])."' && ".
                                "`element` = '".$this->_db->escape($manifestInformation["element"])."'";

                    break;
                default:
                    return;
            }

            // Load new parameters from the DB
            $this->_db->setQuery($qry_load);
            $obj = $this->_db->loadObject();

            // enabled: keep the old parameter
            // access: keep the old parameter
            // published: keep the old parameter
            // params: merge (older is more important than defaut new)

            // Converting to Object Format
            $new_params = DSCParameterFormatINI::stringToObject($obj->params);
            $old_params = DSCParameterFormatINI::stringToObject($savedParameters->params);

            $old_params = (object) array_merge((array) $new_params, (array) $old_params);

            // Converting back to INI format
            $savedParameters->params = DSCParameterFormatINI::object__toString($old_params, '');


            // Save the merged new / old settings
            switch ($manifestInformation["type"]) {
                case "component":
                    $qry_save = "UPDATE `#__components` SET ".
                                "`enabled`=".intval($savedParameters->enabled).", ".
                                "`params` = '".$this->_db->escape($savedParameters->params)."'".
                                " WHERE `name` = '".$manifestInformation["element"]."'";

                    break;

                case "module":
                    $qry_save = "UPDATE `#__modules` SET ".
                                "`access` = ".intval($savedParameters->access).", ".
                                "`published` = ".intval($savedParameters->published).", ".
                                "`params` = '".$this->_db->escape($savedParameters->params)."'".
                                " WHERE `module` = '".$this->_db->escape($manifestInformation["element"])."'";

                    break;

                case "plugin":
                    $qry_save = "UPDATE `#__plugins` SET ".
                                "`access` = ".intval($savedParameters->access).", ".
                                "`published` = ".intval($savedParameters->published).", ".
                                "`params` = '".$this->_db->escape($savedParameters->params)."'".
                                " WHERE `folder` = '".$this->_db->escape($manifestInformation["group"])."' && ".
                                "`element` = '".$this->_db->escape($manifestInformation["element"])."'";
                    break;
                default:
                    return;
            }

            $this->_db->setQuery($qry_save);
            $this->_db->query();
        }

		/**
		 * Tricks joomla so as to not run the custom unistall script of a component during an uninstall
		 *
		 * @access public
		 * @param JInstaller $installer the intaller of the current component
		 */
		function preventCustomUninstall(&$installer) {
			//Get the extension manifest object
			$manifest = $installer->getManifest();
			$manifestFile = $manifest->document;

			//Cleverly remove the XML containing custom uninstall information
			$uninstaller = $manifestFile->getElementByPath('uninstall');
			$manifestFile->removeChild($uninstaller);
		}

		/**
		 * Automatically publishes any extensions that are not in the donotpublish list
		 * any components in the donotpublish list will be un-enabled because they are enabled by default on install
		 *
		 * @access public
		 * @param $manifestInformation Array an array contining the extension type and element name
		 * @return void
		 */
		function publishExtension($manifestInformation) {
			switch ($manifestInformation["type"]) {
				case "component":
					//components auto publish but check if any should be unplublished
					if (in_array($manifestInformation["element"], $this->_doNotPublishList)) {
						$query = "UPDATE #__components SET `enabled` = '0' WHERE `name` = '".$manifestInformation["element"]."'";
					}
					break;
				case "module":
					//check to make sure the extension should be auto published
					if (!in_array($manifestInformation["element"], $this->_doNotPublishList)) {
						$query = "UPDATE #__modules SET `published` = '1' WHERE `module` = '".$manifestInformation["element"]."'";
					}
					break;
				case "plugin":
					//check to make sure the extension should be auto published
					if (!in_array($manifestInformation["element"], $this->_doNotPublishList)) {
						$query = "UPDATE #__plugins SET `published` = '1' WHERE `folder` = '".$manifestInformation["group"]."' AND `element` = '".$manifestInformation["element"]."'";
					}
					break;
				default:
					$query = "";
			}
			//run the query if it was formed
			if ($query != "") {
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}

		/**
		 * Formats the output message that reports the results of the install
		 *
		 * @access private
		 * @return void
		 */
		function _formatMessage() {
			if (sizeof($this->_installedExtensions) > 0) {
				$this->msg->type = 'message';
				$this->msg->message = JText::_('COM_CITRUSCART_INSTALLATION_COMPLETED');
				foreach ($this->_installedExtensions as $extension) {
					$this->msg->message .= "</li>";
					$this->msg->message .= "<li>".JText::_('COM_CITRUSCART_INSTALLED')." ".$extension["type"]." ".$extension["element"];
				}
				$this->msg->message .= "</li>";
			} else {
				$this->msg->type = 'notice';
				$this->msg->message = JText::_('COM_CITRUSCART_NOTHING_INSTALLED');
			}
		}

		/**
		 * Returns a reference to the joomla message object that was formed
		 *
		 * @return Object the joomla message object
		 */
		function &getMessage() {
			return $this->msg;
		}

		/**
		 * Sets the path where the package files are
		 *
		 * @access	public
		 * @param	string	$value	Path
		 * @return	void
		 */
		function setExtensionsPath($value)
		{
			$this->_extensionsPath = $value;
		}

		/**
		 * Get the extensions path
		 *
		 * @access	public
		 * @return	string	extensionsPath
		 */
		function getExtensionsPath()
		{
			if (!empty($this->_extensionsPath)) {
				return $this->_extensionsPath;
			} else {
				return null;
			}
		}

		/**
		 * Set the save parameters switch
		 *
		 * @access public
		 * @param boolean $state save parameters switch state
		 * @return boolean the state of the switch
		 */
		function setSaveParameters($state=true) {
			$this->_saveParameters = $state;
			return $this->_saveParameters;
		}

		/**
		 * Get the save parameters switch
		 *
		 * @access	public
		 * @return	boolean	save parameters switch
		 */
		function getSaveParameters()
		{
			return $this->_saveParameters;
		}

		/**
		 * Set the prevent uninstall script switch
		 *
		 * @access public
		 * @param boolean $state prevent uninstall script switch state
		 * @return boolean the state of the switch
		 */
		function setPreventUninstallScript($state=true) {
			$this->_preventUninstallScript = $state;
			return $this->_preventUninstallScript;
		}

		/**
		 * Get the prevent uninstall script switch
		 *
		 * @access	public
		 * @return	boolean	prevent uninstall script switch
		 */
		function getPreventUninstallScript()
		{
			return $this->_preventUninstallScript;
		}

		/**
		 * Set the keep local copy switch
		 *
		 * @access public
		 * @param boolean $state keep local copy switch state
		 * @return boolean the state of the switch
		 */
		function setKeepLocalCopy($state=false) {
			$this->_keepLocalCopy = $state;
			return $this->_keepLocalCopy;
		}

		/**
		 * Get the keep local copy switch
		 *
		 * @access	public
		 * @return	boolean	keep local copy switch
		 */
		function getKeepLocalCopy()
		{
			return $this->_keepLocalCopy;
		}

		/**
		 * Set the do not publish array
		 *
		 * @access	public
		 * @param	array $list	list of extensions not to publish
		 * @return	void
		 */
		function setDoNotPublishList($list)
		{
			$this->_doNotPublishList = $list;
		}

		/**
		 * Get the do not publish list
		 *
		 * @access	public
		 * @return	array
		 */
		function getDoNotPublishList()
		{
			if (!empty($this->_doNotPublishList)) {
				return $this->_doNotPublishList;
			} else {
				return null;
			}
		}

	}
}