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

defined('_JEXEC') or die('Restricted access');
//JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
//JHtml::_('stylesheet', 'media/citruscart/css/citruscart_checkout_onepage.css');

$doc = JFactory::getDocument();
JHTML::_('behavior.modal');
$doc->addStyleSheet(JUri::root().'media/citruscart/css/citruscart_checkout_onepage.css');
$doc->addScript(JUri::root().'media/citruscart/js/citruscart.js');
?>
<?php //JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>
<?php $form = $this->form; ?>
<?php $row = $this->row;
JFilterOutput::objectHTMLSafe( $row );
?>

<div class='componentheading'>
    <span><?php echo JText::_('COM_CITRUSCART_EDIT_BASIC_INFORMATION'); ?></span>
</div>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" onsubmit="citruscartFormValidation( '<?php echo $form['validation']; ?>', 'validationmessage', document.adminForm.task.value, document.adminForm )" method="post" class="adminform"  id="adminForm"  name="adminForm" enctype="multipart/form-data" >
    <div style="float: right;">
        <input type="button" onclick="citruscartSubmitForm('save');" value="<?php echo JText::_('COM_CITRUSCART_SUBMIT'); ?>" />
    </div>

    <?php
    echo "<< <a href='".JRoute::_("index.php?option=com_citruscart&view=accounts")."'>".JText::_('COM_CITRUSCART_CANCEL_AND_RETURN_TO_PROFILE')."</a>";
    ?>

    <div id="validationmessage"></div>

	<table>
	    <tbody>
	    <tr>
	        <th style="width: 100px; text-align: right;" class="key">
	            <?php echo JText::_('COM_CITRUSCART_TITLE'); ?>
	        </th>
	        <td>
	            <input name="title" id="title"
	            type="text" size="5" maxlength="250"
	            value="<?php echo isset($row->title) ? $row->title : ""; ?>" />
	        </td>
	    </tr>
	    <tr>
	        <th style="width: 100px; text-align: right;" class="key">
	             <?php echo JText::_('COM_CITRUSCART_FIRST_NAME'); ?>
	        </th>
	        <td>
	            <input name="first_name" id="first_name"
	            type="text" size="35" maxlength="250"
	            value="<?php echo isset($row->first_name) ? $row->first_name : ""; ?>" />
	        </td>
	    </tr>
	    <tr>
	        <th style="width: 100px; text-align: right;" class="key">
	             <?php echo JText::_('COM_CITRUSCART_MIDDLE_NAME'); ?>
	        </th>
	        <td>
	           <input type="text" name="middle_name"
	            id="middle_name" size="25" maxlength="250"
	            value="<?php echo isset($row->middle_name) ? $row->middle_name : ""; ?>" />
	        </td>
	    </tr>
	    <tr>
	        <th style="width: 100px; text-align: right;" class="key">
	             <?php echo JText::_('COM_CITRUSCART_LAST_NAME'); ?>
	        </th>
	        <td>
	           <input type="text" name="last_name"
	            id="last_name" size="45" maxlength="250"
	            value="<?php echo isset($row->last_name) ? $row->last_name : ""; ?>" />
	        </td>
	    </tr>
	    <tr>
	        <th style="width: 100px; text-align: right;" class="key">
	          <?php echo JText::_('COM_CITRUSCART_COMPANY'); ?>
	        </th>
	        <td><input type="text" name="company" id="company"
	            size="48" maxlength="250"
	            value="<?php echo isset($row->company) ? $row->company : ""; ?>" /></td>
	    </tr>
	    <tr>
	        <th style="width: 100px; text-align: right;" class="key">
	            <?php echo JText::_('COM_CITRUSCART_PHONE'); ?>
	        </th>
	        <td>
	            <input type="text" name="phone_1" id="phone_1"
	            size="25" maxlength="250"
	            value="<?php echo isset($row->phone_1) ? $row->phone_1 : ""; ?>" />
	        </td>
	    </tr>
	    <tr>
	        <th style="width: 100px; text-align: right;" class="key">
	            <?php echo JText::_('COM_CITRUSCART_CELL'); ?>
	        </th>
	        <td>
	            <input type="text" name="phone_2" id="phone_2"
	            size="25" maxlength="250"
	            value="<?php echo isset($row->phone_2) ? $row->phone_2 : ""; ?>" />
	        </td>
	    </tr>
	    <tr>
	        <th style="width: 100px; text-align: right;" class="key">
	            <?php echo JText::_('COM_CITRUSCART_FAX'); ?>
	        </th>
	        <td>
	            <input type="text" name="fax" id="fax"
	            size="25" maxlength="250"
	            value="<?php echo isset($row->fax) ? $row->fax : ""; ?>" />
	        </td>
	    </tr>
        <tr>
            <th style="width: 100px; text-align: right;" class="key">
                <?php echo JText::_('COM_CITRUSCART_EMAIL_FORMAT'); ?>
            </th>
            <td>
                <?php echo CitruscartSelect::booleans( isset($row->html_emails) ? $row->html_emails : "", 'html_emails', '', '', '', '', JText::_('COM_CITRUSCART_HTML'), JText::_('COM_CITRUSCART_PLAIN_TEXT') ); ?>
            </td>
        </tr>
	    </tbody>
	</table>

    <input type="button" onclick="citruscartSubmitForm('save');" value="<?php echo JText::_('COM_CITRUSCART_SUBMIT'); ?>" />

    <input type="hidden" name="id" value="<?php echo $row->user_id; ?>" />
    <input type="hidden" name="task" id="task" value="" />
    <?php echo $form['validate']; ?>
</form>