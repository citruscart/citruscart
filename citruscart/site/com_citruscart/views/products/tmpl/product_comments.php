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

$row= $this->row;
$click = $this->comments_data->click;
$reviews = $this->comments_data->reviews;
$selectsort = $this->comments_data->selectsort;
$result = $this->comments_data->result;
$review_enable=$this->comments_data->review_enable;
$count=$this->comments_data->count;

$url = JURI::getInstance()->__toString();
$root  = JURI::getInstance()->root();
$return_url = str_replace($root , '', $url);
$linkurl= CitruscartHelperProduct::getSocialBookMarkUri( $url );
$Itemid = JRequest::getInt('Itemid', '0');
$publickey = "6LcAcbwSAAAAAIEtIoDhP0cj7AAQMK9hqzJyAbeD";
$baseurl=$this->baseurl;
$user = JFactory::getUser();
$url_validate = JRoute::_( 'index.php?option=com_citruscart&controller=products&view=products&task=validateReview&format=raw' );
$share_review_enable = Citruscart::getInstance()->get('share_review_enable', '0');

if (($review_enable==1)&&($result == 1 || $count > 0 ) ) {
	$emails = CitruscartHelperProduct::getUserEmailForReview( $this->comments_data->product_id );
?>
<div id="product_review_header" class="citruscart_header">
    <span><?php echo JText::_('COM_CITRUSCART_REVIEWS'); ?></span>
</div>
<?php } ?>
 <div>
    <div class="rowDiv" style="padding-top: 5px;">
        <?php if ($review_enable==1 && $result == 1): ?>
        	<div class="leftAlignDiv">
        		<input onclick="citruscartShowHideDiv('new_review_form');" value="<?php echo JText::_('COM_CITRUSCART_ADD_REVIEW'); ?>" type="button" class="btn" />
        	</div>
        <?php endif;?>
    	<div class="rightAlignDiv">
    	<?php if ($review_enable==1 && $count > 0  ): ?>
    		<form name="sortForm" method="post" action="<?php echo JRoute::_($url); ?>">
    		<?php echo JText::_('COM_CITRUSCART_SORT_BY'); ?>:
    		<?php echo CitruscartSelect::selectsort( $selectsort, 'default_selectsort', array('class' => 'inputbox', 'size' => '1','onchange'=>'document.sortForm.submit();') ); ?>
    		</form>
    	<?php endif;?>
    	</div>
    </div>
    <div id="new_review_form" class="rowPaddingDiv" style="display: none;">
    		<div id="validationmessage_comments" style="padding-top: 10px;"></div>
        <form action="<?php echo $click;?>" method="post" class="adminform" name="commentsForm" enctype="multipart/form-data" >
            <div><?php echo JText::_('COM_CITRUSCART_RATING'); ?>: *</div>
            <?php echo CitruscartHelperProduct::getRatingImage( 5, $this, true  ); ?>
            <?php if ($user->guest || !$user->id) {?>
            <div><?php echo JText::_('COM_CITRUSCART_NAME'); ?>: *</div>
            <div><input type="text" maxlength="100" class="inputbox" value="<?php echo base64_decode(JRequest::getVar('rn', ''));?>" size="40" name="user_name" id="user_name"/></div>
        	<div><?php echo JText::_('COM_CITRUSCART_EMAIL'); ?>: *</div>
            <div><input type="text" maxlength="100" class="inputbox" value="<?php echo base64_decode(JRequest::getVar('re', ''));?>" size="40" name="user_email" id="user_email"/></div>
        	<?php }else{?>
        	<input type="hidden" maxlength="100" class="inputbox" value="<?php echo $user->email;?>" size="40" name="user_email" id="user_email"/>
        	<input type="hidden" maxlength="100" class="inputbox" value="<?php echo $user->name;?>" size="40" name="user_name" id="user_name"/>
        	<?php }?>
            <div><?php echo JText::_('COM_CITRUSCART_COMMENT'); ?>: *</div>
            <div><textarea name="productcomment_text" id="productcomment_text" rows="10" style="width: 99%;" ><?php echo base64_decode(JRequest::getVar('rc', ''));?></textarea></div>
            <?php
            	if (Citruscart::getInstance()->get('use_captcha', '0') == 1 ):
            		Citruscart::load( 'CitruscartRecaptcha', 'library.recaptcha' );
            		$recaptcha = new CitruscartRecaptcha();
            ?>
            <div><?php echo $recaptcha->recaptcha_get_html($publickey); ?></div>
            <?php endif;?>
            <input type="button" name="review" id="review" onclick="javscript:CitruscartFormValidation( '<?php echo $url_validate; ?>','validationmessage_comments', 'addReview', document.commentsForm );" value="<?php echo JText::_('COM_CITRUSCART_SUBMIT_COMMENT'); ?>" />
            <input type="hidden" name="product_id"   value="<?php echo $this->comments_data->product_id;?>" />
            <input type="hidden" name="user_id" value="<?php echo $user->id; ?>" />
            <input type="hidden" name="productcomment_rating" id="productcomment_rating" value="" />
            <input type="hidden" name="Itemid" id="Itemid" value="<?php echo $Itemid; ?>" />
            <input type="hidden" name="task" value="" />
        </form>
    </div>
   <?php
   		if($review_enable==1):
   		 foreach ($reviews as $review) :
   ?>
    <div class="rowPaddingDiv">
        <div class="commentsDiv1">
			<div class="rowDiv">
                <div class="userName">
                   <span><?php echo empty($review->user_name) ? ( empty( $review->username ) ? $review->user_email : $review->username ) : $review->user_name;?></span>
                </div>
                <div class="dateDiv" >
                    <?php
                    	echo "(".JHTML::_('date', $review->created_date,'').")";

                    	if($review->helpful_votes_total!=0 ){
                    		echo sprintf( JText::_('COM_CITRUSCART_X_OF_X_FOUND_THIS_HELPFUL'), $review->helpful_votes, $review->helpful_votes_total);
                    	}
                    ?>
                </div>
                <div class="customerRating">
                    <span>
                        <?php echo CitruscartHelperProduct::getRatingImage( $review->productcomment_rating, $this ); ?>
                	</span>
                </div>
            </div>
            <div id="comments" class="commentsDiv">
                <?php echo $review->productcomment_text; ?>
            </div>
       		<?php
						$isFeedback = CitruscartHelperProduct::isFeedbackAlready( $user->id, $review->productcomment_id );
	       		$helpfuness_enable = Citruscart::getInstance()->get('review_helpfulness_enable', '0');

	       		if ($helpfuness_enable && $user->id != $review->user_id && !$isFeedback) :
       		?>
       		<div id="helpful" class="commentsDiv">
      			 <?php echo JText::_('COM_CITRUSCART_WAS_THIS_REVIEW_HELPFUL_TO_YOU'); ?>?
      			 <a href="index.php?option=com_citruscart&view=products&task=reviewHelpfullness&helpfulness=1&productcomment_id=<?php echo $review->productcomment_id; ?>&product_id=<?php echo $review->product_id; ?>"><?php echo JText::_('COM_CITRUSCART_YES'); ?></a>
      			 <a href="index.php?option=com_citruscart&view=products&task=reviewHelpfullness&helpfulness=0&productcomment_id=<?php echo$review->productcomment_id;?>&product_id=<?php echo $review->product_id;?>"><?php echo JText::_('COM_CITRUSCART_NO'); ?></a>
      			 <a href="index.php?option=com_citruscart&view=products&task=reviewHelpfullness&report=1&productcomment_id=<?php echo$review->productcomment_id;?>&product_id=<?php echo $review->product_id;?>">(<?php echo JText::_('COM_CITRUSCART_REPORT_INAPPROPRIATE_REVIEW'); ?>)</a>
      		</div>
      		<?php
      			endif;
            if ($share_review_enable):
          ?>
		      		<div id="links" class="commentsDiv">
		      		<span class="share_review"><?php echo JText::_('COM_CITRUSCART_SHARE_THIS_REVIEW'); ?>:</span>
		      			 <a href="http://www.facebook.com/share.php?u=<?php echo $linkurl;?>" target='_blank'> <img  src="<?php echo $baseurl;?>/media/citruscart/images/bookmark/facebook.png" alt="facebook"/></a>
		      			 <a href="http://twitter.com/home?status=<?php echo $linkurl;?>" target='_blank'> <img  src="<?php echo $baseurl;?>/media/citruscart/images/bookmark/twitter.png" alt="twitter"/></a>
		      			 <a href="http://www.tumblr.com/login?s=<?php echo $linkurl;?>" target='_blank'> <img  src="<?php echo $baseurl;?>/media/citruscart/images/bookmark/link-tumblr.PNG" alt="link-tumblr"/></a>
		      			 <a href="http://www.stumbleupon.com/submit?url=<?php echo $linkurl;?>&title=<?php echo $row->product_name;?>" target='_blank'> <img  src="<?php echo $baseurl;?>/media/citruscart/images/bookmark/stumbleupon.png" alt="stumbleupon"/></a>
		      		</div>
       		<?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif;?>
    <div id="products_footer" class="pagination">
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
</div>