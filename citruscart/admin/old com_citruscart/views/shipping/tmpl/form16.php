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


?>
<?php $form = $this ->form; ?>
<?php $row = $this ->row; ?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data">

    <legend>
        <?php echo JText::_('COM_CITRUSCART_BASIC_INFORMATION'); ?>
    </legend>

    <table class="table table-striped table-bordered">
        <tr>
            <td width="100" align="right" class="key"><?php echo JText::_('COM_CITRUSCART_NAME'); ?>:</td>
            <td><input name="name" id="name" value="<?php echo isset($row-> name) ? $row -> name : "" ?>" size="48" maxlength="250" type="text" />
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key"><?php echo JText::_('COM_CITRUSCART_ORDERING'); ?>:</td>
            <td><input name="ordering" id="ordering" value="<?php echo isset($row->ordering) ? $row -> ordering : ""  ?>" size="10" maxlength="250" type="text" />
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key"><label for="currency_enabled"> <?php echo JText::_('COM_CITRUSCART_ENABLED'); ?>:
            </label>
            </td>
            <td><?php echo CitruscartSelect::btbooleanlist('enabled', '', isset($row -> enabled) ? $row->enabled : "" ); ?>
            </td>
        </tr>
    </table>


    <legend>
        <?php echo JText::_('COM_CITRUSCART_PARAMETERS'); ?>
    </legend>
    <?php
    $path = JPATH_SITE.'/plugins/'.isset($row->folder) ? $row->folder : "" .'/'.isset($row->element) ? $row->element : "" .'/jform/'. isset($row->element) ? $row->element : "" .'.xml';

    if (file_exists($path))
    {
        $form = JForm::getInstance(isset($row->element) ? $row->element : "", $path);

        $language = JFactory::getLanguage();
        $language -> load('plg_citruscart_'.$row->element, JPATH_ADMINISTRATOR, 'en-GB', true);
        $language -> load('plg_citruscart_'.$row->element, JPATH_ADMINISTRATOR, null, true);

        foreach($row->data as $k => $v)
        {
            $form->setValue($k, 'params', $v);
        }

        $fieldSets = $form->getFieldsets();
        foreach ($fieldSets as $name => $fieldSet) :
        ?>
            <?php $hidden_fields = ''; ?>
            <table class="table table-striped table-bordered">
                <?php foreach ($form->getFieldset($name) as $field) : ?>
                <?php
                if (strtolower($field->type) == 'radio') {
                    $form->setFieldAttribute($field->fieldname, 'class', 'radio btn-group', $field->group);
                }
                ?>
                <?php if (!$field->hidden) : ?>
                <tr>
                    <td class="dsc-key"><?php echo $field -> label; ?></td>
                    <td class="dsc-value">
                        <div class="control-group">
                            <div class="controls">
                                <?php echo $field -> input; ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php else : $hidden_fields.= $field->input; ?>
                <?php endif; ?>
                <?php endforeach; ?>
            </table>
            <?php echo $hidden_fields; ?>
        <?php endforeach; ?>
        <?php
    } else {
        echo "No Params";
    }
    ?>

    <input type="hidden" name="extension_id" value="<?php echo $row -> extension_id; ?>" /> <input type="hidden" name="type" value="plugin" /> <input type="hidden" name="task" value="" />

</form>
