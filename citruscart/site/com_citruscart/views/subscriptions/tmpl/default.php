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
defined('_JEXEC') or die('Restricted access');
	 $doc = JFactory::getDocument();
    $doc->addStyleSheet(JUri::root().'media/citruscart/css/menu.css');
	$state = $this->state;
	$form = $this->form;
	$items = $this->items;
	$menu = CitruscartMenu::getInstance();
?>

<div class='componentheading'>
	<span><?php echo JText::_('COM_CITRUSCART_SUBSCRIPTIONS'); ?></span>
</div>
  <div class="naviagtion header">
	<?php
		require_once(JPATH_SITE.'/administrator/components/com_citruscart/helpers/toolbar.php');
	 	$toolbar = new CitruscartToolBar();
	 	$toolbar->renderLinkbar();

	?>
</div>
	<?php // if( $menu ) { $menu->display(); } ?>

<form action="<?php echo JRoute::_( $form['action']."&limitstart=".$state->limitstart )?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

    <table class="table table-striped table-bordered">
        <tr>
            <td align="left" width="100%">
                <?php echo JText::_('COM_CITRUSCART_SEARCH_BY_APPLYING_FILTERS'); ?>
            </td>
            <td nowrap="nowrap" style="text-align: right;">
                <input name="filter" value="<?php echo $state->filter; ?>" />
                <button onclick="this.form.submit();"><?php echo JText::_('COM_CITRUSCART_SEARCH'); ?></button>
                <button onclick="citruscartFormReset(this.form);"><?php echo JText::_('COM_CITRUSCART_RESET'); ?></button>
            </td>

        </tr>
    </table>

    <table class="adminlist table table-striped table-bordered" style="clear: both;" >
        <thead>
            <tr class="filterline">
                <th>
                    <?php $attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
                </th>
                <th>
                </th>
                <th>
                </th>
                <th>
                </th>
                <th>
                     <?php echo CitruscartSelect::booleans( $state->filter_enabled, 'filter_enabled', $attribs, 'enabled', true, 'COM_CITRUSCART_ENABLED_STATE' ); ?>
                </th>
            </tr>
            <tr>
                <th style="width: 50px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ID', "tbl.subscription_id", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 200px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_CREATED', "tbl.created_datetime", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                    <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_TYPE', "p.product_name", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                     <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_EXPIRES', "tbl.expires_datetime", $state->direction, $state->order ); ?>
                </th>
                <th style="width: 100px;">
                     <?php echo CitruscartGrid::sort( 'COM_CITRUSCART_ENABLED', "tbl.subscription_enabled", $state->direction, $state->order ); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="20">
                    <div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php $i=0; $k=0; ?>
        <?php foreach ($items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
                <td style="text-align: center;">
                    <a href="<?php echo JRoute::_( $item->link_view ); ?>">
                        <?php echo $item->order_id; ?>
                    </a>
                </td>
                <td style="text-align: center;">
                    <a href="<?php echo JRoute::_( $item->link_view ); ?>">
                        <?php echo JHTML::_('date', $item->created_date, Citruscart::getInstance()->get('date_format')); ?>
                    </a>
                </td>
                <td style="text-align: center;">
                    <a href="<?php echo JRoute::_( $item->link_view ); ?>">
                       <?php echo $item->product_name; ?>
                    </a>
                </td>
                <td style="text-align: center;">
                     <a href="<?php echo $item->link_view; ?>">
                        <?php echo JHTML::_('date', $item->expires_datetime, Citruscart::getInstance()->get('date_format')); ?>
                    </a>
                </td>
                <td style="text-align: center;">
                       <?php echo CitruscartGrid::boolean( $item->subscription_enabled ); ?>
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