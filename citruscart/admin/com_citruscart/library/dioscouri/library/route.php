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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class DSCRoute
{
	/**
	 * Translates an internal Joomla URL to a humanly readible URL.
	 *
	 * @param   string   $url    Absolute or Relative URI to Joomla resource.
	 * @param   boolean  $xhtml  Replace & by &amp; for XML compilance.
	 * @param   integer  $ssl    Secure state for the resolved URI.
	 *                             1: Make URI secure using global secure site URI.
	 *                             0: Leave URI in the same secure state as it was passed to the function.
	 *                            -1: Make URI unsecure using the global unsecure site URI.
	 *
	 * @return  The translated humanly readible URL.
	 *
	 * @since   11.1
	 */
	public static function _($url, $xhtml = true, $ssl = null, $findItemid = true)
	{
		// Get the router.
		$app = JFactory::getApplication();
		$router = $app->getRouter();

		// Make sure that we have our router
		if (!$router)
		{
			return null;
		}

		if ((strpos($url, '&') !== 0) && (strpos($url, 'index.php') !== 0))
		{
			return $url;
		}
		//TODO do better Itemid check  so we can check if actually has a value
		if(!strpos($url, 'Itemid=') && $findItemid) {
			$itemid = DSCRoute::findItemid($url);
			if($itemid)
				$url = $url . '&Itemid='.$itemid ;

		}

		// Build route.
		$uri = $router->build($url);
		$url = $uri->__toString(array('path', 'query', 'fragment'));

		// Replace spaces.
		$url = preg_replace('/\s/u', '%20', $url);

		/*
		 * Get the secure/unsecure URLs.
		 *
		 * If the first 5 characters of the BASE are 'https', then we are on an ssl connection over
		 * https and need to set our secure URL to the current request URL, if not, and the scheme is
		 * 'http', then we need to do a quick string manipulation to switch schemes.
		 */
		if ((int) $ssl)
		{
			$uri = JURI::getInstance();

			// Get additional parts.
			static $prefix;
			if (!$prefix)
			{
				$prefix = $uri->__toString(array('host', 'port'));
			}

			// Determine which scheme we want.
			$scheme = ((int) $ssl === 1) ? 'https' : 'http';

			// Make sure our URL path begins with a slash.
			if (!preg_match('#^/#', $url))
			{
				$url = '/' . $url;
			}

			// Build the URL.
			$url = $scheme . '://' . $prefix . $url;
		}

		if ($xhtml)
		{
			$url = htmlspecialchars($url);
		}

		return $url;
	}

	public static function findItemid ($url) {
		//TODO add CACHING
		$db = JFactory::getDBO();
		$query = NEW DSCQuery();
		$query->select('*');
        $query->from(' #__menu as m');
        $query->where(' m.link = '. $db->q( $url )  );
        $query->where(' m.client_id = '.  $db->q(0)); //means frontend only
        $query->where(' m.published = '. $db->q( 1 )); //means turned on
        $db->setQuery($query, 0 , 1);
        $item =  $db->loadObject();
        if ( $item)
		 return  $item->id;

	}

}

