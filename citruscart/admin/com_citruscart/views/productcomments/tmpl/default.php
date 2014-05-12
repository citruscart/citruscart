<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/'); ?>
<?php $state = $this->state; ?>
<?php  $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' ); ?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<table>
        <tr>
            <td align="left" width="100%">
            </td>
            <td nowrap="nowrap">
                <input type="text" name="filter" value="<?php echo $state->filter; ?>" />
                <button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_('COM_CITRUSCART_SEARCH'); ?></button>
                <button class="btn btn-danger"onclick="CitruscartFormReset(this.form);"><?php echo JText::_('COM_CITRUSCART_RESET'); ?></button>
            </td>
        </tr>
    </table>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th style="width: 5px;"><?php echo JText::_('COM_CITRUSCART_NUM'); ?></th>
			<th style="width: 20px;">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
			</th>
			<th style="width: 50px;">
                <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.productcomment_id", $state->direction, $state->order ); ?>
			</th>
            <th style="width: 100px;">
                <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_DATE', "tbl.created_date", $state->direction, $state->order ); ?>
            </th>
			<th style="text-align: left;">
                <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PRODUCT_NAME', "p.product_name", $state->direction, $state->order ); ?>
                +
                <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_COMMENT', "tbl.productcomment_text", $state->direction, $state->order ); ?>
			</th>
			<th style="text-align: left; width: 100px;">
                <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_USER', "m.name", $state->direction, $state->order ); ?>
			</th>
			<th style="width: 150px;">
                <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_USER_RATING', "tbl.productcomment_rating", $state->direction, $state->order ); ?>
			</th>
            <th style="text-align: center; width: 100px;">
                <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_HELPFUL_VOTES', "tbl.helpful_votes", $state->direction, $state->order ); ?>
                <br/>
                (
                <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_TOTAL', "tbl.helpful_votes_total", $state->direction, $state->order ); ?>
                )
            </th>
			<th style="width: 100px;">
                <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_REPORTED', "tbl.reported_count", $state->direction, $state->order ); ?>
			</th>
			<th style="width: 100px;">
                <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_PUBLISHED', "tbl.productcomment_enabled", $state->direction, $state->order ); ?>
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

            </th>
			<th style="text-align: left;">
                <input id="filter_name" name="filter_name" value="<?php echo $state->filter_name; ?>" size="25" />
            </th>
			<th>

			</th>
			<th>

			</th>
			<th>

			</th>
            <th>
                <?php echo CitruscartSelect::booleans( $state->filter_reported, 'filter_reported', $attribs, 'filter_reported', true, 'Reported', 'Yes', 'No' ); ?>
            </th>
			<th>
                <?php echo CitruscartSelect::booleans( $state->filter_enabled, 'filter_enabled', $attribs, 'enabled', true, 'COM_CITRUSCART_ENABLED_STATE' ); ?>
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
	<tbody>
	<?php $i=0; $k=0; ?>
	<?php foreach ($items as $item) : ?>
		<tr class='row<?php echo $k; ?>'>
			<td align="center"><?php echo $i + 1; ?></td>
			<td style="text-align: center;"><?php echo CitruscartGrid::checkedout( $item, $i, 'productcomment_id' ); ?>
			</td>
			<td style="text-align: center;">
    			<a href="<?php echo $item->link; ?>">
        			<?php echo $item->productcomment_id ; ?>
    			</a>
			</td>
            <td style="text-align: center;">
                <a href="<?php echo $item->link; ?>">
                    <?php echo $item->created_date; ?>
                </a>
            </td>
			<td style="text-align: left;">
    			<a href="<?php echo $item->link; ?>">
    			<?php echo JText::_($item->product_name); ?>
    			</a>

    			<div>
                    <?php echo substr( $item->productcomment_text, 0, 250 ); ?>
                    <?php if (strlen($item->productcomment_text) >= '250' ) { echo "..."; } ?>
    			</div>
			</td>
			<td style="text-align: left;">
    			<a href="<?php echo $item->link; ?>">
    			<?php echo JText::_($item->user_name); ?>
    			</a>
			</td>
			<td style="text-align: center;">
                <?php echo CitruscartHelperProduct::getRatingImage( $item->productcomment_rating, $this ); ?>
			</td>
            <td style="text-align: center;">
                <a href="<?php echo $item->link; ?>">
                    <?php echo JText::_($item->helpful_votes); ?>
                </a>
                (
                <a href="<?php echo $item->link; ?>">
                <?php echo JText::_($item->helpful_votes_total); ?>
                </a>
                )
            </td>
			<td style="text-align: center;">
    			<?php
    			if ( ($item->reported_count) > 0)
    			{
        			?>
        			<a href="<?php echo $item->link; ?>">
        			<img src="../media/citruscart/images/required_16.png">
        			</a>
                    <?php
    			}
    			?>
			</td>

			<td style="text-align: center;">
    			<?php echo CitruscartGrid::enable($item->productcomment_enabled, $i, 'productcomment_enabled.' ); ?>
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
	<tfoot>
		<tr>
			<td colspan="20"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>
</table>

<input type="hidden" name="order_change" value="0" /> <input
	type="hidden" name="id" value="" /> <input type="hidden" name="task"
	value="" /> <input type="hidden" name="boxchecked" value="" /> <input
	type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
<input type="hidden" name="filter_direction"
	value="<?php echo $state->direction; ?>" /> <?php echo $this->form['validate']; ?>
</form>
