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
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableGroups extends CitruscartTable
{
	function CitruscartTableGroups ( &$db )
	{

		$tbl_key 	= 'group_id';
		$tbl_suffix = 'groups';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'citruscart';

		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}

	function check()
	{
		$db			= $this->getDBO();
		$nullDate	= $db->getNullDate();
		if (empty($this->created_date) || $this->created_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->created_date = $date->toSql();
		}
		if (empty($this->modified_date) || $this->modified_date == $nullDate)
		{
			$date = JFactory::getDate();
			$this->modified_date = $date->toSql();
		}
		$this->filterHTML( 'group_name' );
		if (empty($this->group_name))
		{
			$this->setError( JText::_('COM_CITRUSCART_NAME_REQUIRED') );
			return false;
		}
		$this->filterHTML( 'group_description' );
		return true;
	}

	/**
	 * Stores the object
	 * @param object
	 * @return boolean
	 */
	function store($updateNulls=false)
	{
		$date = JFactory::getDate();
		$this->modified_date = $date->toSql();
		$store = parent::store($updateNulls);
		return $store;
	}

	/**
	 * Delete also the prices linked to this group
	 */
	function delete($oid=null)
	{
		$k = $this->_tbl_key;
		$default_user_group = Citruscart::getInstance()->get('default_user_group', '1');

		if($oid)
		{
			$key = $oid;
		}
		else
		{
			$key = $this->$k;
		}


		if( $key != $default_user_group )
		{
			$return = parent::delete($oid);

			if($return)
			{

				// Delete user group relationships
				$model = JModelLegacy::getInstance('UserGroups', 'CitruscartModel');
				$model->setState('filter_group', $this->$k);
				$links = $model->getList();

				if($links)
				{
					$table = JTable::getInstance('UserGroups', 'CitruscartTable');
					foreach($links as $link)
					{
						$table->delete($link->user_id);
					}
				}

				// Delete prices
				$model = JModelLegacy::getInstance('ProductPrices', 'CitruscartModel');
				$model->setState('filter_user_group', $this->$k);
				$prices = $model->getList();

				if($prices)
				{
					$table = JTable::getInstance('ProductPrices', 'CitruscartTable');
					foreach($prices as $price)
					{
						$table->delete($price->user_id);
					}
				}
			}
		}
		else
		{
			$this->setError(JText::_('COM_CITRUSCART_COM_CITRUSCART_YOU_CANT_DELETE_THE_DEFAULT_USER_GROUP'));
			return false;
		}

		return $return;

	}
}
