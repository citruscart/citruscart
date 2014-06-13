<?php
/**
 * @version		$Id: view.php 10710 2008-08-21 10:08:12Z eddieajau $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML Element View class
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class CitruscartViewElementImage extends JView
{
	function display()
	{
		$mainframe = JFactory::getApplication();
		
		// Initialize variables
		$db			= JFactory::getDbo();
		$nullDate	= $db->getNullDate();

		$document	= JFactory::getDocument();
		$document->setTitle('Image Selection');

		JHTML::_('behavior.modal');

		$template = $mainframe->getTemplate();
		$document->addStyleSheet("templates/$template/css/general.css");
		
		$app = JFactory::getApplication();
		$append = '';
		if($app->getClientId() == 1) $append = 'administrator/';
		JHTML::_('script'    , 'popup-imagemanager.js', $append .'components/com_media/assets/');
		JHTML::_('stylesheet', 'popup-imagemanager.css', $append .'components/com_media/assets/');

		$limitstart = JRequest::getVar('limitstart', '0', '', 'int');

		$lists = $this->_getLists();

		$rows = $this->get('List');
		$page = $this->get('Pagination');
		JHTML::_('behavior.tooltip');
		
		$config = JComponentHelper::getParams('com_media');
		
		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		jimport('joomla.client.helper');
		$ftp = !JClientHelper::hasCredentials('ftp');

		//$model = JModelLegacy::getInstance('ElementImage', 'CitruscartModel');
		$this->assign( 'session',	JFactory::getSession());
		$this->assign( 'config',		$config);
		$this->assign( 'state',		$this->get('state'));
		$this->assign( 'folderList',	$this->get('folderList'));
		$this->assign('require_ftp', $ftp);
		
		$object = JRequest::getVar( 'object' );
		$link = 'index.php?option=com_citruscart&task=elementImage&tmpl=component&object='.$object;

		?>
		<script type='text/javascript'>
		var image_base_path = '<?php echo Citruscart::getPath('images'); ?>/';
		</script>
		<form action="<?php echo $link; ?>" id="imageForm" method="post" enctype="multipart/form-data">
			<div id="messages" style="display: none;">
				<span id="message"></span><img src="<?php echo JURI::base() ?>components/com_media/images/dots.gif" width="22" height="12" alt="..." />
			</div>
			<fieldset>
				<div style="float: left">
					<label for="folder"><?php echo JText::_('COM_CITRUSCART_DIRECTORY') ?></label>
					<?php echo $this->folderList; ?>
					<button type="button" id="upbutton" title="<?php echo JText::_('COM_CITRUSCART_DIRECTORY_UP') ?>"><?php echo JText::_('COM_CITRUSCART_UP') ?></button>
				</div>
				<div style="float: right">
					<button type="button" onclick="ImageManager.onok();window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_('COM_CITRUSCART_INSERT') ?></button>
					<button type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_('COM_CITRUSCART_CANCEL') ?></button>
				</div>
			</fieldset>
			<iframe id="imageframe" name="imageframe" src="index.php?option=com_media&amp;view=imagesList&amp;tmpl=component&amp;folder=<?php echo $this->state->folder?>"></iframe>
		
			<fieldset>
				<table class="properties">
					<tr>
						<td><label for="f_url"><?php echo JText::_('COM_CITRUSCART_IMAGE_URL') ?></label></td>
						<td><input type="text" id="f_url" value="" /></td>
						<td><label for="f_align"><?php echo JText::_('COM_CITRUSCART_ALIGN') ?></label></td>
						<td>
							<select size="1" id="f_align" title="Positioning of this image">
								<option value="" selected="selected"><?php echo JText::_('COM_CITRUSCART_NOT_SET') ?></option>
								<option value="left"><?php echo JText::_('COM_CITRUSCART_LEFT') ?></option>
								<option value="right"><?php echo JText::_('COM_CITRUSCART_RIGHT') ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="f_alt"><?php echo JText::_('COM_CITRUSCART_IMAGE_DESCRIPTION') ?></label></td>
						<td><input type="text" id="f_alt" value="" /></td>
					</tr>
					<tr>
						<td><label for="f_title"><?php echo JText::_('COM_CITRUSCART_TITLE') ?></label></td>
						<td><input type="text" id="f_title" value="" /></td>
						<td><label for="f_caption"><?php echo JText::_('COM_CITRUSCART_CAPTION') ?></label></td>
						<td><input type="checkbox" id="f_caption" /></td>
					</tr>
				</table>
			</fieldset>
			<input type="hidden" id="dirPath" name="dirPath" />
			<input type="hidden" id="f_file" name="f_file" />
			<input type="hidden" id="tmpl" name="component" />
		</form>
		
		<form action="<?php echo JURI::base(); ?>index.php?option=com_media&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;pop_up=1&amp;<?php echo JSession::getFormToken();?>=1" id="uploadForm" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend><?php echo JText::_('COM_CITRUSCART_UPLOAD'); ?></legend>
				<fieldset class="actions">
					<input type="file" id="file-upload" name="Filedata" />
					<input type="submit" id="file-upload-submit" value="<?php echo JText::_('COM_CITRUSCART_START_UPLOAD'); ?>"/>
					<span id="upload-clear"></span>
				</fieldset>
				<ul class="upload-queue" id="upload-queue">
					<li style="display: none" />
				</ul>
			</fieldset>
			<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_media&view=images&tmpl=component&e_name='.JRequest::get('e_name')); ?>" />
		</form>
		<?php
	}

	function _getLists()
	{
		$mainframe = JFactory::getApplication();

		// Initialize variables
		$db		= JFactory::getDbo();

		// Get some variables from the request
		$option				= JRequest::get( 'option' );
		$filter_order		= $mainframe->getUserStateFromRequest('imageelement.filter_order',		'filter_order',		'',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest('imageelement.filter_order_Dir',	'filter_order_Dir',	'',	'word');
		$filter_state		= $mainframe->getUserStateFromRequest('imageelement.filter_state',		'filter_state',		'',	'word');
		$limit				= $mainframe->getUserStateFromRequest('global.list.limit',					'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart			= $mainframe->getUserStateFromRequest('imageelement.limitstart',			'limitstart',		0,	'int');
		$search				= $mainframe->getUserStateFromRequest('imageelement.search',				'search',			'',	'string');
		$search				= JString::strtolower($search);

		// search filter
		$lists['search'] = $search;

		return $lists;
	}
}
