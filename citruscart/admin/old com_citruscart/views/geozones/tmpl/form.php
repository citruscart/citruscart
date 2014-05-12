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

$form = $this->form;
$row = $this->row;
JFilterOutput::objectHTMLSafe( $row );
?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data">


            <table class="table table-striped table-bordered">
                <tr>
                    <td width="100" align="right" class="key">
                        <label for="geozone_name">
                        <?php echo JText::_('COM_CITRUSCART_NAME'); ?>:
                        </label>
                    </td>
                    <td>
                        <input type="text" name="geozone_name" id="geozone_name" size="48" maxlength="250" value="<?php echo $row->geozone_name; ?>" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <label for="geozone_description">
                        <?php echo JText::_('COM_CITRUSCART_DESCRIPTION'); ?>:
                        </label>
                    </td>
                    <td>
                        <textarea name="geozone_description" id="geozone_description" rows="5" cols="25"><?php echo $row->geozone_description; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <label for="geozone_type">
                        <?php echo JText::_('COM_CITRUSCART_TYPE'); ?>:
                        </label>
                    </td>
                    <td>
                        <?php echo CitruscartSelect::geozonetypes( $row->geozonetype_id, 'geozonetype_id', '', 'geozonetype_id', true ); ?>
                    </td>
                </tr>
            </table>
            <input type="hidden" id="geozone_id" name="id" value="<?php echo $row->geozone_id; ?>" />
            <input type="hidden" name="task" value="" />
    </fieldset>
</form>