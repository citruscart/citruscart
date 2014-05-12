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
defined('_JEXEC') or die('Restricted access'); ?>


            <h2><?php echo JText::_('COM_CITRUSCART_SEARCH_FOR_PRODUCT'); ?></h2>
            <table class="table table-striped table-bordered">
            	<tr><td> <input type="text" name="filter" value="<?php echo $this->state->filter; ?>" size="240" /></td> <td> <input type="submit" class="btn btn-primary pull-left" name="submit_search" value="<?php echo JText::_('COM_CITRUSCART_CONTINUE'); ?>" /></td></tr>
            </table>

