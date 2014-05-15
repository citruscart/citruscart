<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');?>
<?php JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false); ?>
<?php JHTML::_('stylesheet', 'Citruscart.css', 'media/citruscart/css/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>

<form action="<?php echo JRoute::_( 'index.php?option=com_citruscart&view=pos&tmpl=component' ); ?>" method="post" class="adminForm" name="adminForm" >

		<div class="header " style="float: left;">
			<h2>
			<?php echo JText::_('COM_CITRUSCART_MANAGE_ADDRESSES');?>
			</h2>
		</div>
		<div class="toolbar pull-right" id="toolbar" >
			<table class="table table-striped table-bordered" width="500px">
				<tr>
					<td align="center">
						<div class="btn-group">
					<a class="btn" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('COM_CITRUSCART_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO_SET_AS_BILLING_DEFAULT')?>');}else{  submitbutton('flag_billing')}" href="#" >
					<?php echo JText::_('COM_CITRUSCART_BILLING_DEFAULT');?>
					</a>

					<a class="btn" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('COM_CITRUSCART_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO_SET_AS_SHIPPING_DEFAULT')?>');}else{  submitbutton('flag_shipping')}" href="#" >
					<?php echo JText::_('COM_CITRUSCART_SHIPPING_DEFAULT');?>
					</a>


					<a class="btn" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('COM_CITRUSCART_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO_EDIT')?>');}else{  submitbutton('address')}" href="#" >
					<?php echo JText::_('COM_CITRUSCART_EDIT');?>
					</a>


					<a class="btn" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('COM_CITRUSCART_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO_DELETE');?>');}else{if(confirm('<?php echo JText::_('COM_CITRUSCART_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_SELECTED_ITEMS')?>?')){submitbutton('flag_deleted');}}" href="#" >
					<?php echo JText::_('COM_CITRUSCART_DELETE');?>
					</a>

					<a class="btn" href="<?php echo JRoute::_("index.php?option=com_citruscart&view=pos&task=address&tmpl=component"); ?>" >
					<?php echo JText::_('COM_CITRUSCART_NEW');?>
					</a>



					</div>
					</td>

				</tr>
			</table>
		</div>

    <table class="table table-striped table-bordered" style="clear: both;">
        <thead>
            <tr>
                <th style="width: 20px;">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th style="text-align: center;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_NAME', "tbl.address_name", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ADDRESS', "tbl.address_1", $state->direction, $state->order ); ?>
                </th>
                <th>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php if (!count($items)) : ?>
            <tr>
                <td colspan="10" align="center">
                    <?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
                </td>
            </tr>
        <?php else: ?>
        	<?php $i=0; $k=0; ?>
        	<?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
                <td style="text-align: center;">
                    <?php echo CitruscartGrid::checkedout( $item, $i, 'address_id' ); ?>
                </td>
                <td style="text-align: center;">
                    <a href="<?php echo JRoute::_( 'index.php?option=com_citruscart&view=pos&task=address&tmpl=component&id='.$item->address_id ); ?>">
                        <?php echo $item->address_name; ?>
                    </a>
                </td>
                <td style="text-align: left;">
                    <?php // TODO Use sprintf to enable formatting?  How best to display addresses? ?>
                    <!-- ADDRESS -->
                    <b><?php echo $item->first_name; ?> <?php echo $item->middle_name; ?> <?php echo $item->last_name; ?></b><br/>
                    <?php if (!empty($item->company)) { echo $item->company; ?><br/><?php } ?>
                    <?php echo $item->address_1; ?><br/>
                    <?php if (!empty($item->address_2)) { echo $item->address_2; ?><br/><?php } ?>
                    <?php echo $item->city; ?>, <?php echo $item->zone_name; ?> <?php echo $item->postal_code; ?><br/>
                    <?php echo $item->country_name; ?><br/>
                    <!-- PHONE NUMBERS -->
                    <?php // if ($item->phone_1 || $item->phone_2 || $item->fax) { echo "<hr/>"; } ?>
                    <?php if (!empty($item->phone_1)) { echo "&nbsp;&bull;&nbsp;<b>".JText::_('COM_CITRUSCART_PHONE')."</b>: ".$item->phone_1; ?><br/><?php } ?>
                    <?php if (!empty($item->phone_2)) { echo "&nbsp;&bull;&nbsp;<b>".JText::_('COM_CITRUSCART_ALT_PHONE')."</b>: ".$item->phone_2; ?><br/><?php } ?>
                    <?php if (!empty($item->fax)) { echo "&nbsp;&bull;&nbsp;<b>".JText::_('COM_CITRUSCART_FAX')."</b>: ".$item->fax; ?><br/><?php } ?>
                </td>
                <td style="text-align: center;">
                    <?php if ($item->is_default_shipping && $item->is_default_billing)
                    {
                        echo JText::_('COM_CITRUSCART_DEFAULT_BILLING_AND_SHIPPING_ADDRESS');
                    }
                    elseif ($item->is_default_shipping)
                    {
                    	echo JText::_('COM_CITRUSCART_DEFAULT_SHIPPING_ADDRESS');
                    }
                    elseif ($item->is_default_billing)
                    {
                    	echo JText::_('COM_CITRUSCART_DEFAULT_BILLING_ADDRESS');
                    }
                    ?>
                </td>
            </tr>
            <?php $i=$i+1; $k = (1 - $k); ?>
            <?php endforeach; ?>

       	<?php endif;?>
        </tbody>
	</table>


    <input type="hidden" name="order_change" value="0" />
    <input type="hidden" name="id" value="" />
    <input type="hidden" name="task" id="task" value="" />
    <input type="hidden" name="boxchecked" value="" />
    <input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
    <input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />
</form>