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
require_once(JPATH_SITE.'/libraries/dioscouri/library/article.php');
class CitruscartArticle extends DSCArticle
{


	/**
	 *
	 * @return unknown_type
	 */
/*	function display( $articleid )
	{
		global $mainframe;
		$html = '';

		$dispatcher	   = JDispatcher::getInstance();

		$article = JTable::getInstance('content');
		$article->load( $articleid );
		// Return html if the load fails
		if (!$article->id)
		{
			return $html;
		}
		$article->text = $article->introtext . chr(13).chr(13) . $article->fulltext;

		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		//get the com_content application param
		if($mainframe->isAdmin())
		{
			 jimport( 'joomla.application.component.helper' );
			 $params = JComponentHelper::getParams("com_content");
		}
		else
		{
			$application = JFactory::getApplication();
			$params		=& $application->getParams('com_content');
		}

		$aparams	=& $article->attribs;
		$params->merge($aparams);

		// merge isn't overwriting the global component params, so using this
		$article_params = new DSCParameter( $article->attribs );

		// Fire Content plugins on the article so they change their tags
		/*
		 * Process the prepare content plugins
		 */
	/*		JPluginHelper::importPlugin('content');
			$results = JFactory::getApplication()->triggerEvent('onPrepareContent', array (& $article, & $params, $limitstart));

		/*
		 * Handle display events
		 */
	/*		$article->event = new stdClass();
			$results = JFactory::getApplication()->triggerEvent('onAfterDisplayTitle', array (& $article, &$params, $limitstart));
			$article->event->afterDisplayTitle = trim(implode("\n", $results));

			$results = JFactory::getApplication()->triggerEvent('onBeforeDisplayContent', array (& $article, & $params, $limitstart));
			$article->event->beforeDisplayContent = trim(implode("\n", $results));

			$results = JFactory::getApplication()->triggerEvent('onAfterDisplayContent', array (& $article, & $params, $limitstart));
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
	}*/
}