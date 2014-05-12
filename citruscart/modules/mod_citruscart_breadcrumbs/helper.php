<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class modCitruscartBreadcrumbsHelper extends JObject
{
    /**
     * Sets the modules params as a property of the object
     * param unknown_type $params
     * return unknown_type
     */
    function __construct( $params )
    {
        $this->params = $params;
    }

    /**
     * Method to build the pathway/breadcrumbs
     * return string
     */
    function pathway()
    {
    	$input = JFactory::getApplication()->input;
    	$pathway = '';
    	$catid = $input->getInt('filter_category');

    	if($this->params->get('showhome'))
    	{
    		$homeText = $this->params->get('hometext');
    		$homeText = empty($homeText) ? JText::_('COM_CITRUSCART_HOME') : $homeText;
    		$pathway .= " <a href='index.php'>".$homeText.'</a> ';
    	}

    	// get the root category
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
        $root = JTable::getInstance('Categories', 'CitruscartTable')->getRoot();
        $root_itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->category($root->category_id, true);

        $catRoot = $this->params->get('showcatroot', '1');
        if ($catRoot && $catid != $root->category_id)
        {
        	$pathway .= $this->getSeparator();
        	$link = JRoute::_( "index.php?option=com_citruscart&view=products&filter_category=".$root->category_id."&Itemid=".$root_itemid, false );
            $rootText = $this->params->get('roottext');
    		$rootText = empty($rootText) ? JText::_('COM_CITRUSCART_ALL_CATEGORIES') : $rootText;
        	$pathway .= " <a href='$link'>".$rootText.'</a> ';
        }

		$table = JTable::getInstance('Categories', 'CitruscartTable');
        $table->load( $catid );

		if (empty($table->category_id))
		{
			return $pathway;
		}

		$path = $table->getPath();

     	foreach ($path as $cat)
		{
			if (!$cat->isroot)
			{
				if (!$itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->category($cat->category_id, true))
			    {
			    	$itemid = $root_itemid;
			    }
			    $slug = $cat->category_alias ? ":$cat->category_alias" : "";
			    $link = JRoute::_("index.php?option=com_citruscart&view=products&filter_category=".$cat->category_id.$slug."&Itemid=".$itemid, false);

			    if (!empty($pathway)) { $pathway .= $this->getSeparator(); }

			    $pathway .= " <a href='$link'>".JText::_( $cat->category_name ).'</a> ';
			}
		}

     	if(!empty($pathway))
     	{
     		$pathway .= $this->getSeparator();
     	}

   		if ($linkSelf = $this->params->get('linkself'))
        {
            if (!$itemid = Citruscart::getClass( "CitruscartHelperRoute", 'helpers.route' )->category($table->category_id, true))
            {
            	$itemid = $root_itemid;
            }
            $slug = $table->category_alias ? ":$table->category_alias" : "";
            $link = JRoute::_("index.php?option=com_citruscart&view=products&filter_category=".$table->category_id.$slug."&Itemid=".$itemid, false);
        	$pathway .= " <a href='$link'>".JText::_( $table->category_name ).'</a> ';
        }
        else
        {
        	$pathway .= JText::_( $table->category_name );
        }

        return $pathway;
    }

    /**
     * Method to get the separator
     * return string
     */
    function getSeparator()
    {
    	$text = '';
    	$text = $this->params->get('separator', '>');
    	$text = empty($text) ? " > " : " {$text} ";
    	return $text;
    }
}

