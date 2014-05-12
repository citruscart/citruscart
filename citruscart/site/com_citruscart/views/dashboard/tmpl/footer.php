<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php

$url = "http://www.dioscouri.com/";
if ($amigosid = Citruscart::getInstance()->get( 'amigosid', '' ))
{
    $url .= "?amigosid=".$amigosid;
}
?>

<?php if (Citruscart::getInstance()->get('show_linkback')) : ?>
<p align="center">
	<?php echo JText::_('COM_CITRUSCART_POWERED_BY')." <a href='{$url}' target='_blank'>".JText::_('COM_CITRUSCART_CITRUSCART_ECOMMERCE')."</a>"; ?>
</p>
<?php endif; ?>
