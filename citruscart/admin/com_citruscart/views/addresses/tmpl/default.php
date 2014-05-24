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

defined('_JEXEC') or die('Restricted access'); ?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $app = JFactory::getApplication(); ?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data">

    <?php
    echo CitruscartGrid::pagetooltip( $app->input->getString('view') );
    ?>

    <?php echo CitruscartGrid::searchform($state->filter,JText::_('COM_CITRUSCART_SEARCH'), JText::_('COM_CITRUSCART_RESET') ) ?>

	<table class="table table-striped table-bordered" style="clear: both;">
		<thead>
            <tr>
                <th style="width: 5px;">
                	<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
                </th>
                <th style="width: 20px;">
                  	<?php echo JHtmlGrid::checkall($name = 'cid', $tip = 'JGLOBAL_CHECK_ALL', $action = 'Joomla.checkAll(this)')?>
                </th>
                <th style="width: 50px;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.address_id", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 50px;">
                	<?php echo CitruscartGrid::sort( 'COM_CITRUSCART_USER_ID', "tbl.user_id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                	<?php echo JText::_('COM_CITRUSCART_USER'); ?>
                </th>
                <th style="text-align: left;">
    	            <?php echo JText::_('COM_CITRUSCART_ADDRESS'); ?>
                </th>
            </tr>
            <tr class="filterline">
                <th colspan="3">
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
                <th>
                    <input type="text" id="filter_userid" name="filter_userid" value="<?php echo $state->filter_userid; ?>" class="input-mini" />
                </th>
                <th style="text-align: left;">
                    <input type="text" id="filter_user" name="filter_user" value="<?php echo $state->filter_user; ?>" class="input-medium" />
                </th>
                <th style="text-align: left;">
                    <input type="text" id="filter_address" name="filter_address" value="<?php echo $state->filter_address; ?>"  class="input-medium" />
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
                    <div class="pagination pagination-toolbar">
                    	<?php echo $this->pagination->getPagesLinks(); ?>
                    </div>
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
					<?php echo CitruscartGrid::checkedout( $item, $i, 'address_id' ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>" class="badge badge-warning">
						<?php echo $item->address_id; ?>
					</a>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>" class="badge badge-info">
						<?php echo $item->user_id; ?>
					</a>
				</td>
				<td style="text-align: left;">
					<?php
					if ($item->user_id > 0) {
					    $user = JFactory::getUser( $item->user_id );
					    echo $user->name . " [ " . $user->email . " ] ";
					} else {
					    echo JText::_( "COM_CITRUSCART_GUEST" );
					}
					?>
				</td>
				<td style="text-align: left;">
				    <a href="<?php echo $item->link; ?>">
                        <?php echo $item->getSummary(); ?>
                    </a>
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />

	<?php echo $this->form['validate']; ?>
</form>