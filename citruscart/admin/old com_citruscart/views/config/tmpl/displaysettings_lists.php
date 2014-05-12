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
defined('_JEXEC') or die('Restricted access'); ?>
<?php $row = $this -> row; ?>

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th class="dsc-key">
                <?php echo JText::_( 'COM_CITRUSCART_DISABLE_CHANGING_LIST_LIMIT' ); ?>
            </th>
            <td class="dsc-value">
                <?php echo CitruscartSelect::btbooleanlist( 'disable_changing_list_limit', 'class="inputbox"', $this->row->get('disable_changing_list_limit', '0') ); ?>
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <th class="dsc-key">
                <?php echo JText::_('COM_CITRUSCART_DEFAULT_LIST_LIMIT'); ?>
            </th>
            <td>
                <input type="text" name="default_list_limit" value="<?php echo $this->row->get('default_list_limit', JFactory::getApplication()->getCfg('list_limit')); ?>" class="input-small" />
            </td>
            <td>

            </td>
        </tr>
    </tbody>
</table>
