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

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

if ( !class_exists('Citruscart') )
JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

Citruscart::load( 'CitruscartConfig', 'defines' );

require_once(JPATH_SITE.'/libraries/dioscouri/library/helper.php');

class CitruscartHelperBase extends DSCHelper
{
		static $added_strings = null;

	/**
	 * constructor
	 * make it protected where necessary
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Returns a reference to the a Helper object, only creating it if it doesn't already exist
	 *
	 * @param type 		$type 	 The helper type to instantiate
	 * @param string 	$prefix	 A prefix for the helper class name. Optional.
	 * @return helper The Helper Object
	 */
	public static function getInstance( $type = 'Base', $prefix = 'CitruscartHelper' )
	{
		//parent::getInstance( $type , $prefix );

		 static $instances;

		if (!isset( $instances )) {
			$instances = array();
		}

		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);

		// The Base helper is in _base.php, but it's named CitruscartHelperBase
		if(strtolower($type) == 'Base'){
			$helperClass = $prefix.ucfirst($type);
			$type = '_Base';
		}

		$helperClass = $prefix.ucfirst($type);

		if (empty($instances[$helperClass]))
		{

			if (!class_exists( $helperClass ))
			{
				jimport('joomla.filesystem.path');
				if($path = JPath::find(CitruscartHelperBase::addIncludePath(), strtolower($type).'.php'))
				{
					require_once $path;

					if (!class_exists( $helperClass ))
					{
						JError::raiseWarning( 0, 'Helper class ' . $helperClass . ' not found in file.' );
						return false;
					}
				}
				else
				{
					JError::raiseWarning( 0, 'Helper ' . $type . ' not supported. File not found.' );
					return false;
				}
			}

			$instance = new $helperClass();

			$instances[$helperClass] = $instance;
		}

