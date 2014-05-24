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

$app = JFactory::getApplication();
$state = $this->state;
$rows = $this->get('List');
$form = $this->form;

$product_id = $app->input->getInt('id',0);

JHTML::_('behavior.modal');
JHTML::_('behavior.tooltip');
$model = $this->getModel();
$page = $this->get('Pagination');
?>
<form id="adminForm" action="<?php echo JRoute::_( $form['action'] .'&tmpl=component&object='.$this->object )?>" method="post" name="adminForm">
<div class="pull-left">
	<?php echo CitruscartSelect::productstates($state->filter_state, 'product_state', array('class' => 'inputbox', 'onchange' => 'this.form.submit();' ) ); ?>
</div>
<?php echo CitruscartGrid::searchform($state->filter,JText::_('COM_CITRUSCART_SEARCH'), JText::_('COM_CITRUSCART_RESET') ) ?>

<table class="dsc-clear table table-striped table-bordered">
	<thead>
		<tr>
			<th width="2%" class="title">
				<?php echo DSCGrid::sort( 'ID', 'tbl.product_id', $state->direction , $state->order ); ?>
			</th>
			<th style="width:50px;">
				<?php echo JText::_('COM_CITRUSCART_IMAGE'); ?>
			</th>
			<th class="title">
				<?php echo DSCGrid::sort( 'Name', 'tbl.product_name', $state->direction, $state->order ); ?>
			</th>
			<th class="title">
				<?php echo DSCGrid::sort( 'Description', 'tbl.product_description', $state->direction, $state->order ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="15"><?php echo $page->getListFooter(); ?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $rows ); $i < $n; $i++)
	{
		$row =$rows[$i];
		$onclick = "
					window.parent.Dsc.select{$model->getName()}(
					'{$row->product_id}', '".str_replace(array("'", "\""), array("\\'", ""), $row->product_name)."', '".$this->object."'
					);";
		?>
		<tr class="row-<?php echo $k%2;?>">


			<td style="text-align: center;"><a style="cursor: pointer;"
				onclick="<?php echo $onclick; ?>"> <?php echo $row->product_id;?> </a>
			</td>
			<td>
			<?php
				if (!empty($row->product_full_image) )
				{
					echo CitruscartHelperProduct::getImage($row->product_id, '', $row->product_name, 'thumb', false, false, array('width' => 60 ));
				}
			?>
			</td>
			<td><a style="cursor: pointer;"
				onclick="<?php echo $onclick; ?>"> <?php echo htmlspecialchars($row->product_name, ENT_QUOTES, 'UTF-8'); ?>
			</a></td>
			<td style="text-align: center;"><a style="cursor: pointer;"
				onclick="<?php echo $onclick; ?>"> <?php echo $row->product_description_short;?>
			</a></td>

		</tr>
		<?php
		$k = 1 - $k;
		}

		?>
	</tbody>
</table>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo @$state->order; ?>" />
<input type="hidden" name="filter_direction" value="<?php echo @$state->direction; ?>" />
</form>