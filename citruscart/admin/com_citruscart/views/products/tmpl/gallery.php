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

JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/');
JHTML::_('stylesheet', 'citruscart.js', 'media/com_citruscart/js/');
$app = JFactory::getApplication();
$images =$this->images;
$path = $this->url;
Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
Citruscart::load( 'CitruscartUrl', 'library.url' );
$product_id = $app->input->getInt('id', 0);
$update_parent = $app->input->getString('update_parent');
if (!empty($update_parent))
{
    ?>
    <script type="text/javascript">
    window.parent.CitruscartUpdateParentDefaultImage('<?php echo $product_id; ?>');
    </script>
    <?php
}
?>
<div id="gallery">
	<table border="0">
		<tr>

			<?php
			foreach($images as $i){
				?>
				<td>
				<?php echo CitruscartUrl::popup( $path.$i, '<img src="'.$path."thumbs/".$i.'" style="vertical-align: bottom;" />', array('update' => false, 'img' => true));?>
				</td>
				<?php
			}
			?>

	</tr>
	<tr>

		<?php
		foreach($images as $i){
		?>
		<td>
			<a href="index.php?option=com_citruscart&controller=products&task=deleteImage&product_id=<?php echo $product_id?>&image=<?php echo $i; ?>"><?php echo JText::_('COM_CITRUSCART_DELETE'); ?></a><br />
			<a href="index.php?option=com_citruscart&controller=products&task=setDefaultImage&product_id=<?php echo $product_id?>&image=<?php echo $i; ?>"><?php echo JText::_('COM_CITRUSCART_MAKE_DEFAULT'); ?></a>
		</td>
		<?php
		}
		?>

	</tr>
	</table>
</div>