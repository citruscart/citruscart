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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once( JPATH_ADMINISTRATOR.'/components/com_media/helpers/media.php' );

class CitruscartModelElementImage extends JModel
{
	function getState($property = null)
	{
		$input = JFactory::getApplication()->input;
		static $set;

		if (!$set) {
			$folder = $input->get( 'folder', '', '', 'path' );
			$this->setState('folder', $folder);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}
		return parent::getState($property);
	}

	function getImages()
	{
		$list = $this->getList();
		return $list['images'];
	}

	function getFolders()
	{
		$list = $this->getList();
		return $list['folders'];
	}

	function getDocuments()
	{
		$list = $this->getList();
		return $list['docs'];
	}

	/**
	 * Image Manager Popup
	 *
	 * @param string $listFolder The image directory to display
	 * @since 1.5
	 */
	function getFolderList($base = null)
	{
		$mainframe = JFactory::getApplication();

		// Get some paths from the request
		if (empty($base)) {
			$base = Citruscart::getPath('images');
		}

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($base, '.', true, true);

		// Load appropriate language files
		$lang = JFactory::getLanguage();
		$lang->load('', JPATH_ADMINISTRATOR);
		$lang->load($mainframe->get( 'option' ), JPATH_ADMINISTRATOR);

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_CITRUSCART_INSERT_IMAGE'));

		// Build the array of select options for the folder list
		$options[] = JHTML::_('select.option', "","/");
		foreach ($folders as $folder) {
			$folder 	= str_replace(Citruscart::getPath('images'), "", $folder);
			$value		= substr($folder, 1);
			$text	 	= str_replace(DS, "/", $folder);
			$options[] 	= JHTML::_('select.option', $value, $text);
		}

		// Sort the folder list array
		if (is_array($options)) {
			sort($options);
		}

		// Create the drop-down folder select list
		$list = JHTML::_('select.genericlist',  $options, 'folderlist', "class=\"inputbox\" size=\"1\" onchange=\"ImageManager.setFolder(this.options[this.selectedIndex].value)\" ", 'value', 'text', $base);
		return $list;
	}

	function getFolderTree($base = null)
	{
		// Get some paths from the request
		if (empty($base)) {
			$base = Citruscart::getPath('images');
		}
		$mediaBase = str_replace(DS, '/', Citruscart::getPath('images').'/');

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($base, '.', true, true);

		$tree = array();
		foreach ($folders as $folder)
		{
			$folder		= str_replace(DS, '/', $folder);
			$name		= substr($folder, strrpos($folder, '/') + 1);
			$relative	= str_replace($mediaBase, '', $folder);
			$absolute	= $folder;
			$path		= explode('/', $relative);
			$node		= (object) array('name' => $name, 'relative' => $relative, 'absolute' => $absolute);

			$tmp = &$tree;
			for ($i=0,$n=count($path); $i<$n; $i++)
			{
				if (!isset($tmp['children'])) {
					$tmp['children'] = array();
				}
				if ($i == $n-1) {
					// We need to place the node
					$tmp['children'][$relative] = array('data' =>$node, 'children' => array());
					break;
				}
				if (array_key_exists($key = implode('/', array_slice($path, 0, $i+1)), $tmp['children'])) {
					$tmp = &$tmp['children'][$key];
				}
			}
		}
		$tree['data'] = (object) array('name' => JText::_('COM_CITRUSCART_MEDIA'), 'relative' => '', 'absolute' => $base);
		return $tree;
	}

