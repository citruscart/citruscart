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


JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
$state = $this->state;
$items = $this->items;

?>
<style>
.citruscart-product-main-images {

	}

.ul-images{
	margin-left :5px;
	margin-bottom:5px;
	padding:5px 10px;
	border:1px solid #ccc;
	text-align: center;

	}

</style>
<div class='categoryheading'>
<?php echo JText::_('COM_CITRUSCART_SEARCH_RESULTS_FOR').': '.$state->filter; ?>
</div>

<form action="<?php echo JRoute::_( $form['action']."&limitstart=".$state->limitstart )?>" method="post" name="adminForm" enctype="multipart/form-data">

     <div style="float: right; padding: 5px;"><?php echo $this->pagination->getResultsCounter(); ?></div>
	     <?php echo $this->pagination->getListFooter(); ?>
     <?php if (empty($items)) :?>
    		<?php echo JText::_('COM_CITRUSCART_NO_MATCHING_ITEMS_FOUND'); ?>
	 <?php else :?>
	 <ul id="image-list" class="nav navbar-nav">
			<?php foreach ($items as $item) :?>
			<li class="ul-images" >
               <a href="<?php echo JRoute::_( $item->link ); ?>">
	               <?php echo CitruscartHelperProduct::getImage($item->product_id); ?>
                 </a>
                <p class="price"><?php echo CitruscartHelperBase::currency($item->price); ?></p>

       </li>
	<?php endforeach;?>
	</ul>
	<?php endif; ?>
      </tbody>
    </table>
<?php echo $this->form['validate']; ?>
</form>