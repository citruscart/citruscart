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

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class CitruscartHelperMenu extends CitruscartHelperBase
{
    /**
     * Determines if the Citruscart admin-side submenu module is enabled 
     * @return unknown_type
     */
    function isSubmenuEnabled()
    {
    	if(version_compare(JVERSION,'1.6.0','ge')) {
        $query = "SELECT `enabled` FROM #__extensions WHERE `element` = 'mod_citruscart_admin_submenu';";
		} else {
		 $query = "SELECT `published` FROM #__modules WHERE `module` = 'mod_citruscart_admin_submenu';";	
		}
        $db = JFactory::getDBO();
        $db->setQuery( $query );
        $result = $db->loadResult();
        return $result;
		
    }
    
    /**
     * Whether using the admin-side submenu module or just using the menu tmpl (from the dashboard view),
     * tells Citruscart to display the submenu
     * 
     * @param $menu
     * @return unknown_type
     */
    function display( $menu_name='submenu' )
    {
    	/* Get the application */
    	$app = JFactory::getApplication();
        if (!$this->isSubmenuEnabled())
        {
            $menu = CitruscartMenu::getInstance( $menu_name );
        }
            else
        {
        	$app->input->set('citruscart_display_submenu', '1');
            //JRequest::setVar('Citruscart_display_submenu', '1'); // tells the Citruscart_admin_submenu module to display
        }
    }
}
