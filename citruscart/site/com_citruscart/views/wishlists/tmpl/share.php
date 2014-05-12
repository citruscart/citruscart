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

if( $this->only_redirect ) : ?>
<?php
	Citruscart::load( "CitruscartHelperRoute", 'helpers.route' );
	$router = new CitruscartHelperRoute();
?>
<script type="text/javascript">
	citruscartJQ( function() {
		window.parent.location = '<?php echo JRoute::_( "index.php?option=com_citruscart&view=wishlists&email=true&Itemid=".$router->findItemid( array('view'=>'wishlists') ), false ); ?>';
		window.close();
	});
</script>
<?php else : ?>

	<div id='page-title-navigation'>
	    <h1 id="page-title"><?php echo JText::_('COM_CITRUSCART_SHARE_WISHLIST_ITEMS'); ?></h1>
	</div>

	<form action="<?php echo JRoute::_('index.php?option=com_citruscart&view=wishlists&task=shareitems&tmpl=component' ); ?>" method="post" name="adminForm" enctype="multipart/form-data">
	    <div class="wrap">
	        <div class="form_key">
	            <?php echo JText::_('COM_CITRUSCART_ITEMS_BEING_SHARED'); ?>
	        </div>
	        <div class="form_input">
	            <ul>
	            <?php foreach ($this->items as $item) { ?>
	                <li>
	                    <?php
	                    	echo $item->product_name;
							$attributes = explode( ',', $item->product_attributes );
				        	$tbl = JTable::getInstance('ProductAttributes', 'CitruscartTable');
							$tbl_opt = JTable::getInstance( 'ProductAttributeOptions', 'CitruscartTable' );
							$product_name = $item->product_name;
							$attr_list = array();
					        for( $i = 0, $c = count( $attributes ); $i < $c; $i++ )
					        {
					        	$tbl_opt->load( $attributes[$i] );
								$tbl->load( $tbl_opt->productattribute_id );
			        			$item->link .= '&attribute_'.$tbl_opt->productattribute_id.'='.$attributes[$i];
								$attr_list []= $tbl->productattribute_name.': '.$tbl_opt->productattributeoption_name;
					        }
							if( count( $attr_list ) ) {
								echo ' ('.implode( '; ', $attr_list ).')';
							}
	                    ?>
	                    <input type="hidden" name="cid[]" value="<?php echo $item->wishlist_id; ?>" />
	                </li>
	            <?php } ?>
	            </ul>
	        </div>
	    </div>

	    <div class="wrap">
	        <div class="form_key">
	            <?php echo JText::_('COM_CITRUSCART_EMAIL_RECIPIENTS'); ?>
	        </div>
	        <div class="form_input">
	            <p class="tip"><?php echo JText::_('COM_CITRUSCART_EMAIL_RECIPIENTS_TIP'); ?></p>
	            <textarea name="share_emails" style="width: 95%;"></textarea>
	        </div>
	    </div>

	    <div class="wrap">
	        <div class="form_key">
	            <?php echo JText::_('COM_CITRUSCART_MESSAGE'); ?>
	        </div>
	        <div class="form_input">
	            <p class="tip"><?php echo JText::_('COM_CITRUSCART_MESSAGE_TIP'); ?></p>
	            <textarea name="share_message" style="width: 95%; height: 150px;"></textarea>
	        </div>
	    </div>

	    <div class="wrap">
	        <input type="submit" class="btn submit" name="shareitems" value="<?php echo JText::_('COM_CITRUSCART_SHARE_NOW'); ?>" />
	        <?php echo JHTML::_( 'form.token' ); ?>
	    </div>
	</form>

<?php endif; ?>