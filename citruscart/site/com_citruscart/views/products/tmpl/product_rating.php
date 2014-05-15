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
$rating = $this->rating;

/* Get the document */
$doc = JFactory::getDocument();
$doc->addScript(JUri::root().'media/citruscart/js/citruscart.js');

if( $rating->clickable )
{
	for( $i = 1; $i <= $rating->count; $i++ ) : ?>
		<span id="rating_<?php echo $i; ?>">
	   	<a href="javascript:void(0);" onclick="citruscartRating(<?php echo $i; ?>);">
		   	<img id="rate_<?php echo $i; ?>" src="media/citruscart/images/star_00.png" alt="<?php echo $i?>">
			</a>
		</span>
	<?php endfor;
}
else
{
	switch ( $rating->rating )
	{
		case "5":
				$src = Citruscart::getURL( 'ratings' )."five.png";
				$alt = JText::_('COM_CITRUSCART_GREAT');
				$title = JText::_('COM_CITRUSCART_GREAT');
				$name = JText::_('COM_CITRUSCART_GREAT');
			break;
		case "4.5":
				$src = Citruscart::getURL( 'ratings' )."four_half.png";
				$alt = JText::_('COM_CITRUSCART_GREAT');
				$title = JText::_('COM_CITRUSCART_GREAT');
				$name = JText::_('COM_CITRUSCART_GREAT');
			break;
		case "4":
				$src = Citruscart::getURL( 'ratings' )."four.png";
				$alt = JText::_('COM_CITRUSCART_GOOD');
				$title = JText::_('COM_CITRUSCART_GOOD');
				$name = JText::_('COM_CITRUSCART_GOOD');
			break;
		case "3.5":
				$src = Citruscart::getURL( 'ratings' )."three_half.png";
				$alt = JText::_('COM_CITRUSCART_GREAT');
				$title = JText::_('COM_CITRUSCART_GREAT');
				$name = JText::_('COM_CITRUSCART_GREAT');
			break;
		case "3":
				$src = Citruscart::getURL( 'ratings' )."three.png";
				$alt = JText::_('COM_CITRUSCART_AVERAGE');
				$title = JText::_('COM_CITRUSCART_AVERAGE');
				$name = JText::_('COM_CITRUSCART_AVERAGE');
			break;
		case "2.5":
				$src = Citruscart::getURL( 'ratings' )."two_half.png";
				$alt = JText::_('COM_CITRUSCART_AVERAGE');
				$title = JText::_('COM_CITRUSCART_AVERAGE');
				$name = JText::_('COM_CITRUSCART_AVERAGE');
			break;
		case "2":
				$src = Citruscart::getURL( 'ratings' )."two.png";
				$alt = JText::_('COM_CITRUSCART_POOR');
				$title = JText::_('COM_CITRUSCART_POOR');
				$name = JText::_('COM_CITRUSCART_POOR');
			break;
		case "1.5":
				$src = Citruscart::getURL( 'ratings' )."one_half.png";
				$alt = JText::_('COM_CITRUSCART_POOR');
				$title = JText::_('COM_CITRUSCART_POOR');
				$name = JText::_('COM_CITRUSCART_POOR');
			break;
		case "1":
				$src = Citruscart::getURL( 'ratings' )."one.png";
				$alt = JText::_('COM_CITRUSCART_UNSATISFACTORY');
				$title = JText::_('COM_CITRUSCART_UNSATISFACTORY');
				$name = JText::_('COM_CITRUSCART_UNSATISFACTORY');
			break;
		case "0.5":
				$src = Citruscart::getURL( 'ratings' )."zero_half.png";
				$alt = JText::_('COM_CITRUSCART_UNSATISFACTORY');
				$title = JText::_('COM_CITRUSCART_UNSATISFACTORY');
				$name = JText::_('COM_CITRUSCART_UNSATISFACTORY');
			break;
		default:
				$src = Citruscart::getURL( 'ratings' )."zero.png";
				$alt = JText::_('COM_CITRUSCART_UNRATED');
				$title = JText::_('COM_CITRUSCART_UNRATED');
				$name = JText::_('COM_CITRUSCART_UNRATED');
			break;
	}

	echo "<img src='".$src."' alt='".$alt."' title='".$title."' name='".$name."' align='center' border='0' />";
}