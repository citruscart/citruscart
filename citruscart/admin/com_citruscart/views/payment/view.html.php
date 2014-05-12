<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base' );

class CitruscartViewPayment extends CitruscartViewBase

{
	function getLayoutVars($tpl=null)
	{
		$app =JFactory::getApplication();
		$layout = $this->getLayout();
		$this->renderSubmenu();
		switch(strtolower($layout))
		{
			case "view":
			    $this->set( 'leftMenu', 'leftmenu_configuration' );
				$this->_form($tpl);
			  break;
			case "form":
				$app->input->set('hidemainmenu', '1');
				$this->_form($tpl);
			  break;
			case "default":
			default:
				$this->set( 'leftMenu', 'leftmenu_configuration' );
				$this->_default($tpl);
			  break;
		}
	}

	function _form($tpl=null)
	{
        parent::_form($tpl);

        // the row is already loaded in base view so this might not be needed leaving this hear to help figure this out in joomla 1.5 if needed.
       if(!version_compare(JVERSION,'1.6.0','ge')) {
		$row = $this->getModel()->getItem();
		$params = new DSCParameter( $row->params, $this->getPath( 'plg_xml', $row->folder.'/'.$row->element), 'plugin' );
		$this->assignRef('params',$params);
	   }

	}

	/**
	 * Get a path
	 *
	 * @param   string  $varname      Identify location or type of xml
	 * @param   string  $user_option  Option (e.g. com_something) used to find path.
	 *
	 * @return  string  The requested path
	 *
	 * @since   11.1
	 * @deprecated  12.1
	 */
	public static function getPath($varname, $user_option = null)
	{
		$app = JFactory::getApplication();
		// Check needed for handling of custom/new module XML file loading
		$check = (($varname == 'mod0_xml') || ($varname == 'mod1_xml'));

		if (!$user_option && !$check)
		{
			$user_option = $app->input->get('option');
		}
		else
		{
			$user_option = JFilterInput::getInstance()->clean($user_option, 'path');
		}

		$result = null;
		$name = substr($user_option, 4);

		switch ($varname)
		{
			case 'front':
				$result = $this->_checkPath('/components/' . $user_option . '/' . $name . '.php', 0);
				break;

			case 'html':
			case 'front_html':
				if (!($result = $this->_checkPath('/templates/' . JApplication::getTemplate() . '/components/' . $name . '.html.php', 0)))
				{
					$result = $this->_checkPath('/components/' . $user_option . '/' . $name . '.html.php', 0);
				}
				break;

			case 'toolbar':
				$result = $this->_checkPath('/components/' . $user_option . '/toolbar.' . $name . '.php', -1);
				break;

			case 'toolbar_html':
				$result = $this->_checkPath('/components/' . $user_option . '/toolbar.' . $name . '.html.php', -1);
				break;

			case 'toolbar_default':
			case 'toolbar_front':
				$result = $this->_checkPath('/includes/HTML_toolbar.php', 0);
				break;

			case 'admin':
				$path = '/components/' . $user_option . '/admin.' . $name . '.php';
				$result = $this->_checkPath($path, -1);
				if ($result == null)
				{
					$path = '/components/' . $user_option . '/' . $name . '.php';
					$result = $this->_checkPath($path, -1);
				}
				break;

			case 'admin_html':
				$path = '/components/' . $user_option . '/admin.' . $name . '.html.php';
				$result = $this->_checkPath($path, -1);
				break;

			case 'admin_functions':
				$path = '/components/' . $user_option . '/' . $name . '.functions.php';
				$result = $this->_checkPath($path, -1);
				break;

			case 'class':
				if (!($result = $this->_checkPath('/components/' . $user_option . '/' . $name . '.class.php')))
				{
					$result = $this->_checkPath('/includes/' . $name . '.php');
				}
				break;

			case 'helper':
				$path = '/components/' . $user_option . '/' . $name . '.helper.php';
				$result = $this->_checkPath($path);
				break;

			case 'com_xml':
				$path = '/components/' . $user_option . '/' . $name . '.xml';
				$result = $this->_checkPath($path, 1);
				break;

			case 'mod0_xml':
				$path = '/modules/' . $user_option . '/' . $user_option . '.xml';
				$result = $this->_checkPath($path);
				break;

			case 'mod1_xml':
				// Admin modules
				$path = '/modules/' . $user_option . '/' . $user_option . '.xml';
				$result = $this->_checkPath($path, -1);
				break;

			case 'plg_xml':
				// Site plugins
				$j15path = '/plugins/' . $user_option . '.xml';
				$parts = explode(DIRECTORY_SEPARATOR, $user_option);
				$j16path = '/plugins/' . $user_option . '/' . $parts[1] . '.xml';
				$j15 = $this->_checkPath($j15path, 0);
				$j16 = $this->_checkPath($j16path, 0);
				// Return 1.6 if working otherwise default to whatever 1.5 gives us
				$result = $j16 ? $j16 : $j15;
				break;

			case 'menu_xml':
				$path = '/components/com_menus/' . $user_option . '/' . $user_option . '.xml';
				$result = $this->_checkPath($path, -1);
				break;
		}
		return  $result;
	}

	protected static function _checkPath($path, $checkAdmin = 1)
	{
		$file = JPATH_SITE . $path;
		if ($checkAdmin > -1 && file_exists($file))
		{
			return $file;
		}
		elseif ($checkAdmin != 0)
		{
			$file = JPATH_ADMINISTRATOR . $path;
			if (file_exists($file))
			{
				return $file;
			}
		}

		return null;
	}

	function _defaultToolbar()
	{
	}

    function _viewToolbar( $isNew = null )
    {
    	JToolBarHelper::custom( 'view', 'forward', 'forward', 'COM_CITRUSCART_SUBMIT', false );
    	JToolBarHelper::cancel( 'close', 'COM_CITRUSCART_CLOSE' );
    }

    /**
     * The default toolbar for editing an item
     * @param $isNew
     * @return unknown_type
     */
    function _formToolbar( $isNew=null )
    {
        $divider = false;
        $surrounding = (!empty($this->surrounding)) ? $this->surrounding : array();
        if (!empty($surrounding['prev']))
        {
            $divider = true;
            JToolBarHelper::custom('saveprev', "saveprev", "saveprev", 'COM_CITRUSCART_SAVE_PLUS_PREV', false);
        }
        if (!empty($surrounding['next']))
        {
            $divider = true;
            JToolBarHelper::custom('savenext', "savenext", "savenext", 'COM_CITRUSCART_SAVE_PLUS_NEXT', false);
        }
        if ($divider)
        {
            JToolBarHelper::divider();
        }

        JToolBarHelper::save('save');
        JToolBarHelper::apply('apply');

        if ($isNew)
        {
            JToolBarHelper::cancel();
        }
            else
        {
            JToolBarHelper::cancel( 'close', 'COM_CITRUSCART_CLOSE' );
        }
    }
}
