<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

/** Import library dependencies */
jimport('joomla.plugin.plugin');

if ( !class_exists('Citruscart') )
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

class plgUserCitruscart extends JPlugin
{

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $language = JFactory::getLanguage();
		$language -> load('plg_user_citruscart', JPATH_ADMINISTRATOR, 'en-GB', true);
		$language -> load('plg_user_citruscart', JPATH_ADMINISTRATOR, null, true);
    }

    /**
     * When the user logs in, their session cart should override their db-stored cart.
     * Current actions take precedence
     * For Joomla 1.5
     *
     * @param $user
     * @param $options
     * @return unknown_type
     */
    public function onLoginUser($user, $options = array())
    {
      return $this->doLoginUser($user, $options);
    }

    /**
     * When the user logs in, their session cart should override their db-stored cart.
     * Current actions take precedence
     *
     * @param $user
     * @param $options
     * @return unknown_type
     */
    public function onUserLogin($user, $options = array())
    {
      return $this->doLoginUser($user, $options);
    }

    /**
     * When the user logs in, their session cart should override their db-stored cart.
     * Current actions take precedence
     *
     * @param $user
     * @param $options
     * @return unknown_type
     */
    private function doLoginUser( $user, $options = array() )
    {
    	$session = JFactory::getSession();
    	$old_sessionid = $session->get( 'old_sessionid' );

    	$user['id'] = intval(JUserHelper::getUserId($user['username']));

    	// Should check that Citruscart is installed first before executing
        if (!$this->_isInstalled())
        {
            return;
        }

        Citruscart::load( 'CitruscartHelperCarts', 'helpers.carts' );
        $helper = new CitruscartHelperCarts();
        if (!empty($old_sessionid))
        {
            $helper->mergeSessionCartWithUserCart( $old_sessionid, $user['id'] );

            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
		    $wishlist_model = JModelLegacy::getInstance( 'WishlistItems', 'CitruscartModel' );
            $wishlist_model->setUserForSessionItems( $old_sessionid, $user['id'] );
        }
            else
        {
            $helper->updateUserCartItemsSessionId( $user['id'], $session->getId() );
        }

        $this->checkUserGroup();

       return true;
    }

    /**
     * Checks the extension is installed
     *
     * @return boolean
     */
    function _isInstalled()
    {
        $success = false;

        jimport('joomla.filesystem.file');
        if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_citruscart/defines.php'))
        {
            $success = true;
        }
        return $success;
    }

    /**
     * check whether user belongs to a group or not
     * in case not then add them to the default group
     *
     * @return unknown type
     */
    private function checkUserGroup()
    {
        $user = JFactory::getUser();
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
        $user_groups = JTable::getInstance('UserGroups', 'CitruscartTable');
        $user_groups->load(array('user_id'=>$user->id));

        if (empty($user_groups->group_id))
        {
            $user_groups->group_id = Citruscart::getInstance()->get('default_user_group', '1'); ; // If there is no user selected then it will consider as default user group
            $user_groups->user_id = $user->id;
            if (!$user_groups->save())
            {
            	// TODO if data does not save in the mapping table, what to do?
            }
        }
    }
}