	function getList()
	{
		static $list;

		// Only process the list once per request
		if (is_array($list)) {
			return $list;
		}

		// Get current path from request
		$current = $this->getState('folder');

		// If undefined, set to empty
		if ($current == 'undefined') {
			$current = '';
		}

		// Initialize variables
		if (strlen($current) > 0) {
			$basePath = Citruscart::getPath('images').DS.$current;
		} else {
			$basePath = Citruscart::getPath('images');
		}
		$mediaBase = str_replace(DS, '/', Citruscart::getPath('images').'/');

		$images 	= array ();
		$folders 	= array ();
		$docs 		= array ();

		// Get the list of files and folders from the given folder
		$fileList 	= JFolder::files($basePath);
		$folderList = JFolder::folders($basePath);

		// Iterate over the files if they exist
		if ($fileList !== false) {
			foreach ($fileList as $file)
			{
				if (is_file($basePath.DS.$file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html') {
					$tmp = new JObject();
					$tmp->name = $file;
					$tmp->path = str_replace(DS, '/', JPath::clean($basePath.DS.$file));
					$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
					$tmp->size = filesize($tmp->path);

					$ext = strtolower(JFile::getExt($file));
					switch ($ext)
					{
						// Image
						case 'jpg':
						case 'png':
						case 'gif':
						case 'xcf':
						case 'odg':
						case 'bmp':
						case 'jpeg':
							$info = getimagesize($tmp->path);
							$tmp->width		= $info[0];
							$tmp->height	= $info[1];
							$tmp->type		= $info[2];
							$tmp->mime		= $info['mime'];

							$filesize		= MediaHelper::parseSize($tmp->size);

							if (($info[0] > 60) || ($info[1] > 60)) {
								$dimensions = MediaHelper::imageResize($info[0], $info[1], 60);
								$tmp->width_60 = $dimensions[0];
								$tmp->height_60 = $dimensions[1];
							} else {
								$tmp->width_60 = $tmp->width;
								$tmp->height_60 = $tmp->height;
							}

							if (($info[0] > 16) || ($info[1] > 16)) {
								$dimensions = MediaHelper::imageResize($info[0], $info[1], 16);
								$tmp->width_16 = $dimensions[0];
								$tmp->height_16 = $dimensions[1];
							} else {
								$tmp->width_16 = $tmp->width;
								$tmp->height_16 = $tmp->height;
							}
							$images[] = $tmp;
							break;
						// Non-image document
						default:
							$iconfile_32 = JPATH_ADMINISTRATOR."/components/com_media/images/mime-icon-32/".$ext.".png";
							if (file_exists($iconfile_32)) {
								$tmp->icon_32 = "components/com_media/images/mime-icon-32/".$ext.".png";
							} else {
								$tmp->icon_32 = "components/com_media/images/con_info.png";
							}
							$iconfile_16 = JPATH_ADMINISTRATOR."/components/com_media/images/mime-icon-16/".$ext.".png";
							if (file_exists($iconfile_16)) {
								$tmp->icon_16 = "components/com_media/images/mime-icon-16/".$ext.".png";
							} else {
								$tmp->icon_16 = "components/com_media/images/con_info.png";
							}
							$docs[] = $tmp;
							break;
					}
				}
			}
		}

		// Iterate over the folders if they exist
		if ($folderList !== false) {
			foreach ($folderList as $folder) {
				$tmp = new JObject();
				$tmp->name = basename($folder);
				$tmp->path = str_replace(DS, '/', JPath::clean($basePath.DS.$folder));
				$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
				$count = MediaHelper::countFiles($tmp->path);
				$tmp->files = $count[0];
				$tmp->folders = $count[1];

				$folders[] = $tmp;
			}
		}

		$list = array('folders' => $folders, 'docs' => $docs, 'images' => $images);

		return $list;
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function _fetchElement($name, $value='', $node='', $control_name='')
	{
		$mainframe = JFactory::getApplication();

		$db			= JFactory::getDbo();
		$doc 		= JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name ? $control_name.'['.$name.']' : $name;

		if ($value) {
			$title = $value;
		} else {
			$title = JText::_('COM_CITRUSCART_SELECT_AN_IMAGE');
		}

		$js = "
		function jSelectImage(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_citruscart&task=elementImage&tmpl=component&object='.$name;

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		// $html .= "\n &nbsp; <input class=\"inputbox modal-button\" type=\"button\" value=\"".JText::_('COM_CITRUSCART_SELECT')."\" />";
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('COM_CITRUSCART_SELECT_AN_IMAGE').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.JText::_('COM_CITRUSCART_SELECT').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function _clearElement($name, $value='', $node='', $control_name='')
	{

		$mainframe = JFactory::getApplication();

		$db			= JFactory::getDbo();
		$doc 		= JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name ? $control_name.'['.$name.']' : $name;

		$js = "
		function resetElement(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
		}";
		$doc->addScriptDeclaration($js);

		$html = '<div class="button2-left">
		<div class="blank">

		<a href="javascript::void();" onclick="resetElement( \''.$value.'\', \''.JText::_('COM_CITRUSCART_SELECT_AN_IMAGE').'\', \''.$name.'\' )">'.JText::_('COM_CITRUSCART_CLEAR_SELECTION').'</span>
		</div></div>'."\n";

		return $html;
	}

}

