<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class DSC extends JObject
{
    protected $_name 		= 'dsc';
    protected $_version 		= '2.0';
	protected $_build          = '';
	protected $_versiontype    = '';
	protected $_copyrightyear 	= '2014 - 2019';
	protected $_min_php		= '5.3';

	/**
	* constructor
	* @return void
	*/
	public function __construct()
	{
	    parent::__construct();
	    $this->setVariables();
	}

	/**
	* Get the version
	*/
	public function getVersion()
	{
		return $this->get('_version');
	}
	/**
	* Get the version
	*/
	public function getVersionType()
	{
		return $this->get('_versiontype');
	}

	/**
	 * Get the full version string
	 */
	public function getFullVersion()
	{
		$version = $this->getVersion()." ".JText::_( ucfirst($this->getVersionType()) )." ".$this->getBuild();
		return $version;
	}

	/**
	* Get the copyright year
	*/
	public function getBuild()
	{
		return $this->get('_build');
	}

	/**
	 * Get the copyright year
	 */
	public function getCopyrightYear()
	{
		return $this->get('_copyrightyear');
	}

	/**
	 * Get the Name
	 */
	public function getName()
	{
	    return $this->get('_name');
	}

	/**
	 * Get the Minimum Version of Php
	 */
	public function getMinPhp()
	{
		//get version from PHP. Note this should be in format 'x.x.x' but on some systems will look like this: eg. 'x.x.x-unbuntu5.2'
		$phpV = $this->getServerPhp();
		$minV = $this->_min_php;
		$passes = false;

		if ($phpV[0] >= $minV[0]) {
			if (empty($minV[2]) || $minV[2] == '*') {
				$passes = true;
			} elseif ($phpV[2] >= $minV[2]) {
				if (empty($minV[4]) || $minV[4] == '*' || $phpV[4] >= $minV[4]) {
					$passes = true;
				}
			}
		}
		//if it doesn't pass raise a Joomla Notice
		if (!$passes) :
		JError::raiseNotice('VERSION_ERROR',sprintf(JText::_('ERROR_PHP_VERSION'),$minV,$phpV));
		endif;

		//return minimum PHP version
		return $this->_min_php;
	}

	public function getServerPhp()
	{
		return PHP_VERSION;
	}

	public static function getApp( $app=null, $find=true )
	{
		$input = JFactory::getApplication()->input;

		if (empty($app) && empty($find))
		{
			return new DSC();
		}

		if (empty($app) && !empty($find))
		{
			$app = $input->get('option');
			//$app = JRequest::get('option');
		}

		if (strpos($app, 'com_') !== false) {
			$app = str_replace( 'com_', '', $app );
		}

		if ( !class_exists($app) ) {
			JLoader::register( $app, JPATH_ADMINISTRATOR."/components/com_" . $app . "/defines.php" );
		}
		if ( class_exists($app) ) {
			return $app::getInstance();
		} else {
			return null;
		}

	}

	/**
	* Get the URL to the folder containing all media assets
	*
	* @param string	$type	The type of URL to return, default 'media'
	* @return 	string	URL
	*/
	public static function getURL( $type = 'media', $com='' )
	{
	    $name = 'citruscart';
	    if (!empty($com)) {
	        $app = self::getApp($com);
	        $name = "com_" . $app->getName();
	    }

	    $url = '';

	    switch ( $type )
	    {
	        case 'media':
	            $url = JURI::root( true ) . '/media/'.$name.'/';
	            break;
	        case 'css':
	            $url = JURI::root( true ) . '/media/'.$name.'/css/';
	            break;
	        case 'images':
	            $url = JURI::root( true ) . '/media/'.$name.'/images/';
	            break;
	        case 'js':
	            $url = JURI::root( true ) . '/media/'.$name.'/js/';
	            break;
	    }

	    return $url;
	}

	/**
	 * Get the path to the folder containing all media assets
	 *
	 * @param 	string	$type	The type of path to return, default 'media'
	 * @return 	string	Path
	 */
	public static function getPath( $type = 'media', $com='' )
	{
	    $name = 'citruscart';
	    if (!empty($com)) {
	        $app = self::getApp($com);
	        $name = "com_" . $app->getName();
	    }

	    $path = '';

	    switch ( $type )
	    {
	        case 'media':
	            $path = JPATH_SITE . '/media/'.$name;
	            break;
	        case 'css':
	            $path = JPATH_SITE . '/media/'.$name.'/css';
	            break;
	        case 'images':
	            $path = JPATH_SITE . '/media/'.$name.'/images';
	            break;
	        case 'js':
	            $path = JPATH_SITE . '/media/'.$name.'/js';
	            break;
	    }

	    return $path;
	}

	/**
	 *
	 * Enter description here ...
	 */
	public static function loadLibrary( $load_js=true )
	{
		if (!class_exists('DSCLoader')) {
			jimport('joomla.filesystem.file');
			if (!JFile::exists(JPATH_SITE.'/libraries/dioscouri/loader.php')) {
				return false;
			}
			require_once JPATH_SITE.'/libraries/dioscouri/loader.php';
		}

		if (!defined('_DSC'))
		{
		    define('_DSC', 1);

		    $parentPath = JPATH_SITE . '/libraries/dioscouri/library';
		    DSCLoader::discover('DSC', $parentPath, true);

		    $autoloader = new DSCLoader();

			if ($load_js)
		    {
    		    $doc = JFactory::getDocument( );
    		    $uri = JURI::getInstance( );
    		    $js = "Dsc.jbase = '" . $uri->root( ) . "';\n";
    		    $doc->addScript(JUri::root().'media/citruscart/js/common.js');
    		    //$doc->addScript( DSC::getURL('js') . 'common.js' );
    		    $doc->addScriptDeclaration( $js );
		    }
		}

		return true;
	}

	/**
	* Adds the Highcharts library files to the autoloader
	* and adds the highcharts js file to the stack
	*
	*/
	public static function loadHighcharts()
	{
		static $loaded = false;

		if( $loaded )
			return;

	    jimport('dioscouri.highroller.highroller.highroller');
	    jimport('dioscouri.highroller.highroller.highrollerareachart');
	    jimport('dioscouri.highroller.highroller.highrollerareasplinechart');
	    jimport('dioscouri.highroller.highroller.highrollerbarchart');
	    jimport('dioscouri.highroller.highroller.highrollercolumnchart');
	    jimport('dioscouri.highroller.highroller.highrollerlinechart');
	    jimport('dioscouri.highroller.highroller.highrollerpiechart');
	    jimport('dioscouri.highroller.highroller.highrollerscatterchart');
	    jimport('dioscouri.highroller.highroller.highrollerseriesdata');
	    jimport('dioscouri.highroller.highroller.highrollersplinechart');

	    DSC::loadJQuery();
	    JHTML::_( 'script', 'highcharts.js', 'libraries/dioscouri/highroller/highcharts/' );
		$load = false;
	}

	/**
	 * Loads JQuery
	 *
	 */
	public static function loadJQuery($version='latest', $noConflict=true, $alias=null)
	{
		static $loaded;

		if (empty($loaded)) {
		    $loaded = array();
		}

		if ( !empty($loaded[$alias]) ) {
			return true;
		}

	    switch($version)
	    {
	        case "latest":
	        default:
	            JHTML::_( 'script', 'jquery-1.8.3.min.js', 'media/citruscart/jquery/core/' );
	            break;
	    }


	    if ($noConflict)
	    {
	        $document = JFactory::getDocument();
	        $script = "jQuery.noConflict();";
	        if (!empty($alias)) {
	            $script = "var $alias = jQuery.noConflict();";
	        }
	        $document->addScriptDeclaration( $script );
	    }

	    $return = new JObject();
	    $return->version = $version;
	    $return->noConflict = $noConflict;
	    $return->alias = $alias;

	    $loaded[$alias] = $return;

	    return $loaded[$alias];
	}

	/**
	 *
	 * @param string $version
	 * @param int $joomla
	 * @param unknown_type $responsive
	 */
	public static function loadBootstrap($version='default', $joomla=true, $responsive=false )
	{
        // short term backwards compatibility.  Update your components
        if ((is_int($version) && in_array($version, array(0,1))) || (strlen($joomla) > 1))
        {
            $org_version = $version;
            $org_joomla = $joomla;
            $version = $org_joomla;
            $joomla = $org_version;
        }

	    static $loaded = false;

	    if ( $loaded ) {
	        return;
	    }

	    DSC::loadJQuery();
	    $doc = JFactory::getDocument();

	    $doc->addStyleSheet(JUri::root().'media/citruscart/bootstrap/'.$version.'/css/bootstrap.min.css');
	    //$doc->addScript(JUri::root().'media/citruscart/bootstrap/'.$version.'/js/bootstrap.min.js');

	   // JHTML::_( 'script', 'bootstrap.min.js', 'media/citruscart/bootstrap/'.$version.'/js/' );
	 //   JHTML::_( 'stylesheet', 'bootstrap.min.css', 'media/citruscart/bootstrap/'.$version.'/css/' );

	    if ($joomla) {
	        //JHTML::_( 'stylesheet', 'joomla.bootstrap.css', 'media/citruscart/css/' );

	        $doc->addStyleSheet(JUri::root().'media/citruscart/css/joomla.bootstrap.css');
	    }

	    if ($responsive) {
	    	$doc->addStyleSheet(JUri::root().'media/citruscart/bootstrap/'.$version.'/css/bootstrap-responsive.min.css');
	        //JHTML::_( 'stylesheet', 'bootstrap-responsive.min.css', 'media/citruscart/bootstrap/'.$version.'/css/' );
	    }

	    $loaded = true;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
    private static function _dump( $var, $ignore_underscore = true, $public_only=true )
    {
    	$data = print_r( $var, true );
    	//return $data;

    	$lines = explode("\n", $data);
    	$key = 0;

    	//foreach ($lines as $key=>$line)
    	while (isset($lines[$key]))
    	{
    	    $line = $lines[$key];
    	    $is_protected = false;
    	    if (strpos($line, ':protected]') !== false)
    	    {
    	        $is_protected = true;
    	    }

    	    if ($is_protected && $public_only)
    	    {
    	        // unset this one
    	        unset($lines[$key]);

    	        // is this an array or object?
    	        // if so, unset all the next lines until the array/object is done

    	        $nextkey = $key + 1;
    	        if (trim($lines[$nextkey]) == '(')
    	        {
    	            // count the spaces at the beginning of the line
    	            $count = substr_count(rtrim($lines[$nextkey]), ' ');

    	            unset($lines[$nextkey]);
    	            $key = $nextkey;

    	            $next_line_key = $nextkey + 1;
    	            $next_line_space_count = substr_count(rtrim($lines[$next_line_key]), ' ');

                    while (trim($lines[$next_line_key]) != ')' || $next_line_space_count != $count)
                    {
                        unset($lines[$next_line_key]);
                        $next_line_key = $next_line_key + 1;
                        $next_line_space_count = substr_count(rtrim($lines[$next_line_key]), ' ');
                    }

                    if (trim($lines[$next_line_key]) == ')' && $next_line_space_count == $count)
                    {
                        unset($lines[$next_line_key]);
                        $key = $next_line_key;
                    }
    	        }
    	    }

    	    $key++;
    	}

    	foreach ($lines as $key=>$line)
    	{
    	    if (empty($lines[$key])) {
    	        unset($lines[$key]);
    	    }
    	}

    	$out = implode("\n", $lines) . "\n";
    	return $out;
    }

	/**
	* Method to dump the structure of a variable for debugging purposes
	*
	* @param	mixed	A variable
	* @param	boolean	True to ensure all characters are htmlsafe
	* @return	string
	* @since	1.5
	* @static
	*/
	public static function dump( $var, $public_only = true, $htmlSafe = true )
	{
	    $arg_list = func_get_args();
	    $numargs = func_num_args();
	    $ignore_underscore = true;
	    if ($numargs == 4) {
	        $ignore_underscore = $arg_list[3];
	    }

	    if (!$public_only)
	    {
	        $result = self::_dump( $var, $public_only, $ignore_underscore );
	        return '<pre>'.( $htmlSafe ? htmlspecialchars( $result ) : $result).'</pre>';
	    }

	    if (!is_object($var) && !is_array($var))
	    {
	        $result = self::_dump( $var, $ignore_underscore, $public_only );
	        return '<pre>'.( $htmlSafe ? htmlspecialchars( $result ) : $result).'</pre>';
	    }

	    // TODO do a recursive remove of underscored keys, rather than only two levels
	    if (is_object($var))
	    {
	        $keys = get_object_vars($var);
	        foreach ($keys as $key=>$value)
	        {
	            if (substr($key, 0, 1) == '_' )
	            {
	                unset($var->$key);
	            }
	            else
	            {
	                if (is_object($var->$key))
	                {
	                    $sub_keys = get_object_vars($var->$key);
	                    foreach ($sub_keys as $sub_key=>$sub_key_value)
	                    {
	                        if (substr($sub_key, 0, 1) == '_')
	                        {
	                            unset($var->$key->$sub_key);
	                        }
	                    }
	                }
	                elseif (is_array($var->$key))
	                {
	                    foreach ($var->$key as $sub_key=>$sub_key_value)
	                    {
	                        if (substr($sub_key, 0, 1) == '_')
	                        {
	                            unset($var->$key[$sub_key]);
	                        }
	                    }
	                }
	            }


	        }
	        $result = self::_dump( $var, $ignore_underscore, $public_only );
	        return '<pre>'.( $htmlSafe ? htmlspecialchars( $result ) : $result).'</pre>';
	    }

	    if (is_array($var))
	    {
	        foreach ($var as $key=>$value)
	        {
	            if (substr($key, 0, 1) == '_')
	            {
	                unset($var[$key]);
	            }
	            else
	            {
	                if (is_object($var[$key]))
	                {
	                    $sub_keys = get_object_vars($var[$key]);
	                    foreach ($sub_keys as $sub_key=>$sub_key_value)
	                    {
	                        if (substr($sub_key, 0, 1) == '_')
	                        {
	                            unset($var[$key]->$sub_key);
	                        }
	                    }
	                }
	                elseif (is_array($var[$key]))
	                {
	                    foreach ($var[$key] as $sub_key=>$sub_key_value)
	                    {
	                        if (substr($sub_key, 0, 1) == '_')
	                        {
	                            unset($var[$key][$sub_key]);
	                        }
	                    }
	                }
	            }
	        }
	        $result = self::_dump( $var, $ignore_underscore, $public_only );
	        return '<pre>'.( $htmlSafe ? htmlspecialchars( $result ) : $result).'</pre>';
	    }
	}

	/**
	 * Method to intelligently load class files in the framework
	 *
	 * @param string $classname   The class name
	 * @param string $filepath    The filepath ( dot notation )
	 * @param array  $options
	 * @return boolean
	 */
	public static function load( $classname, $filepath='library', $options=array( 'site'=>'site', 'type'=>'libraries', 'ext'=>'dioscouri' ) )
	{
	    $classname = strtolower( $classname );
	    if(version_compare(JVERSION,'1.6.0','ge')) {
	        // Joomla! 1.6+ code here
	        $classes = JLoader::getClassList();
	    } else {
	        // Joomla! 1.5 code here
	        $classes = JLoader::register();
	    }

	    if ( class_exists($classname) || array_key_exists( $classname, $classes ) )
	    {
	        // echo "$classname exists<br/>";
	        return true;
	    }

	    static $paths;

	    if (empty($paths))
	    {
	        $paths = array();
	    }

	    if (empty($paths[$classname]) || !is_file($paths[$classname]))
	    {
	        // find the file and set the path
	        if (!empty($options['base']))
	        {
	            $base = $options['base'];
	        }
	        else
	        {
	            // recreate base from $options array
	            switch ($options['site'])
	            {
	                case "site":
	                    $base = JPATH_SITE.'/';
	                    break;
	                default:
	                    $base = JPATH_ADMINISTRATOR.'/';
	                break;
	            }

	            $base .= (!empty($options['type'])) ? $options['type'].'/' : '';
	            $base .= (!empty($options['ext'])) ? $options['ext'].'/' : '';
	        }

	        $paths[$classname] = $base.str_replace( '.', '/', $filepath ).'.php';
	    }

	    // if invalid path, return false
	    if (!is_file($paths[$classname]))
	    {
	        // echo "file does not exist<br/>";
	        return false;
	    }

	    // if not registered, register it
	    if ( !array_key_exists( $classname, $classes ) )
	    {
	        // echo "$classname not registered, so registering it<br/>";
	        JLoader::register( $classname, $paths[$classname] );
	        return true;
	    }
	    return false;
	}

	/**
	 * Intelligently loads instances of classes in framework
	 *
	 * Usage: $object = DSC::getClass( 'DSCHelperCarts', 'helpers.carts' );
	 * Usage: $suffix = DSC::getClass( 'DSCHelperCarts', 'helpers.carts' )->getSuffix();
	 * Usage: $categories = DSC::getClass( 'DSCSelect', 'select' )->category( $selected );
	 *
	 * @param string $classname   The class name
	 * @param string $filepath    The filepath ( dot notation )
	 * @param array  $options
	 * @return object of requested class (if possible), else a new JObject
	 */
	public static function getClass( $classname, $filepath='library', $options=array( 'site'=>'site', 'type'=>'libraries', 'ext'=>'dioscouri' )  )
	{
	    if (self::load( $classname, $filepath, $options ))
	    {
	        $instance = new $classname();
	        return $instance;
	    }

	    $instance = new JObject();
	    return $instance;
	}

	/**
	* Returns the query
	* @return string The query to be used to retrieve the config data from the database
	*/
	public function _buildQuery()
	{
	    $query = "";
	    //$query = "SELECT * FROM #__component_config";
	    return $query;
	}

	/**
	* Retrieves the data
	* @return array Array of objects containing the data from the database
	*/
	public function getData()
	{
	    // load the data if it doesn't already exist
	    if (empty( $this->_data ))
	    {
	        $this->_data = '';
	        $database = JFactory::getDBO();
	        if ($query = $this->_buildQuery())
	        {
    	        $database->setQuery( $query );
    	        $this->_data = $database->loadObjectList();
	        }
	    }

	    return $this->_data;
	}

	/**
	 * Set Variables
	 *
	 * @acces	public
	 * @return	object
	 */
	public function setVariables()
	{
	    $success = false;

	    /*
	    $classname = strtolower( get_class($this) );
	    $cache = JFactory::getCache( 'com_' . $classname . '.defines' );
	    $cache->setCaching(true);
	    $cache->setLifeTime('3600');
	    $data = $cache->call(array($this, 'getData'));
	    */

	    $data = $this->getData();

	    if ( !empty($data) )
	    {
	    	$count = count($data);
	        for ($i=0; $i<$count; $i++)
	        {
	            $title = $data[$i]->config_name;
	            $value = $data[$i]->value;
	            if (!empty($title)) {
	                $this->$title = $value;
	            }
	        }

	        $success = true;
	    }

	    return $success;
	}

  /**
   * Finds out if a component is installed
   *
   * @param string $option
   *
   * @return  True or False based on if the component exists or not
   */
  public function isComponentInstalled($option)
  {
		if(version_compare(JVERSION,'1.6.0','ge')) {
	        // Joomla! 1.6+ code here
          $db = JFactory::getDBO();
          $q = new DSCQuery();
          $q->select( 'extension_id' );
          $q->from( '#__extensions' );
          $q->where( 'type = \'component\'' );
          $q->where( 'enabled = 1' );
          $q->where( 'element = '.$db->Quote( $option ) );
          $db->setQuery( $q );
          $res = $db->loadObject();
          return $res !== null;
	    } else {
	        // Joomla! 1.5 code here
          return JComponentHelper::getComponent( $option, true)->enabled;
	    }
  }
}

