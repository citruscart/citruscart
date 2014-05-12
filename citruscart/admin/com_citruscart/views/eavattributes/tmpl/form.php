<?php 
/*------------------------------------------------------------------------
 # com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = $this->form; ?>
<?php //$row = $this->row;
$row = JArrayHelper::fromObject($this->row);
JFilterOutput::objectHTMLSafe( $row );
?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >


			<table class="table table-striped table-bordered">
				<tr>
					<td width="100" align="right" class="key">
						<?php echo JText::_('COM_CITRUSCART_NAME'); ?>:
					</td>
					<td>
						<input type="text" name="eavattribute_label" id="eavattribute_label" size="48" maxlength="250" value="<?php echo $row['eavattribute_label']; ?>" />
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<?php echo JText::_('COM_CITRUSCART_ALIAS'); ?>:
					</td>
					<td>
						<input type="text" name="eavattribute_alias" id="eavattribute_alias" size="48" maxlength="250" value="<?php echo $row['eavattribute_alias']; ?>" />
					</td>
				</tr>
                <tr>
                    <td style="width: 100px;" class="key">
                        <label for="enabled">
                        <?php echo JText::_('COM_CITRUSCART_ENABLED'); ?>:
                        </label>
                    </td>
                    <td>
                        <?php echo CitruscartSelect::btbooleanlist( 'enabled', '', $row['enabled'] ); ?>
                    </td>
                </tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="eaventity_type">
						<?php echo JText::_('COM_CITRUSCART_ENTITY_TYPE'); ?>:
						</label>
					</td>
					<td>
						<?php echo CitruscartSelect::entitytype($row['eaventity_type'], 'eaventity_type'); ?>
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="eaventity_id">
						<?php echo JText::_('COM_CITRUSCART_ENTITY'); ?>:
						</label>
					</td>
					<td>
						<?php 
							if($row['eaventity_type'])
							{
								$allowed_types = array('products');
								if(in_array($row['eaventity_type'], $allowed_types))
								{
									$url = JRoute::_("index.php?option=com_citruscart&controller=eavattributes&task=selectentities&tmpl=component&eaventity_type=".$row['eaventity_type']."&id=".$row['eavattribute_id']);
									echo CitruscartUrl::popup($url, JText::_('COM_CITRUSCART_SELECT_ENTITIES')); 
								}
							}
							else
							{
						?>
							<div class="note well">
								<?php echo JText::_('COM_CITRUSCART_CLICK_APPLY_TO_ADD_A_LINK_TO_AN_ENTITY_FOR_THIS_PRODUCT'); ?>
							</div>
						<?php 
							}
						?>
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="eavattribute_type">
						<?php echo JText::_('COM_CITRUSCART_DATA_TYPE'); ?>:
						</label>
					</td>
					<td>
						<?php echo CitruscartSelect::attributetype($row['eavattribute_type'], 'eavattribute_type'); ?>
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="editable_by">
						<?php echo JText::_('COM_CITRUSCART_EDITABLE_BY'); ?>:
						</label>
					</td>
					<td>
						<?php echo CitruscartSelect::editableby($row['editable_by'], 'editable_by'); ?>
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="eavattribute_required">
						<?php echo JText::_('COM_CITRUSCART_REQUIRED'); ?>:
						</label>
					</td>
					<td>
						<?php echo CitruscartSelect::btbooleanlist( 'eavattribute_required', '', $row['eavattribute_required'] ); ?>
					</td>
				</tr>
                <tr>
                    <td class="dsc-key">
                        <?php echo JText::_('COM_CITRUSCART_FORMAT_STRFTIME'); ?>:
                    </td>
                    <td class="dsc-value">
                        <input type="text" name="eavattribute_format_strftime" id="eavattribute_format_strftime" value="<?php echo $row['eavattribute_format_strftime']; ?>" size="30" maxlength="250" />
                        <p class="dsc-tip">
                        <?php echo JText::_('COM_CITRUSCART_FORMAT_STRFTIME_TIP'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="dsc-key">
                        <?php echo JText::_('COM_CITRUSCART_FORMAT_DATE'); ?>:
                    </td>
                    <td class="dsc-value">
                        <input type="text" name="eavattribute_format_date" id="eavattribute_format_date" value="<?php echo $row['eavattribute_format_date']; ?>" size="30" maxlength="250" />
                        <p class="dsc-tip">
                        <?php echo JText::_('COM_CITRUSCART_FORMAT_DATE_TIP'); ?>
                        </p>
                    </td>
                </tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $row['eavattribute_id']; ?>" />
			<input type="hidden" name="task" value="" />
	</fieldset>
</form>