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
JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/');
$downloadItems = $this->product_file_data->downloadItems;
$nondownloadItems = $this->product_file_data->nondownloadItems;
?>

    <div id="product_files">
        <div id="product_files_header" class="citruscart_header">
            <span><?php echo JText::_('COM_CITRUSCART_FILES'); ?></span>
        </div>

        <?php
        $k = 0;
        foreach ($downloadItems as $item): ?>
        <div class="productfile">
            <span class="productfile_image">
                <a href="<?php echo JRoute::_( 'index.php?option=com_citruscart&view=products&task=downloadfile&format=raw&id='.$item->productfile_id."&product_id=".$this->product_file_data->product_id); ?>">
                    <img src="<?php echo Citruscart::getURL('images')."download.png"; ?>" alt="<?php echo JText::_('COM_CITRUSCART_DOWNLOAD') ?>" style="height: 24px; padding: 5px; vertical-align: middle;" />
                </a>
            </span>
            <span class="productfile_link" style="vertical-align: middle;" >
                <a href="<?php echo JRoute::_( 'index.php?option=com_citruscart&view=products&task=downloadfile&format=raw&id='.$item->productfile_id."&product_id=".$this->product_file_data->product_id); ?>"><?php echo $item->productfile_name; ?></a>
            </span>
        </div>
        <?php $k = 1 - $k; ?>
        <?php endforeach;

        foreach ($nondownloadItems as $item): ?>
        <div class="productfile">
            <span class="productfile_image">
                   <img src="<?php echo Citruscart::getURL('images')."download.png"; ?>" alt="<?php echo JText::_('COM_CITRUSCART_DOWNLOAD') ?>" style="height: 24px; padding: 5px; vertical-align: middle;" />
                           </span>
            <span class="productfile_link" style="vertical-align: middle;" >
               <?php echo $item->productfile_name; ?>
            </span>
        </div>
        <?php $k = 1 - $k; ?>
        <?php endforeach; ?>

    </div>


