<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load('CitruscartTable', 'tables._base');

class CitruscartTableProductFiles extends CitruscartTable 
{
	function CitruscartTableProductFiles(&$db) 
	{
		$tbl_key = 'productfile_id';
		$tbl_suffix = 'productfiles';
		$this -> set('_suffix', $tbl_suffix);
		$name = 'citruscart';

		parent::__construct("#__{$name}_{$tbl_suffix}", $tbl_key, $db);
	}

	/**
	 * Checks row for data integrity.
	 *
	 * @return unknown_type
	 */
	function check() 
	{
		if (empty($this -> product_id)) 
		{
			$this -> setError(JText::_('COM_CITRUSCART_PRODUCT_ASSOCIATION_REQUIRED'));
			return false;
		}

		if (empty($this -> productfile_name)) 
		{
			$this -> setError(JText::_('COM_CITRUSCART_FILE_NAME_REQUIRED'));
			return false;
		}

		$nullDate = $this -> _db -> getNullDate();
		if (empty($this -> created_date) || $this -> created_date == $nullDate) 
		{
			$date = JFactory::getDate();
			$this -> created_date = $date -> toSql();
		}
		$date = JFactory::getDate();
		$this -> modified_date = $date -> toSql();

		return true;
	}

	/**
	 * Adds context to the default reorder method
	 * @return unknown_type
	 */
	function reorder($where = '') 
	{
		parent::reorder('product_id = ' . $this -> _db -> Quote($this -> product_id));
	}

	/**
	 * Determines if a user can download the file
	 * only using datetime if it is present
	 *
	 * @param unknown_type $user_id
	 * @param unknown_type $datetime
	 * @return unknown_type
	 */
	function canDownload($user_id, $datetime = null) 
	{
		// if the user is super admin, yes
		if (DSCAcl::isAdmin()) 
		{
			return true;
		}

		// if the product file doesn't require purchase
		if (empty($this -> purchase_required)) 
		{
			return true;
		}

		// or because they have purchased it and the num_downloads is < max (or max == -1)

		// Check if they have an unlimited number of downloads
		$productdownloads = JTable::getInstance('ProductDownloads', 'CitruscartTable');
		$productdownloads -> load(array('productfile_id' => $this -> productfile_id, 'user_id' => $user_id));

		if ($productdownloads -> productdownload_id) 
		{
			if ($productdownloads -> productdownload_max == '-1' || $productdownloads -> productdownload_max > '0') 
			{
				return true;
			}
		}
		
		// otherwise no
		return false;
	}

	/**
	 * Logs a download
	 *
	 * @param $user_id
	 * @return unknown_type
	 */
	function logDownload($user_id) 
	{
		$downloadlog = JTable::getInstance('ProductDownloadLogs', 'CitruscartTable');
		$downloadlog -> user_id = $user_id;
		$downloadlog -> productfile_id = $this -> productfile_id;
		$downloadlog -> save();
	}

	/**
	 * Loads a row from the database and binds the fields to the object properties
	 *
	 * @access	public
	 * @param	mixed	Optional primary key.  If not specifed, the value of current key is used
	 * @return	boolean	True if successful
	 */
	function load($oid = null, $reset = true) 
	{
		if (!parent::load($oid, $reset)) {
			return false;
		}

		// relative paths start with DSDS
		if (strpos($this -> productfile_path, DS . DS) !== false)// relative path => add JPATH_BASE before it
		{
			$app = JFactory::getApplication();
			if ($app -> isAdmin())//saving on admin site -> path_base contains "/Administrator"
				$this -> productfile_path = substr(JPATH_BASE, 0, strlen(JPATH_BASE) - 14) . substr($this -> productfile_path, 1);
			else
				$this -> productfile_path = JPATH_BASE . substr($this -> productfile_path, 1);
		}

		return true;
	}

	/**
	 *
	 * @param unknown_type $updateNulls
	 * @return unknown_type
	 */
	function store($updateNulls = false) 
	{
		$app = JFactory::getApplication();
		// relative paths start with DSDS
		if (strpos($this -> productfile_path, DS . DS) === false)// not a relative path => make it
		{
			$prefix = DS . DS;
			if ($this -> productfile_path[0] == DS)// in case we use UNIX path (starting with /)
				$prefix = DS;
			if ($app -> isAdmin())//saving on admin site -> path_base contains "/Administrator"
				$this -> productfile_path = $prefix . substr($this -> productfile_path, strlen(JPATH_BASE) - 14);
			else
				$this -> productfile_path = $prefix . substr($this -> productfile_path, strlen(JPATH_BASE));
		}

		return parent::store($updateNulls);
	}
	
	function delete( $oid=null )
	{
	    $k = $this->_tbl_key;
	    if ($oid) {
	        $this->$k = intval( $oid );
	    }
	
	    if ($return = parent::delete( $oid ))
	    {
	        $query = new CitruscartQuery();
	        $query->delete();
	        $query->from( '#__citruscart_productdownloads' );
	        $query->where( 'productfile_id = '.$this->$k );
	        $this->_db->setQuery( (string) $query );
	        $this->_db->query();
	        
	        $query = new CitruscartQuery();
	        $query->delete();
	        $query->from( '#__citruscart_productdownloadlogs' );
	        $query->where( 'productfile_id = '.$this->$k );
	        $this->_db->setQuery( (string) $query );
	        $this->_db->query();
	    }
	
	    return parent::check();
	}

}
