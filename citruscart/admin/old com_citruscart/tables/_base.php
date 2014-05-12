<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

//Citruscart::load( 'CitruscartQuery', 'library.query' );
require_once(JPATH_SITE.'/libraries/dioscouri/library/table.php');
class CitruscartTable extends DSCTable
{
	/**
	 * constructor
	 */
	function __construct( $tbl_name, $tbl_key, &$db )
	{
		parent::__construct( $tbl_name, $tbl_key, $db );
		// set table properties based on table's fields
		//$this->setTableProperties();
	}

	/**
	 * Uses the parameters from com_content to clean the HTML from a fieldname
	 *
	 * @param $fieldname (optional) default = description
	 * @return void
	 */
	function filterHTML( $fieldname='description' )
	{
		if ( !in_array( $fieldname, array_keys( $this->getProperties() ) ) )
		{
			$this->setError( get_class( $this ).' does not have a field named `'.$fieldname.'`' );
			return;
		}

		// Filter settings
		jimport( 'joomla.application.component.helper' );
		$config	= JComponentHelper::getParams( 'com_content' );
		$user	= JFactory::getUser();
		$gid	= $user->get( 'gid' );

		$filterGroups	= $config->get( 'filter_groups' );

		// convert to array if one group selected
		if ( (!is_array($filterGroups) && (int) $filterGroups > 0) )
		{
			$filterGroups = array($filterGroups);
		}

		if (is_array($filterGroups) && in_array( $gid, $filterGroups ))
		{
			$filterType		= $config->get( 'filter_type' );
			$filterTags		= preg_split( '#[,\s]+#', trim( $config->get( 'filter_tags' ) ) );
			$filterAttrs	= preg_split( '#[,\s]+#', trim( $config->get( 'filter_attritbutes' ) ) );
			switch ($filterType)
			{
				case 'NH':
					$filter	= new JFilterInput();
					break;
				case 'WL':
					$filter	= new JFilterInput( $filterTags, $filterAttrs, 0, 0 );
					break;
				case 'BL':
				default:
					$filter	= new JFilterInput( $filterTags, $filterAttrs, 1, 1 );
					break;
			}
			$this->$fieldname	= $filter->clean( $this->$fieldname );
		}
			elseif (empty($filterGroups))
		{
			$filter = new JFilterInput(array(), array(), 1, 1);
			$this->$fieldname = $filter->clean( $this->$fieldname );
		}
	}

	/**
	 * Same as JFilterOutput::stringURLSafe, but allowing _ character
	 *
	 * @param unknown_type $string
	 */
	public function stringDBSafe($string)
	{
		//remove any '-' from the string they will be used as concatonater
		$str = str_replace('-', ' ', $string);

		$lang = JFactory::getLanguage();
		$str = $lang->transliterate($str);

		// remove any duplicate whitespace, and ensure all characters are alphanumeric
		$str = preg_replace(array('/\s+/','/[^A-Za-z0-9_\-]/'), array('-',''), $str);

		// lowercase and trim
		$str = trim(strtolower($str));
		return $str;
	}

}
