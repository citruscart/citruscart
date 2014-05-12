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

<?php if (!empty($items)) { ?>
    <ul>
    <?php foreach ($items as $item) {
        $itemid_string = null;
        if ($itemid = $helper->model->getItemid($item->wishlist_id) ) {
            $itemid_string = "&Itemid=" . $itemid;
        }
        ?>
        <li>
            <a href="<?php echo JRoute::_( "index.php?option=com_citruscart&view=wishlists&task=view&id=" . $item->wishlist_id . $itemid_string ); ?>">
                <span class="wishlist-name wishlist-<?php echo $item->wishlist_id; ?>">
                    <?php echo $item->wishlist_name; ?>
                </span>
            </a>
        </li>
    <?php } ?>
    </ul>
<?php } ?>