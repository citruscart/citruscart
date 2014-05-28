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

class DSCArticle extends JObject
{
	/**
	 * Takes a simple product description an formats it like an article
	 * to trick payment plugins into acting on it
	 *
	 * @param string $text
	 * @return string HTML
	 */
	public static function fromString( $text )
	{
        $mainframe = JFactory::getApplication();
        $params       = $mainframe->getParams('com_content');

        

		$article = JTable::getInstance('content');
		$article->text = $text;

		$limitstart = 0;

        /*
         * Process the prepare content plugins
         */
        JPluginHelper::importPlugin('content');
        $results = JFactory::getApplication()->triggerEvent('onPrepareContent', array (& $article, & $params, $limitstart));

        /*
         * Handle display events
         */
        $article->event = new stdClass();

        // TODO Since there is no title, do we include this event?
        $results = JFactory::getApplication()->triggerEvent('onAfterDisplayTitle', array ($article, &$params, $limitstart));
        $article->event->afterDisplayTitle = trim(implode("\n", $results));

        $results = JFactory::getApplication()->triggerEvent('onBeforeDisplayContent', array (& $article, & $params, $limitstart));
        $article->event->beforeDisplayContent = trim(implode("\n", $results));

        $results = JFactory::getApplication()->triggerEvent('onAfterDisplayContent', array (& $article, & $params, $limitstart));
        $article->event->afterDisplayContent = trim(implode("\n", $results));

        // collect $html
        $html = '';
        $html .= $article->event->afterDisplayTitle;
        $html .= $article->event->beforeDisplayContent;
        $html .= $article->text;
        $html .= $article->event->afterDisplayContent;

        return $html;

	}

	/**
	 *
	 * @return unknown_type
	 */
	public static function display( $articleid )
	{
		JPluginHelper::importPlugin('citruscart');
		$html = '';
		$mainframe = JFactory::getApplication();
		$article = JTable::getInstance('content');
		$article->load( $articleid );
		// Return html if the load fails
		if (!$article->id)
		{
			return $html;
		}
		$article->text = $article->introtext . chr(13).chr(13) . $article->fulltext;

		$limitstart	=$mainframe->input->getInt('limitstart', 0);
		$params		= $mainframe->getParams('com_content');
		$aparams	= $article->attribs;
		$params->merge($aparams);
		// merge isn't overwriting the global component params, so using this
		jimport('joomla.html.parameter');
		$article_params = new JParameter( $article->attribs );

		// Fire Content plugins on the article so they change their sample
		/*
		 * Process the prepare content plugins
		 */
		JPluginHelper::importPlugin('content');
		$results = JFactory::getApplication()->triggerEvent('onPrepareContent', array (& $article, & $params, $limitstart));

		/*
		 * Handle display events
		 */
		$article->event = new stdClass();
		$results = $mainframe->triggerEvent('onAfterDisplayTitle', array ($article, &$params, $limitstart));
		$article->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $mainframe->triggerEvent('onBeforeDisplayContent', array (& $article, & $params, $limitstart));
		$article->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $mainframe->triggerEvent('onAfterDisplayContent', array (& $article, & $params, $limitstart));
		$article->event->afterDisplayContent = trim(implode("\n", $results));

		// Use param for displaying article title
		$show_title = $article_params->get('show_title', $params->get('show_title') );
		if ($show_title)
		{
			$html .= "<h3>{$article->title}</h3>";
		}
		$html .= $article->event->afterDisplayTitle;
		$html .= $article->event->beforeDisplayContent;
		$html .= $article->text;
		$html .= $article->event->afterDisplayContent;

		return $html;
	}
}