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

Citruscart::load('CitruscartHelperBase', 'helpers._base');
Citruscart::load('CitruscartSelect', 'library.select');
JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
$helper = CitruscartHelperBase::getInstance();

$url = JRoute::_( 'index.php?option=com_citruscart&view=products&task=setCurrency&return='.base64_encode( JURI::getInstance()->__toString() ) , false);
// Check the currently selected currency
$selected = CitruscartHelperBase::getSessionVariable('currency_id', Citruscart::getInstance()->get( 'default_currencyid', 1 ) );
?>

<div id="mod_citruscart_currency">
    <form action="<?php echo $url; ?>" method="post" name="currencySwitch">
        <?php // echo JText::_('COM_CITRUSCART_SELECT_CURRENCY').': '; ?>
        <?php $attribs = array( 'onChange' => 'document.currencySwitch.submit(); '); ?>
        <?php echo CitruscartSelect::currency($selected, 'currency_id', $attribs); ?>
    </form>
</div>
