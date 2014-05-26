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

defined('_JEXEC') or die('Restricted access');
	JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
	$state = $this->state;
	$form = $this->form;
	$items = $this->items;

	Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' );
	$display_subnum = Citruscart::getInstance()->get( 'display_subnum', 0 );
	$create_user_link = "index.php?option=com_users&task=add";
	/* Get the applicaiton */
	$app= JFactory::getApplication();
	if (version_compare(JVERSION, '1.6.0', 'ge'))
		$create_user_link = "index.php?option=com_users&task=user.add";
?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<?php	echo CitruscartGrid::pagetooltip( $app->input->get('view') );	?>

    <?php $button = "<input type='button' class='btn btn-success pull-left' value='".JText::_('COM_CITRUSCART_CREATE_NEW_USER')."' />";
                	echo CitruscartUrl::popup( $create_user_link, $button, array('update' => true) );
                ?>

	<?php echo CitruscartGrid::searchform($state->filter,JText::_('COM_CITRUSCART_SEARCH'), JText::_('COM_CITRUSCART_RESET') ) ?>

	<table class="table table-bordered">
		<thead>
            <tr>
                <th style="width: 5px;">
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="width: 50px;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.id", $state->direction, $state->order ); ?>
                </th>
                <?php if( $display_subnum ): ?>
                <th style="width: 70px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_SUB_NUM', "ui.sub_number", $state->direction, $state->order ); ?>
                </th>
                <?php endif; ?>
                <th style="text-align: left;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.name", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: center;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_USERNAME', "tbl.username", $state->direction, $state->order ); ?>
                </th>
				<th style="text-align: center;">
					<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_EMAIL', 'tbl.email', $state->direction, $state->order); ?>
				</th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_GROUP', 'group.group_name', $state->direction, $state->order); ?>
                </th>
				<th>
				</th>
				<th>
				</th>
            </tr>
            <tr class="filterline">
                <th colspan="2">
                    <?php $attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
                   <div class="range">
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_FROM'); ?>" id="filter_id_from" name="filter_id_from" value="<?php echo $state->filter_id_from; ?>" size="5" class="input input-tiny" />
                        </div>
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_('COM_CITRUSCART_TO'); ?>" id="filter_id_to" name="filter_id_to" value="<?php echo $state->filter_id_to; ?>" size="5" class="input input-tiny" />
                        </div>
                    </div>
                </th>
                
                <?php if( $display_subnum ): ?>
                <th>
                    <input id="filter_subnum" type="text" name="filter_subnum" value="<?php echo $state->filter_subnum; ?>" size="10"/>
                </th>
                
                <?php endif; ?>
                <th style="text-align: left;">
                	<input id="filter_name" type="text" name="filter_name" value="<?php echo $state->filter_name; ?>" size="25" placeholder="<?php echo JText::_("COM_CITRUSCART_NAME");?>" />
                </th>
                <th>
                	<input id="filter_username" type="text" name="filter_username" value="<?php echo $state->filter_username; ?>" size="25" placeholder="<?php echo JText::_("COM_CITRUSCART_USERNAME");?>"/>
                </th>
                <th>
                    <input id="filter_email" type="text" name="filter_email" value="<?php echo $state->filter_email; ?>" size="25" placeholder="<?php echo JText::_("COM_CITRUSCART_EMAIL");?>"/>
                </th>
                <th>
                    <?php echo CitruscartSelect::groups($state->filter_group, 'filter_group', $attribs, 'filter_group', true ); ?>
                </th>
                <th>
                </th>
                <th>
                </th>
            </tr>
            <tr>
                <th colspan="20" style="font-weight: normal;">
                    <div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
                    <div style="float: left;"><?php echo $this->pagination->getListFooter(); ?></div>
                </th>
            </tr>
        </thead>
       </table>
       <table class="table table-striped table-bordered">
        <tfoot>
            <tr>
                <td colspan="20">
                    <div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
                    <?php echo $this->pagination->getPagesLinks(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td align="center">
					<?php echo $i + 1; ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>" class="badge badge-warning">
						<?php echo $item->id; ?>
					</a>
				</td>
        <?php if( $display_subnum ): ?>
        <td style="text-align: center;">
        	<?php echo CitruscartHelperSubscription::displaySubNum( $item->sub_number ); ?>
        </td>
        <?php endif; ?>
				<td style="text-align: left;">
					<a href="<?php echo $item->link; ?>">
						<label class="label label-success"><?php echo $item->name; ?></label>
					</a>
				</td>
				<td style="text-align: center;">
					<?php echo $item->username; ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->email; ?>
				</td>
                <td style="text-align: center;">
                    <?php echo $item->group_name; ?>
                </td>
				<td style="text-align: center;">
					[
					<a href="<?php echo $item->link; ?>" class="badge badge-info">
						<?php echo JText::_('COM_CITRUSCART_VIEW_DASHBOARD'); ?>
					</a>
					]
				</td>
                <td style="text-align: center;">
                </td>
			</tr>
			<?php $i=$i+1; $k = (1 - $k); ?>
			<?php endforeach; ?>

			<?php if (!count($items)) : ?>
			<tr>
				<td colspan="10" align="center">
					<?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</form>