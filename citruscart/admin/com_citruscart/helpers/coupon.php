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

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class CitruscartHelperCoupon extends CitruscartHelperBase
{
	/**
	 * Given a coupon id or code, checks if the coupon is available for use today.
	 * If given a user_id, checks if the user can use the coupon
	 *
	 * @param $coupon string
	 * @param $id_type string
	 * @param $user_id
	 *
	 * @return boolean if false, coupon object if true
	 */
	function isValid( $coupon_id, $id_type='code', $user_id='' )
	{
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );

		switch($id_type)
		{
			case 'id':
			case 'coupon_id':
				$coupon = JTable::getInstance( 'Coupons', 'CitruscartTable' );
				$coupon->load( array('coupon_id'=>$coupon_id) );
				break;
			case 'code':
			case 'coupon_code':
			default:
				$coupon = JTable::getInstance( 'Coupons', 'CitruscartTable' );
				$coupon->load( array('coupon_code'=>$coupon_id) );
				break;
		}

		// do we need individualized error reporting?
		if (empty($coupon->coupon_id))
		{
			$this->setError( JText::_('COM_CITRUSCART_INVALID_COUPON') );
			return false;
		}

		// is the coupon enabled?
		if (empty($coupon->coupon_enabled))
		{
			$this->setError( JText::_('COM_CITRUSCART_COUPON_NOT_ENABLED') );
			return false;
		}

		$date = JFactory::getDate();
		if ($date->toSql() < $coupon->start_date)
		{
			$this->setError( JText::_('COM_CITRUSCART_COUPON_NOT_VALID_TODAY') );
			return false;
		}

		$db = JFactory::getDBO();
		$nullDate = $db->getNullDate();
		if ($coupon->expiration_date != $nullDate && $date->toSql() > $coupon->expiration_date)
		{
			$this->setError( JText::_('COM_CITRUSCART_COUPON_EXPIRED') );
			return false;
		}

		if ($coupon->coupon_max_uses > '-1' && $coupon->coupon_uses >= $coupon->coupon_max_uses)
		{
			$this->setError( JText::_('COM_CITRUSCART_COUPON_MAXIMUM_USES_REACHED') );
			return false;
		}

		if (!empty($user_id))
		{
			// Check the user's uses of this coupon
			$model = JModelLegacy::getInstance( 'OrderCoupons', 'CitruscartModel' );
			$model->setState('filter_user', $user_id);
			$model->setState('filter_coupon', $coupon->coupon_id);
			$user_uses = $model->getResult();
			if ($coupon->coupon_max_uses_per_user > '-1' && $user_uses >= $coupon->coupon_max_uses_per_user)
			{
				$this->setError( JText::_('COM_CITRUSCART_COUPON_USED_MAXIMUM_NUMBER_OF_TIMES') );
				return false;
			}
		}

		// all ok
		return $coupon;
	}

	public static function checkByProductIds($coupon_id, $product_ids)
	{
		if (!empty($product_ids))
		{

			$ids = implode(",", $product_ids);

			// Check the product_id
			Citruscart::load( 'CitruscartQuery', 'library.query' );
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$table = JTable::getInstance( 'ProductCoupons', 'CitruscartTable' );

			$query = new CitruscartQuery();
			$query->select( "COUNT(*)" );
			$query->from( $table->getTableName()." AS tbl" );
			$query->where( "tbl.product_id IN (".$ids.")" );
			$query->where( "tbl.coupon_id = ".(int) $coupon_id );

			$db = JFactory::getDBO();
			$db->setQuery( (string) $query );

			$count = $db->loadResult();

			if (!$count)
			{
				return false;
			}

			return true;
		}

		return false;
	}

	public static function getCouponProductIds($coupon_id)
	{
		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$query = new CitruscartQuery();
		$query->select('product_id');
		$query->from('#__citruscart_productcouponxref');
		$query->where('coupon_id = '.(int)$coupon_id);

		$db = JFactory::getDBO();
		$db->setQuery($query);
		return $db->loadColumn();

	}

	/**
	 * One a new order,
	 * increase the uses count on all the ordercoupons.
	 *
	 * @param $order_id
	 * @return unknown_type
	 */
	public function processOrder( $order_id )
	{
	    DSCModel::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
	    $model = DSCModel::getInstance( 'Ordercoupons', 'CitruscartModel' );
	    $model->setState( 'filter_orderid', $order_id );
	    if ($items = $model->getList())
	    {
	        DSCTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
	        $coupon = DSCTable::getInstance( 'Coupons', 'CitruscartTable' );
	        foreach ($items as $item)
	        {
	            $coupon->load( array( 'coupon_id'=>$item->coupon_id ) );
	            $coupon->coupon_uses = $coupon->coupon_uses + 1;
	            if (!$coupon->save())
	            {
	                //JFactory::getApplication()->enqueueMessage( $coupon->getError() );
	            }
	        }
	    }
	}

}