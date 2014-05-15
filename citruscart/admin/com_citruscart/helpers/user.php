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

Citruscart::load('CitruscartHelperBase', 'helpers._base');
require_once(JPATH_SITE.'/libraries/dioscouri/library/helper/user.php');
class CitruscartHelperUser extends DSCHelperUser
{
	/**
	 * Gets a users basic information
	 *
	 * @param int $userid
	 * @return obj CitruscartAddresses if found, false otherwise
	 */
	public static function getBasicInfo( $userid )
	{
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$row = JTable::getInstance('UserInfo', 'CitruscartTable');
		$row->load( array( 'user_id' => $userid ) );
		return $row;
	}

	/**
	 * Gets a users primary address, if possible
	 *
	 * @param int $userid
	 * @return obj CitruscartAddresses if found, false otherwise
	 */
	public static function getPrimaryAddress( $userid, $type='billing' )
	{
		$return = false;
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
		$model = JModelLegacy::getInstance( 'Addresses', 'CitruscartModel' );
		switch($type)
		{
			case "shipping":
				$model->setState('filter_isdefaultshipping', '1');
				break;
			default:
				$model->setState('filter_isdefaultbilling', '1');
				break;
		}
		$model->setState('filter_userid', (int) $userid);
		$model->setState( 'filter_deleted', 0 );
		$items = $model->getList();

		if (empty($items))
		{
			$model = JModelLegacy::getInstance( 'Addresses', 'CitruscartModel' );
			$model->setState('filter_userid', (int) $userid);
			$model->setState( 'filter_deleted', 0 );
			$items = $model->getList();
		}

		if (!empty($items))
		{
			$return = $items[0];
		}


		return $return;
	}

	/**
	 * Gets a user's geozone
	 * @param int $userid
	 * @return unknown_type
	 */
	public static function getGeoZones( $userid )
	{
		Citruscart::load( 'CitruscartHelperShipping', 'helpers.shipping' );

		$address = CitruscartHelperUser::getPrimaryAddress( $userid, 'billing' );
		if (empty($address->zone_id))
		{
			return array();
		}

		$geozones = CitruscartHelperShipping::getGeoZones( $address->zone_id, '1', $address->postal_code );
		return $geozones;
	}


	/**
	 *
	 * @param $string
	 * @return unknown_type
	 */
	public static function emailExists( $string, $table='users'  ) {
		switch($table)
		{
			case 'accounts' :
				$table = '#__citruscart_accounts';
				break;

			case  'users':
			default     :
				$table = '#__users';
		}

		$success = false;
		$database = JFactory::getDBO();
		$string = $database->escape($string);
		$query = "
            SELECT
                *
            FROM
            $table
            WHERE 1
            AND
                `email` = '{$string}'
            LIMIT 1
        ";
            $database->setQuery($query);
            $result = $database->loadObject();
            if ($result) {
            	$success = true;
            }
            return $result;
	}


	/**
	 * Returns yes/no
	 * @param object
	 * @param mixed Boolean
	 * @return array
	 */
	function _sendMail( &$user, $details, $useractivation, $guest=false )
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_citruscart', JPATH_ADMINISTRATOR);

		$mainframe = JFactory::getApplication();

		$db     = JFactory::getDBO();

		$name       = $user->get('name');
		$email      = $user->get('email');
		$username   = $user->get('username');
		$activation = $user->get('activation');
		$password   = $details['password2']; // using the original generated pword for the email

		$usersConfig    = JComponentHelper::getParams( 'com_users' );
		// $useractivation = $usersConfig->get( 'useractivation' );
		$sitename       = $mainframe->getCfg( 'sitename' );
		$mailfrom       = $mainframe->getCfg( 'mailfrom' );
		$fromname       = $mainframe->getCfg( 'fromname' );
		$siteURL        = JURI::base();

		$subject    = sprintf ( JText::_('COM_CITRUSCART_ACCOUNT_DETAILS_FOR'), $name, $sitename);
		$subject    = html_entity_decode($subject, ENT_QUOTES);

		if ( $useractivation == 1 )
		{
			$message = sprintf ( JText::_('COM_CITRUSCART_EMAIL_MESSAGE_ACTIVATION'), $sitename, $siteURL, $username, $password, $activation );
		}
		else
		{
			$message = sprintf ( JText::_('COM_CITRUSCART_EMAIL_MESSAGE'), $sitename, $siteURL, $username, $password );
		}

		if ($guest)
		{
			$message = sprintf ( JText::_('COM_CITRUSCART_EMAIL_MESSAGE_GUEST'), $sitename, $siteURL, $username, $password );
		}

		$message = html_entity_decode($message, ENT_QUOTES);

		//get all super administrator
		/*$query = 'SELECT name, email, sendEmail' .
                ' FROM #__users' .
                ' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();*/