		return $instances[$helperClass];
	}

	/**
	 * Check if the path exists, and if not, tries to create it
	 * @param string $dir
	 * @param bool $create
	 */
	/*function checkDirectory($dir, $create = true)
	{
		$return = true;
		if (!$exists = JFolder::exists( $dir ) )
		{
			if ($create)
			{
				if (!$return = JFolder::create( $dir ))
				{
					$this->setError( "Attempted to Create Dir But Failed" );
					//JFactory::getApplication( )->enqueueMessage( JText::_('COM_CITRUSCART_CREATE_DIR_FAILED') . " " . $dir );
				}
			}
			else
			{
				$return = false;
				$this->setError( "Dir Does Not Exist and Did Not Attempt to Create" );
				//JFactory::getApplication( )->enqueueMessage( JText::_('COM_CITRUSCART_DIR_DOES_NOT_EXIST_NOT_CREATED') . " " . $dir );
			}
		}

		if (!is_writable($dir))
		{
			if (!$change = JPath::setPermissions( $dir ))
			{
				$this->setError( "Changing Permissions on Dir Failed" );
				//JFactory::getApplication( )->enqueueMessage( JText::_('COM_CITRUSCART_CHANGING_DIR_PERMISSIONS_FAILED') . " " . $dir );
			}
		}

		return $return;
	}

	/**
	 * Determines whether/not a user can view a record
	 *
	 * @param $id					id of commission
	 * @param $userid [optional] 	If absent, current logged-in user is used
	 * @return boolean
	 */
	/*function canView( $id, $userid=null )
	{
		$result = false;

		$user = JFactory::getUser( $userid );
		$userid = intval($user->id);

		// if the user is super admin, yes
		if ($user->gid == '25') { return true; }

		return $result;
	}

	/**
	 * Add a directory where CitruscartHelper should search for helper types. You may
	 * either pass a string or an array of directories.
	 *
	 * @access	public
	 * @param	string	A path to search.
	 * @return	array	An array with directory elements
	 * @since 1.5
	 */
	static function addIncludePath( $path=null )
	{
		static $CitruscartHelperPaths;

		if (!isset($CitruscartHelperPaths)) {
			$CitruscartHelperPaths = array( dirname( __FILE__ ) );
		}

		// just force path to array
		settype($CitruscartHelperPath, 'array');

		if (!empty( $CitruscartHelperPath ) && !in_array( $CitruscartHelperPath, $CitruscartHelperPaths ))
		{
			// loop through the path directories
			foreach ($CitruscartHelperPath as $dir)
			{
				// no surrounding spaces allowed!
				$dir = trim($dir);

				// add to the top of the search dirs
				// so that custom paths are searched before core paths
				array_unshift($CitruscartHelperPaths, $dir);
			}
		}
		return $CitruscartHelperPaths;
	}

	/**
	 * Formats and converts a number according to currency rules
	 * As of v0.5.0 is a wrapper
	 *
	 * @param unknown_type $amount
	 * @param unknown_type $currency
	 * @return unknown_type
	 */
	public static function currency($amount, $currency='', $options='')
	{

		$currency_helper = CitruscartHelperBase::getInstance( 'Currency' );
		$amount = $currency_helper->_($amount, $currency, $options);
		return $amount;
	}

	/**
	 * Return a mesure with its unit
	 * @param float $amount
	 * @param string $type could be dimension or weight
	 */
	/*function measure($amount, $type='dimension')
	{
		// default to whatever is in config

		$config = CitruscartConfig::getInstance();
		$dim_unit = $config->get('dimensions_unit', 'cm');
		$weight_unit = $config->get('weight_unit', 'kg');

		if(strtolower($type) == 'dimension'){
			return $amount.$dim_unit;
		} else{
			return $amount.$weight_unit;
		}

	}

	/**
	 * Nicely format a number
	 *
	 * @param $number
	 * @return unknown_type
	 */
	public static function number($number, $options='' )
	{
		static $default_currency = null;
		$config = CitruscartConfig::getInstance();
		$options = (array) $options;
    if ( !$default_currency )
    {
			JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
			$model = JModelLegacy::getInstance('Currencies', 'CitruscartModel');
     	$model->SetId(CitruscartConfig::getInstance()->get('default_currencyid', '1'));
     	$default_currency = $model->getItem();
		}

		$thousands = isset($options['thousands']) ? $options['thousands'] : $default_currency->thousands_separator;
		$decimal = isset($options['decimal']) ? $options['decimal'] : $default_currency->decimal_separator;
		$num_decimals = isset($options['num_decimals']) ? $options['num_decimals'] : $default_currency->currency_decimals;
		$return = number_format($number, $num_decimals, $decimal, $thousands);
		return $return;
	}	

	/**
	 * Extracts a column from an array of arrays or objects
	 *
	 * @static
	 * @param	array	$array	The source array
	 * @param	string	$index	The index of the column or name of object property
	 * @return	array	Column of values from the source array
	 * @since	1.5
	 */
	/*function getColumn(&$array, $index)
	{
		$result = array();

		if (is_array($array))
		{
			foreach (@$array as $item)
			{
				if (is_array($item) && isset($item[$index]))
				{
					$result[] = $item[$index];
				}
				elseif (is_object($item) && isset($item->$index))
				{
					$result[] = $item->$index;
				}
			}
		}
		return $result;
	}

	/**
	 * Takes an elements object and converts it to an array that can be binded to a JTable object
	 *
	 * @param $elements is an array of objects with ->name and ->value properties, all posted from a form
	 * @return array[name] = value
	 */
	/*function elementsToArray( $elements )
	{
		$return = array();
		$names = array();
		$checked_items = array();
		if (empty($elements))
		{
			$elements = array();
		}

		foreach (@$elements as $element)
		{
			$isarray = false;
			$name = $element->name;
			$value = $element->value;
			$checked = $element->checked;

			// if the name is an array,
			// attempt to recreate it
			// using the array's name
			if (strpos($name, '['))
			{
				$isarray = true;
				$search = array( '[', ']' );
				$exploded = explode( '[', $name, '2' );
				$index = str_replace( $search, '', $exploded[0]);
				$name = str_replace( $search, '', $exploded[1]);
				if (!empty($index))
				{
					// track the name of the array
					if (!in_array($index, $names))
					{
						$names[] = $index;
					}

					if (empty(${$index}))
					{
						${$index} = array();
					}

					if (!empty($name))
					{
						${$index}[$name] = $value;
					}
					else
					{
						${$index}[] = $value;
					}

					if ($checked)
					{
						if (empty($checked_items[$index]))
						{
							$checked_items[$index] = array();
						}
						$checked_items[$index][] = $value;
					}
				}
			}
			elseif (!empty($name))
			{
				$return[$name] = $value;
				if ($checked)
				{
					if (empty($checked_items[$name]))
					{
						$checked_items[$name] = array();
					}
					$checked_items[$name] = $value;
				}
			}
		}

		foreach ($names as $extra)
		{
			$return[$extra] = ${$extra};
		}

		$return['_checked'] = $checked_items;

		return $return;
	}

	/**
	 *
	 * @return unknown_type
	 */
	/*function setDateVariables( $curdate, $enddate, $period )
	{
		$database = JFactory::getDbo();

		$return = new stdClass();
		$return->thisdate = '';
		$return->nextdate = '';

		switch ($period)
		{
			case "daily":
				$thisdate = $curdate;
				$query = " SELECT DATE_ADD('".$curdate."', INTERVAL 1 DAY) ";
				$database->setQuery( $query );
				$nextdate = $database->loadResult();
				$return->thisdate = $thisdate;
				$return->nextdate = $nextdate;
				break;
			case "weekly":
				$start 	= getdate( strtotime($curdate) );

				// First period should be days between x day and the immediate Sunday
				if ($start['wday'] < '1') {
					$thisdate = $curdate;
					$query = " SELECT DATE_ADD( '".$thisdate."', INTERVAL 1 DAY ) ";
					$database->setQuery( $query );
					$nextdate = $database->loadResult();
				} elseif ($start['wday'] > '1') {
					$interval = 8 - $start['wday'];
					$thisdate = $curdate;
					$query = " SELECT DATE_ADD( '".$thisdate."', INTERVAL {$interval} DAY ) ";
					$database->setQuery( $query );
					$nextdate = $database->loadResult();
				} else {
					// then every period following should be Mon-Sun
					$thisdate = $curdate;
					$query = " SELECT DATE_ADD( '".$thisdate."', INTERVAL 7 DAY ) ";
					$database->setQuery( $query );
					$nextdate = $database->loadResult();
				}

				if ( $nextdate > $enddate ) {
					$query = " SELECT DATE_ADD( '".$nextdate."', INTERVAL 1 DAY ) ";
					$database->setQuery( $query );
					$nextdate = $database->loadResult();
				}
				$return->thisdate = $thisdate;
				$return->nextdate = $nextdate;
				break;
			case "monthly":
				$start 	= getdate( strtotime($curdate) );
				$start_datetime = date("Y-m-d", strtotime($start['year']."-".$start['mon']."-01"));
				$thisdate = $start_datetime;
				$query = " SELECT DATE_ADD( '".$thisdate."', INTERVAL 1 MONTH ) ";
				$database->setQuery( $query );
				$nextdate = $database->loadResult();

				$return->thisdate = $thisdate;
				$return->nextdate = $nextdate;
				break;
			default:
				break;
		}

		return $return;
	}

	/**
	 * return local today Data as GMT value.
	 * TODO handle solar and legal time where is present.
	 * @return unknown_type
	 */
	/*function getToday()
	{
		static $today;

		if (empty($today))
		{
			$config = JFactory::getConfig();
			$offset = $config->getValue('config.offset');
			$date = JFactory::getDate(); //get local data
			$today = $date->toFormat( "%Y-%m-%d 00:00:00" );
			if ($offset < 0) {
				$command = 'DATE_ADD';
			} elseif ($offset > 0) {
				$command = 'DATE_SUB';
			} else {
				return $today;
			}

			$database = JFactory::getDbo();
			$query = "
				SELECT
				{$command}( '{$today}', INTERVAL {$offset} HOUR )
				";

				$database->setQuery( $query );
				$today = $database->loadResult();
		}
		return $today;
	}

	/**
	 *
	 * @param $date
	 * @return unknown_type
	 */
 /*	function getOffsetDate( $date, $offset='' )
	{
		if (empty($offset))
		{
			$config = JFactory::getConfig();
			$offset = $config->getValue('config.offset');
		}

		if ($offset > 0) {
			$command = 'DATE_ADD';
		} elseif ($offset < 0) {
			$command = 'DATE_SUB';
		} else {
			$command = '';
		}

		if ($command)
		{
			$offset = abs($offset);

			$database = JFactory::getDbo();
			$query = "
				SELECT
				{$command}( '{$date}', INTERVAL {$offset} HOUR )
				";
				$database->setQuery( $query );
				$date = $database->loadResult();
		}
		return $date;
	}

	function getPeriodData( $start_datetime, $end_datetime, $period='daily', $select="tbl.*", $type='list' )
	{
		static $items;

		if (empty($items[$start_datetime][$end_datetime][$period][$select]))
		{
			$runningtotal = 0;
			$return = new stdClass();
			$database = JFactory::getDbo();

			// the following would be used if there were an additional filter in the Inputs
			$filter_where 	= "";
			$filter_select 	= "";
			$filter_join 	= "";
			$filter_typeid 	= "";
			if ($filter_typeid) {
				$filter_where 	= "";
				$filter_select 	= "";
				$filter_join 	= "";
			}

			$start_datetime = strval( htmlspecialchars( $start_datetime ) );
			$end_datetime = strval( htmlspecialchars( $end_datetime ) );

			$start 	= getdate( strtotime($start_datetime) );

			// start with first day of the period, corrected for offset
			$mainframe = JFactory::getApplication();
			$offset = $mainframe->getCfg( 'offset' );
			if ($offset > 0) {
				$command = 'DATE_ADD';
			} elseif ($offset < 0) {
				$command = 'DATE_SUB';
			} else {
				$command = '';
			}
			if ($command)
			{
				$database = JFactory::getDbo();
				$query = "
					SELECT
					{$command}( '{$start_datetime}', INTERVAL {$offset} HOUR )
					";

					$database->setQuery( $query );
					$curdate = $database->loadResult();

					$query = "
					SELECT
					{$command}( '{$end_datetime}', INTERVAL {$offset} HOUR )
					";

					$database->setQuery( $query );
					$enddate = $database->loadResult();
			}
			else
			{
				$curdate = $start_datetime;
				$enddate = $end_datetime;
			}

			// while the current date <= end_date
			// grab data for the period
			$num = 0;
			$result = array();
			while ($curdate <= $enddate)
			{
				// set working variables
				$variables = CitruscartHelperBase::setDateVariables( $curdate, $enddate, $period );
				$thisdate = $variables->thisdate;
				$nextdate = $variables->nextdate;

				// grab all records
				// TODO Set the query here
				$query = new CitruscartQuery();
				$query->select( $select );
				$rows = $this->selectPeriodData( $thisdate, $nextdate, $select, $type );
				$total = $this->selectPeriodData( $thisdate, $nextdate, "COUNT(*)", "result" );

				//store the value in an array
				$result[$num]['rows']		= $rows;
				$result[$num]['datedata'] 	= getdate( strtotime($thisdate) );
				$result[$num]['countdata']	= $total;
				$runningtotal 				= $runningtotal + $total;

				// increase curdate to the next value
				$curdate = $nextdate;
				$num++;

			} // end of the while loop

			$return->rows 		= $result;
			$return->total 		= $runningtotal;
			$items[$start_datetime][$end_datetime][$period][$select] = $return;
		}

		return $items[$start_datetime][$end_datetime][$period][$select];
	}

	/**
	 * includeJQueryUI function.
	 *
	 * @access public
	 * @return void
	 */
	function includeJQueryUI()
	{
		self::includeJQuery();
		JHTML::_('script', 'jquery-ui-1.7.2.min.js', 'media/citruscart/js/');
		JHTML::_('stylesheet', 'jquery-ui.css', 'media/citruscart/css/');
	}

	/**
	 * includeJQuery function.
	 *
	 * @access public
	 * @return void
	 */
	function includeJQuery()
	{
		JHTML::_('script', 'jquery-1.3.2.min.js', 'media/citruscart/js/');
	}

	/**
	 * Include JQueryMultiFile script
	 */
	function includeMultiFile()
	{
		JHTML::_('script', 'Stickman.MultiUpload.js', 'media/citruscart/js/');
		JHTML::_('stylesheet', 'Stickman.MultiUpload.css', 'media/citruscart/css/');
	}

	/**
	 * Generate an html message for the checkout page
	 * used for validation errors
	 *
	 * @param string message
	 * @return html message
	 */
	function generateMessage($msg, $include_li=true, $add_fade = true )
	{
		$fade = $add_fade ? ' fade' : '';
		//<dd class="notice message '.$fade.'">
		$html = '
		<dl id="system-message">
            <dt class="notice">'.JText::_('COM_CITRUSCART_NOTICE').'</dt>
            <dd class="notice message alert alert-warning">

                <ul>';

		if ($include_li) {
			$html .= "<li>".$msg."</li>";
		} else {
			$html .= $msg;
		}

		$html .= "
                </ul>
            </dd>
        </dl>";

		return $html;
	}

	/**
	 * Sets a json_encoded session variable to value
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @return void
	 */
	/*function setSessionVariable($key, $value)
	{
		$session = JFactory::getSession();
		$session->set($key, json_encode($value));
	}

	/**
	 * Gets json_encoded session variable
	 *
	 * @param str $key
	 * @return mixed
	 */
	/*function getSessionVariable($key, $default=null)
	{
		$session = JFactory::getSession();
		$sessionvalue = $default;
		if ($session->has($key))
		{
			$sessionvalue = $session->get($key);
			if (!empty($sessionvalue))
			{
				$sessionvalue = json_decode($sessionvalue);
			}
		}
		return $sessionvalue;
	}

	/**
	 * Set the document format
	 */
	public static function setFormat( $format = 'html' )
	{
		// 	Default to raw output
		$doc = JFactory::getDocument();
		$document = JDocument::getInstance($format);

		$doc = $document;
	}

	/**
	 * convert Local data to GMT data
	 */
	public static function local_to_GMT_data( $local_data )
	{
		$GMT_data=$local_data ;
		if(!empty($local_data))
		{
			$config = JFactory::getConfig();
			$offset = $config->get('offset');
			$offset=0-$offset;
			$date = date_create($local_data);
			date_modify($date,  $offset.' hour');
			$GMT_data= date_format($date, 'Y-m-d H:i:s');
		}
		return $GMT_data;
	}

	/**
	 * convert GMT data to Local data
	 */
	public static function GMT_to_local_data( $GMT_data )
	{
		$local_data=$GMT_data ;

		if(!empty($local_data))
		{
			$config = JFactory::getConfig();
			$offset = $config->get('offset');

			//$date = date_create($GMT_data);

			$date = date_create($offset);

			date_modify($date,  $offset.' hour');
			$local_data= date_format($date, 'Y-m-d H:i:s');
		}
		return $local_data;
	}

	/**
	 * Generates a validation message
	 *
	 * @param unknown_type $text
	 * @param unknown_type $type
	 * @return unknown_type
	 */
	public static function validationMessage( $text, $type='fail' )
	{
		switch (strtolower($type))
		{
			case "success":
				$src = Citruscart::getUrl( 'images' ).'accept_16.png';
				$html = "<div class='citruscart_validation'><img src='$src' alt='".JText::_('COM_CITRUSCART_SUCCESS')."'><span class='validation-success'>".JText::_( $text )."</span></div>";
				break;
			default:
				$src = Citruscart::getUrl( 'images' ).'remove_16.png';
				$html = "<div class='citruscart_validation'><img src='$src' alt='".JText::_('COM_CITRUSCART_ERROR')."'><span class='validation-fail'>".JText::_( $text )."</span></div>";
				break;
		}
		return $html;
	}

	/**
	 * Generates a new secret key for Citruscart
	 *
	 * @param $length		Length of the word
	 * @return 					Secret key as a string
	 */
	public static function generateSecretWord( $length = 32 )
	{
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+}{|:<>?,. ";
		$len = strlen( $salt );
		$sw = '';

		for ($i = 0; $i < $length; $i++ )
			$sw .= $salt[mt_rand( 0, $len -1 )];

		return $sw;
	}

	/**
	 * Method which gets a correct time of beginning of a day with respect to the current time zone
	 *
	 * @param $date	Joomla JDate object
	 *
	 * @return Correct Datetime with respect to the current time zone
	 */
	function getCorrectBeginDayTime( $date )
	{
		$date_gmt = $date;
		if( is_object( $date_gmt ) )
			$date_gmt = $date_gmt->format( "%Y-%m-%d %H:%M:%S" );

		$date_local = CitruscartHelperBase::GMT_to_local_data( ( string ) $date_gmt );
		$startdate_gmt = date_format( date_create( $date_local ), 'Y-m-d 00:00:00' );
		return CitruscartHelperBase::local_to_GMT_data( $startdate_gmt );

	}

	/**
	 * Method to add translation strings to JS translation object
	 *
	 * @param $strings	Associative array with list of strings to translate
	 *
	 */
	public static function addJsTranslationStrings( $strings )
	{
		if( self::$added_strings === null )
			self::$added_strings = array();

		JHTML::_('script', 'citruscart_lang.js', 'media/citruscart/js/');
		$js_strings = array();
		for( $i = 0, $c = count( $strings ); $i < $c; $i++ )
		{
			if( in_array( strtoupper( $strings[$i] ), self::$added_strings ) === false )
			{
				$js_strings []= '"'.strtoupper( $strings[$i] ).'":"'.JText::_( $strings[$i] ).'"';
				self::$added_strings []= strtoupper( $strings[$i] );
			}
		}

		if( count( $js_strings ) )
		{
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration( 'Joomla.JText.load({'.implode( ',', $js_strings ).'});' );
		}
	}
}
