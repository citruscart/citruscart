<?php
/**
* @package	Dioscouri Library
* @author 	Dioscouri Design
* @link 	http://www.dioscouri.com
* @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die;

class DSCLoader extends JLoader
{
    public function __construct()
    {
        // Register DSCLoader::load as an autoload class handler.
        spl_autoload_register(array($this, 'load'));
    }

	/**
	* Method to recursively discover classes of a given type in a given path.
	*
	* @param   string   $classPrefix  The class name prefix to use for discovery.
	* @param   string   $parentPath   Full path to the parent folder for the classes to discover.
	* @param   boolean  $force        True to overwrite the autoload path value for the class if it already exists.
	*
	* @return  void
	*
	* @since   11.1
	*/
	public static function discover($classPrefix, $parentPath, $force = true, $recurse = false)
	{
		$excluded_dirs = array( '.', '..' );

		// Ignore the operation if the folder doesn't exist.
		if (is_dir($parentPath)) {

			// Open the folder.
			$d = dir($parentPath);

			// Iterate through the folder contents to search for input classes.
			while (false !== ($entry = $d->read()))
			{
				if (!in_array($entry, $excluded_dirs) && is_dir($parentPath.'/'.$entry)) {
					self::discover($classPrefix.$entry, $parentPath.'/'.$entry, $force, $recurse);
				} else {
					// Only load for php files.
					if (file_exists($parentPath.'/'.$entry) && (substr($entry, strrpos($entry, '.') + 1) == 'php')) {

						// Get the class name and full path for each file.
						$class = strtolower($classPrefix.preg_replace('#\.[^.]*$#', '', $entry));
						$path  = $parentPath.'/'.$entry;

						// Register the class with the autoloader if not already registered or the force flag is set.
						if(version_compare(JVERSION,'1.6.0','ge')) {
						    // Joomla! 1.6+ code here
						    self::register($class, $path, $force);
						} else {
						    // Joomla! 1.5 code here
						    JLoader::register($class, $path);
						}
					}
				}
			}

			// Close the folder.
			$d->close();
		}
	}
}
