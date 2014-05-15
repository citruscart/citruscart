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

require_once JPATH_SITE .'/libraries/dioscouri/library/element/media.php';

?>
<?php
$form = $this->form;
$row = $this->row;
$helper_product = new CitruscartHelperProduct();
$multiupload_script = $this->defines->get( 'multiupload_script', 0 );
?>

<fieldset>
    <table class="table">
        <tr>
            <th class="dsc-key">
                <?php echo JText::_('COM_CITRUSCART_PRIMARY_IMAGE'); ?>:
            </th>
            <td class="dsc-value">
                <input name="product_full_image" id="product_full_image" value="<?php echo $row->product_full_image; ?>" type="text" class="input-xxlarge" />
            </td>
        </tr>
        <tr>
            <th class="dsc-key">
                <?php echo JText::_('COM_CITRUSCART_PRIMARY_IMAGE_THUMB'); ?>:
            </th>
            <td class="dsc-value">
                <p class="dsc-clear dsc-tip"><?php echo JText::_('COM_CITRUSCART_PRIMARY_IMAGE_THUMB_TIP'); ?></p>
                <?php $media = new DSCElementMedia(array('readonly'=>false, 'class'=>'input-xxlarge')); ?>
                <?php echo $media->fetchElement( 'product_thumb_image', $row->product_thumb_image ); ?>
            </td>
        </tr>
        <tr>
            <th class="dsc-key">
                <?php echo JText::_('COM_CITRUSCART_UPLOAD_NEW_IMAGES'); ?>
            </th>
            <td>
                <div class="help-block muted">
                    <?php echo JText::_('COM_CITRUSCART_UPLOAD_ZIP_IMAGES_MESSAGE'); ?>
                </div>

                <div class="control-group">
                    <input id="new_product_full_images" name="product_full_image_new[]" type="file" multiple="multiple" <?php if (empty($row->product_id) || !in_array($multiupload_script, array('0', 'uploadify'))) { ?> onchange="CitruscartMakeFileList();" size="40" <?php } ?> />
                </div>

                <div class="help-block dsc-clear">
                    <h4 id="fileList-title" style="display: none;"><?php echo JText::_( "COM_CITRUSCART_FILES_SELECTED" ); ?></h4>
                    <ul id="fileList"></ul>
                    <div id="queue"></div>
                </div>

            </td>
        </tr>
        <tr>
            <th class="dsc-key">
                <?php echo JText::_('COM_CITRUSCART_IMAGES_GALLERY_PATH_OVERRIDE'); ?>
            </th>
            <td>
                <textarea name="product_images_path" id="product_images_path"><?php echo $row->product_images_path; ?></textarea>
                <div class="help-block muted">
                    <?php echo JText::_('COM_CITRUSCART_IF_NO_IMAGE_PATH_OVERRIDE_IS_SPECIFIED_MESSAGE'); ?>
                    <ul>
                        <li>/images/com_citruscart/products/[SKU]</li>
                        <li>/images/com_citruscart/products/[ID]</li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>
</fieldset>

<div id="form-gallery">
<?php

if (!empty($row->product_id)) {
    $this->setLayout( 'form_gallery' ); echo $this->loadTemplate();
}
?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        Citruscart.bindProductGalleryLinks();
    });

    <?php if (!empty($row->product_id) && in_array($multiupload_script, array('0', 'uploadify'))) { ?>
	jQuery(document).ready(function() {


		jQuery('#new_product_full_images').uploadifive({
			'auto' : true,
			'removeCompleted': true,
			'method' : 'post',
			'formData' : {
			   'option':'com_citruscart',
			   'view':'products',
			   'task':'uploadifyImage',
			   'format':'raw',
			   'product_id': '<?php echo $row->product_id ?>',
			   '<?php echo JSession::getFormToken(); ?>': '1'
			},
			'queueID'          : 'queue',
			'uploadScript'     : 'index.php',
			'onQueueComplete' : function() { Citruscart.refreshProductGallery(<?php echo $row->product_id; ?>); jQuery('#new_product_full_images').uploadifive('clearQueue'); },
			'onFallback'   : function() { citruscartJQ('#new_product_full_images').on('change', function(){ CitruscartMakeFileList(); }); },
			'onInit': function() { }
		});
	});
	<?php } ?>

    function CitruscartMakeFileList() {
    	var input = document.getElementById("new_product_full_images");
    	var ul = document.getElementById("fileList");
    	var title = document.getElementById("fileList-title");
    	while (ul.hasChildNodes()) {
    		ul.removeChild(ul.firstChild);
    	}
    	for (var i = 0; i < input.files.length; i++) {
    		var li = document.createElement("li");
    		li.innerHTML = input.files[i].name;
    		ul.appendChild(li);
    	}
    	if(!ul.hasChildNodes()) {
    		var li = document.createElement("li");
    		li.innerHTML = 'No Files Selected';
    		ul.appendChild(li);
    		title.setStyle('display', 'block');
    	} else {
    	    title.setStyle('display', 'block');
    	}
    }
</script>