		$rows = DSCAcl::getAdminList();

		// Send email to user
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		$success = CitruscartHelperUser::doMail($mailfrom, $fromname, $email, $subject, $message);

		return $success;
	}

	/**
	 * Processes a new order
	 *
	 * @param $order_id
	 * @return unknown_type
	 */
	function processOrder( $order_id )
	{
		// get the order
		$model = JModelLegacy::getInstance( 'Orders', 'CitruscartModel' );
		$model->setId( $order_id );
		$order = $model->getItem();
		if( $order->user_id < Citruscart::getGuestIdStart() ) {
			//load language from frontend so it has email  language strings
			$lang = JFactory::getLanguage();
			$lang->load('com_citruscart', JPATH_SITE);

			$details = array();
			$details['name'] = $order->billing_first_name .' '. $order->billing_last_name;
			$details['username'] = $order->userinfo_email;
			$details['email'] = $order->userinfo_email;
			jimport('joomla.user.helper');
			$details['password'] = JUserHelper::genRandomPassword();
			$details['password2'] = $details['password'] ;



		 $user = CitruscartHelperUser::createNewUser($details);
		 print_r($user);
		 $order->user_id = $user->id;
		 //update the order to the new user
		 $table = $model->getTable();
		 $table->load($order->order_id);
		 $table->user_id = $user->id;
		 $table -> save();

		 $model->clearCache();
		 //

		}


		// find the products in the order that are integrated
		foreach ($order->orderitems as $orderitem)
		{
			$model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
			$model->setId( $orderitem->product_id );
			$product = $model->getItem();

			$core_user_change_gid = $product->product_parameters->get('core_user_change_gid');
			$core_user_new_gid = $product->product_parameters->get('core_user_new_gid');
			if (!empty($core_user_change_gid))
			{

				DSCAcl::addgroup( $order->user_id,$core_user_new_gid);
			}
		}
	}

	/**
	 * Gets a user's user group used for pricing
	 *
	 * @param $user_id
	 * @return mixed
	 */
	public static function getUserGroup( $user_id='', $product_id='')
	{
		// $sets[$user_id][$product_id]
		static $sets, $groups;
		if (!is_array($sets)) { $sets = array(); }
		if (!is_array($groups)) { $groups = array(); }

		$user_id = (int) $user_id;
		$product_id = (int) $product_id;

		if (!empty($user_id) && !empty($product_id))
		{
			if (!isset($sets[$user_id][$product_id]))
			{
				if (!isset($groups[$user_id]))
				{
					JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
					$model = JModelLegacy::getInstance('UserGroups', 'CitruscartModel');
					$model->setState( 'filter_user', $user_id );
					//order to get the upper group
					$model->setState('order', 'g.ordering');
					$model->setState( 'direction', 'ASC' );
					$groups[$user_id] = $model->getList();
				}
				$items = $groups[$user_id];

				// using the helper to cut down on queries
				$product_helper =  CitruscartHelperBase::getInstance( 'Product' );

				$prices = $product_helper->getPrices( $product_id );

				$groupIds = array();
				foreach ($prices as $price)
				{
					$groupIds[] = $price->group_id;
				}

				foreach ($items as $item)
				{
					if (in_array($item->group_id, $groupIds))
					{
						$sets[$user_id][$product_id] = $item->group_id;
						// return $sets[$user_id][$product_id]; // i dont understand why the return here doesnt work and and the still continues
						break;
					}
				}
			}
		}

		if (!isset($sets[$user_id][$product_id]))
		{
			$sets[$user_id][$product_id] = Citruscart::getInstance()->get('default_user_group', '1');
		}

		return $sets[$user_id][$product_id];
	}
	/**
	 *
	 * Get Avatar based on the installed community component
	 * @param int $id - userid
	 * @return object
	 */
	function getAvatar($id)
	{
		$avatar = '';
		$found = false;
		Citruscart::load( 'CitruscartHelperAmbra', 'helpers.ambra' );
		$helper_ambra = CitruscartHelperBase::getInstance( 'Ambra' );

		//check if ambra installed
		if($helper_ambra->isInstalled() && !$found)
		{
			if ( !class_exists('Ambra') )
			{
				JLoader::register( "Ambra", JPATH_ADMINISTRATOR."/components/com_ambra/defines.php" );
			}
			//Get Ambra Avatar
			if($image = Ambra::getClass( "AmbraHelperUser", 'helpers.user' )->getAvatar( $id ))
			{
				$link = JRoute::_( JURI::root().'index.php?option=com_ambra&view=users&id='.$id, false );
				$avatar .= "<a href='{$link}' target='_blank'>";
				$avatar .= "<img src='{$image}' style='max-width:80px; border:1px solid #ccccce;' />";
				$avatar .= "</a>";
			}
			$found = true;
		}
		//check if jomsocial installed
		if( DSC::getApp()->isComponentInstalled( 'com_community' ) && !$found)
		{
			//Get JomSocial Avatar
			$database = JFactory::getDBO();
			$query = "
			SELECT
				*
			FROM
				#__community_users
			WHERE
				`userid` = '".$id."'
			";
			$database->setQuery( $query );
			$result = $database->loadObject();
			if (isset($result->thumb ))
			{
				$image = JURI::root().$result->thumb;
			}
			$link = JRoute::_( JURI::root().'index.php?option=com_community&view=profile&userid='.$id, false );
			$avatar .= "<a href='{$link}' target='_blank'>";
			$avatar .= "<img src='{$image}' style='max-width:80px; border:1px solid #ccccce;' />";
			$avatar .= "</a>";
			$found = true;
		}
		//check if community builder is installed
		if( DSC::getApp()->isComponentInstalled( 'com_comprofiler' ) && !$found)
		{
			//Get JomSocial Avatar
			$database = JFactory::getDBO();
			$query = "
			SELECT
				*
			FROM
				#__comprofiler
			WHERE
				`id` = '".$id."'
			";
			$database->setQuery( $query );
			$result = $database->loadObject();
			if (isset($result->avatar))
			{
				$image = JURI::root().'images/comprofiler/'.$result->avatar;
			}
			else
			{
				$image = JRoute::_( JURI::root().'components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png');
			}
			$link = JRoute::_( JURI::root().'index.php?option=com_comprofiler&userid='.$id, false );
			$avatar .= "<a href='{$link}' target='_blank'>";
			$avatar .= "<img src='{$image}' style='max-width:80px; border:1px solid #ccccce;' />";
			$avatar .= "</a>";
			$found = true;
		}

		return $avatar;

	}

	/*
	 * Gets user subscription number
	 *
	 * @param $id User ID
	 *
	 * @return User subscription number
	 */
	function getSubNumber( $id )
	{
		$db = JFactory::getDbo();
		$q = ' SELECT `sub_number` FROM `#__citruscart_userinfo` WHERE `user_id` = '.$id;
		$db->setQuery( $q );
		return $db->loadResult();
	}

	/**
	 * Method which returns the next guest user account ID in the system
	 * (starts off with -11 => reserve 0 ... -10 for later use)
	 *
	 * @return Guest user account ID
	 */
	public function getNextGuestUserId()
	{
		$db = JFactory::getDbo();
		Citruscart::load( 'CitruscartQuery', 'library.query' );
		$q = new CitruscartQuery();
		$start_id = Citruscart::getGuestIdStart();

		$q->select( 'min( tbl.user_id)' );
		$q->from( '#__citruscart_userinfo tbl' );
		$q->where( 'tbl.user_id < '.$start_id );
		$db->setQuery( ( string )$q );
		$res = $db->loadResult();
		if( $res === null ) // no guest account in system
			return $start_id-1; // start off with -11
		else
			return $res - 1; // the last guest account id -1
	}

	/**
	 *
	 * Method to validate a user password via PHP
	 * @param $pass								Password for validation
	 * @param $force_validation		Can forces this method to validate the password, even thought PHP validation is turned off
	 *
	 * @return	Array with result of password validation (position 0) and list of requirements which the password does not fullfil (position 1)
	 */
	function validatePassword( $password, $force_validation=false )
	{
		$errors = array();
		$result = true;
		$defines = Citruscart::getInstance();

		$validate_php = $force_validation || $defines->get( 'password_php_validate', 0 );
		if( !$validate_php ) {
			return array( $result, $errors );
		}

		$min_length = $defines->get( 'password_min_length', 5 );
		$req_num = $defines->get( 'password_req_num', 1 );
		$req_alpha = $defines->get( 'password_req_alpha', 1 );
		$req_spec = $defines->get( 'password_req_spec', 1 );

		if( strlen( $password ) < $min_length )
		{
			$result = false;
			$errors[] = JText::sprintf("COM_CITRUSCART_PASSWORD_MIN_LENGTH", $min_length);
		}

		if( $req_num && !preg_match( '/[0-9]/', $password ) )
		{
			$result = false;
			$errors[] = JText::_("COM_CITRUSCART_PASSWORD_REQ_NUMBER");
		}

		if( $req_alpha && !preg_match( '/[a-zA-Z]/', $password ) )
		{
			$result = false;
			$errors[] = JText::_("COM_CITRUSCART_PASSWORD_REQ_ALPHA");
		}

		if( $req_spec && !preg_match( '/[\\/\|_\-\+=\."\':;\[\]~<>!@?#$%\^&\*()]/', $password ) )
		{
			$result = false;
			$errors[] = JText::_("COM_CITRUSCART_PASSWORD_REQ_SPEC");
		}

		return array( $result, $errors );
	}
}
