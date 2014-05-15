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
<?php JHTML::_('stylesheet', 'component.css', 'media/citruscart/css/'); ?>
<?php $state = $this->state; ?>
<?php $form = $this->form; ?>
<?php $items = $this->items; ?>
<?php $row = $this->row; ?>
<?php $app = JFactory::getApplication();?>

<h1 style="margin-left: 2%; margin-top: 2%;"><?php echo JText::_('COM_CITRUSCART_SET_RATES_FOR'); ?>: <?php echo $row->tax_class_name; ?></h1>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data">

    <?php echo CitruscartGrid::pagetooltip( $app->input->getString('view') ); ?>

<div class="note" style="width: 96%; margin-left: auto; margin-right: auto;">
    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('COM_CITRUSCART_ADD_A_NEW_TAX_RATE'); ?></div>
    <div style="float: right;">
        <input type="hidden" name="tax_class_id" value="<?php echo $row->tax_class_id; ?>" />
        <button class="btn btn-primary" onclick="document.getElementById('task').value='createrate'; document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_CREATE_RATE'); ?></button>
    </div>
    <div class="reset"></div>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th><?php echo JText::_('COM_CITRUSCART_GEOZONE'); ?></th>
                <th><?php echo JText::_('COM_CITRUSCART_PREDECESSOR'); ?></th>
                <th><?php echo JText::_('COM_CITRUSCART_DESCRIPTION'); ?></th>
                <th><?php echo JText::_('COM_CITRUSCART_RATE'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">
                    <?php echo CitruscartSelect::geozone( '', 'geozone_id', 1 ); ?>
                </td>
                <td style="text-align: center;">
                    <?php echo CitruscartSelect::taxratespredecessors( 0, 'level', 1 ); ?>
                </td>
                <td style="text-align: center;">
                    <input id="tax_rate_description" name="tax_rate_description" value="" />
                </td>
                <td style="text-align: center;">
                    <input id="tax_rate" name="tax_rate" value="" />
                </td>
            </tr>
        </tbody>
    </table>

 </div>    
 
 <div class="note_green" style="width: 96%; margin-left: auto; margin-right: auto;">
    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('COM_CITRUSCART_CURRENT_TAX_RATES'); ?></div>
    <div style="float: right;">
        <input type="hidden" name="tax_class_id" value="<?php echo $row->tax_class_id; ?>" />
        <button class="btn btn-success" onclick="document.adminForm.toggle.checked=true; checkAll(<?php echo count( $items ); ?>); document.getElementById('task').value='saverates'; document.adminForm.submit();"><?php echo JText::_('COM_CITRUSCART_SAVE_ALL_CHANGES'); ?></button>
    </div>
    <table class="table table-striped table-bordered" style="clear: both;">
        <thead>
            <tr>
                <th style="width: 20px;">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $items ); ?>);" />
                </th>
                <th style="width: 50px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.tax_rate_id", $state->direction, $state->order ); ?>
                </th>                
                <th style="text-align: left;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_GEO_ZONE', "tbl.geozone_id", $state->direction, $state->order ); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_LEVEL', "tbl.level", $state->direction, $state->order ); ?>
                </th>
                <th>
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_TAX_RATE_DESCRIPTION', "tbl.tax_rate_description", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_TAX_RATE', "tbl.tax_rate", $state->direction, $state->order ); ?>
                </th>
                <th>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
                <td style="text-align: center;">
                    <?php echo CitruscartGrid::checkedout( $item, $i, 'tax_rate_id' ); ?>
                </td>
                <td style="text-align: center;">
                    <?php echo $item->tax_rate_id; ?>
                </td>   
                <td style="text-align: left;">
                    <?php echo JText::_( $item->geozone_name ); ?>
                </td>
                <td style="text-align: left;">
                    <?php echo $this->listRateLevels( $item->level, $item->tax_rate_id, $item->tax_class_id ); ?><br />
                    (<?php 
                    		echo implode( ' | ', $this->getAssociatedTaxRates( $item->level, $item->geozone_id, $item->tax_class_id ) ); 
                    ?>)
                </td>
                <td style="text-align: center;">
                    <input type="text" name="description[<?php echo $item->tax_rate_id; ?>]" value="<?php echo $item->tax_rate_description; ?>" />
                </td>
                <td style="text-align: center;">
                    <input type="text" name="rate[<?php echo $item->tax_rate_id; ?>]" value="<?php echo $item->tax_rate; ?>" />
                </td>
                <td style="text-align: center;">
                    [<a href="index.php?option=com_citruscart&controller=taxrates&task=delete&cid[]=<?php echo $item->tax_rate_id; ?>&return=<?php echo base64_encode("index.php?option=com_citruscart&controller=taxclasses&task=setrates&id={$row->tax_class_id}&tmpl=component"); ?>">
                        <?php echo JText::_('COM_CITRUSCART_DELETE_RATE'); ?>   
                    </a>
                    ]
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
                <td colspan="20">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <input type="hidden" name="order_change" value="0" />
    <input type="hidden" name="id" value="<?php echo $row->tax_class_id; ?>" />
    <input type="hidden" name="task" id="task" value="setrates" />
    <input type="hidden" name="boxchecked" value="" />
    <input type="hidden" name="filter_order" value="<?php echo $state->order; ?>" />
    <input type="hidden" name="filter_direction" value="<?php echo $state->direction; ?>" />
    
    <?php echo $this->form['validate']; ?>
</div>
</form>