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

// no direct access
defined('_JEXEC') or die('Restricted access');?>

	<?php
		$img_file = "dioscouri_logo_transparent.png";
		$img_path = "../media/citruscart/images";

		JPluginHelper::importPlugin('Citruscart');

		$results = JFactory::getApplication()->triggerEvent( 'onGetFooter', array() );

		$html = implode('', $results);
		echo $html;

		$url = "http://www.citruscart.com/";
		if ($amigosid = Citruscart::getInstance()->get( 'amigosid', '' ))
		{
			$url .= "?amigosid=".$amigosid;
		}
	?>

	<table style="margin-bottom: 5px; width: 100%; border-top: thin solid #e5e5e5;">
	<tbody>
	<tr>
		<td style="text-align: center; width: 33%;">
			<?php echo JText::_('COM_CITRUSCART_CITRUSCART'); ?>: <?php echo JText::_('COM_CITRUSCART_CITRUSCART_DESC'); ?>
			<br/>
			<?php echo JText::_('COM_CITRUSCART_COPYRIGHT'); ?>: <?php echo Citruscart::getInstance()->getCopyrightYear(); ?> &copy; <a href="<?php echo $url; ?>" target="_blank"><?php echo JText::_('COM_CITRUSCART');?></a>
			<br/>
			<?php echo JText::_('COM_CITRUSCART_VERSION'); ?>: <?php echo Citruscart::getInstance()->getVersion(); ?>
			<br/>
			<?php echo sprintf( JText::_('COM_CITRUSCART_PHP_VERSION_LINE'), Citruscart::getInstance()->getMinPhp(), Citruscart::getInstance()->getServerPhp() );?>
		</td>
	</tr>
	</tbody>
	</table>
