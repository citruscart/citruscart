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
?>
<?php $form = $this->form; ?>
<?php $row = $this->row; ?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" enctype="multipart/form-data" >

	<?php echo CitruscartGrid::pagetooltip( 'products_view' ); ?>

    <table style="width: 100%;">
    <tr>
        <td style="width: 70%; max-width: 70%; vertical-align: top; padding: 0px 5px 0px 0px;">


		<fieldset>
			<legend><?php echo JText::_('COM_CITRUSCART_PRODUCT'); ?></legend>
				<table class="admintable">
					<tr>
						<td width="100" align="right" class="key">
							<?php echo JText::_('COM_CITRUSCART_NAME'); ?>:
						</td>
						<td>
							<?php echo $row->product_name; ?>
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<?php echo JText::_('COM_CITRUSCART_MODEL'); ?>:
						</td>
						<td>
							<?php echo $row->product_model; ?>
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<?php echo JText::_('COM_CITRUSCART_SKU'); ?>:
						</td>
						<td>
							<?php echo $row->product_sku; ?>
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<?php echo JText::_('COM_CITRUSCART_ENABLED'); ?>:
						</td>
						<td>
							<?php echo CitruscartGrid::boolean( $row->product_enabled ); ?>
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<?php echo JText::_('COM_CITRUSCART_CURRENT_IMAGE'); ?>:
						</td>
						<td>
							<?php
							jimport('joomla.filesystem.file');
							if (!empty($row->product_full_image) && JFile::exists( Citruscart::getPath( 'products_images').'/'.$row->product_full_image ))
							{
								?>
								<img src="<?php echo Citruscart::getURL( 'products_images').$row->product_full_image; ?>" style="display: block;" />
								<?php
							}
							?>
						</td>
					</tr>
				</table>
		</fieldset>

            <?php
            $modules = JModuleHelper::getModules("citruscart_product_dashboard_main");
            $document   = JFactory::getDocument();
            $renderer   = $document->loadRenderer('module');
            $attribs    = array();
            $attribs['style'] = 'xhtml';
            foreach ( $modules as $mod )
            {
                echo $renderer->render($mod, $attribs);
            }
            ?>
        </td>
        <td style="vertical-align: top; width: 30%; min-width: 30%; padding: 0px 0px 0px 5px;">

            <?php
            $modules = JModuleHelper::getModules("citruscart_product_dashboard_right");
            $document   = JFactory::getDocument();
            $renderer   = $document->loadRenderer('module');
            $attribs    = array();
            $attribs['style'] = 'xhtml';
            foreach ( $modules as $mod )
            {
                $mod_params = new DSCParameter( $mod->params );
                if ($mod_params->get('hide_title', '1')) { $mod->showtitle = '0'; }
                echo $renderer->render($mod, $attribs);
            }
            ?>
        </td>
    </tr>
    </table>

<input type="hidden" name="id" value="<?php echo $row->product_id; ?>" />
<input type="hidden" name="task" id="task" value="" />
</form>