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
<?php JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>
<?php JHTML::_('stylesheet', 'Citruscart.css', 'media/citruscart/css/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>

<form action="<?php echo JRoute::_( 'index.php?option=com_citruscart&view=pos&tmpl=component' ); ?>" method="post" class="adminForm" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset>
		<div class="header icon-48-Citruscart" style="float: left;">
			<?php echo JText::_('Manage Addresses');?>
		</div>
		<div class="toolbar" id="toolbar" style="float: right;">
			<table class="toolbar">
				<tr>
					<td align="center">
					<a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('Please make a selection from the list to set as Billing default')?>');}else{  submitbutton('flag_billing')}" href="#" >
					<span class="icon-32-default" title="<?php echo JText::_('Default', true);?>"></span><?php echo JText::_('BILLING DEFAULT');?>
					</a>
					</td>
					<td align="center">
					<a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('Please make a selection from the list to set as Shipping default')?>');}else{  submitbutton('flag_shipping')}" href="#" >
					<span class="icon-32-default" title="<?php echo JText::_('Default', true);?>"></span><?php echo JText::_('SHIPPING DEFAULT');?>
					</a>
					</td>
					<td class="divider"> </td>
					<td align="center">
					<a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('Please make a selection from the list to edit')?>');}else{  submitbutton('address')}" href="#" >
					<span class="icon-32-edit" title="<?php echo JText::_('Edit', true);?>"></span><?php echo JText::_('Edit');?>
					</a>
					</td>
					<td align="center">
					<a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('Please make a selection from the list to delete');?>');}else{if(confirm('<?php echo JText::_('Are you sure you want to delete the selected Items')?>?')){submitbutton('flag_deleted');}}" href="#" >
					<span class="icon-32-delete" title="<?php echo JText::_('Delete', true);?>"></span><?php echo JText::_('Delete');?>
					</a>
					</td>
					<td align="center">
					<a href="<?php echo JRoute::_("index.php?option=com_citruscart&view=pos&task=address&tmpl=component"); ?>" >
					<span class="icon-32-new" title="<?php echo JText::_('New', true);?>"></span><?php echo JText::_('New');?>
					</a>
					</td>
					<td class="divider"> </td>
					<td align="center">
					<a onclick="window.parent.document.getElementById( 'sbox-window' ).close();" href="#" >
					<span class="icon-32-cancel" title="<?php echo JText::_('Close', true);?>"></span><?php echo JText::_('Close');?>
					</a>
					</td>
				</tr>
			</table>
		</div>
	</fieldset>	     
    <table class="adminlist" style="clear: both;">
        <thead>
            <tr>
                <th style="width: 20px;">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th style="text-align: center;">
                    <?php echo CitruscartGrid::sort( 'Name', "tbl.address_name", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo CitruscartGrid::sort( 'Address', "tbl.address_1", $state->direction, $state->order ); ?>
                </th>
                <th>
                </th>
            </tr>
        </thead>
        <tbody>     
        <?php if (!count($items)) : ?>
            <tr>
                <td colspan="10" align="center">
                    <?php echo JText::_('NO ITEMS FOUND'); ?>
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
                    <?php if (!empty($item->phone_1)) { echo "&nbsp;&bull;&nbsp;<b>".JText::_( "PHONE" )."</b>: ".$item->phone_1; ?><br/><?php } ?>
                    <?php if (!empty($item->phone_2)) { echo "&nbsp;&bull;&nbsp;<b>".JText::_( "Alt Phone" )."</b>: ".$item->phone_2; ?><br/><?php } ?>
                    <?php if (!empty($item->fax)) { echo "&nbsp;&bull;&nbsp;<b>".JText::_( "FAX" )."</b>: ".$item->fax; ?><br/><?php } ?>
                </td>
                <td style="text-align: center;">
                    <?php if ($item->is_default_shipping && $item->is_default_billing)
                    {
                        echo JText::_( "DEFAULT BILLING AND SHIPPING ADDRESS" );
                    }
                    elseif ($item->is_default_shipping) 
                    {
                    	echo JText::_( "DEFAULT SHIPPING ADDRESS" );
                    }
                    elseif ($item->is_default_billing) 
                    {
                    	echo JText::_( "DEFAULT BILLING ADDRESS" );
                    }
                    ?>
                </td>
            </tr>
            <?php $i=$i1; $k = (1 - $k); ?>
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