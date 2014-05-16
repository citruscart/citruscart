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
JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
JHTML::_('script', 'core.js', 'media/system/js/');

$form = $this->form;
$row = $this->row;
$carts = $this->carts;
$procoms=$this->procoms;
$orders=$this->orders;
$subs=$this->subs;
$surrounding = $this->surrounding;
$total_cart=$this->total_cart;

Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
$helper_user = CitruscartHelperBase::getInstance( 'user' );
$helper_product = CitruscartHelperBase::getInstance( 'product' );

$name = $row->first_name.' '.$row->last_name;
if( $name == ' ' ) {
	$address = $helper_user->getPrimaryAddress( $row->id, 'billing' );
	if( is_object( $address ) ) {
		$name = $address->first_name. ' '.$address->last_name;
	}
}
?>

<form action="<?php echo JRoute::_( $form['action'] )?>" method="post" class="adminform" name="adminForm" id="adminForm">
<?php echo CitruscartGrid::pagetooltip( 'users_view' ); ?>
<table width="100%" border="0">
	<tr>
		<td colspan="2">
			<h2 style="padding:0px; margin:0px;"><?php echo $name; ?></h2>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<fieldset>
				<legend><?php echo JText::_('COM_CITRUSCART_BASIC_USER_INFO'); ?></legend>
				<div id="citruscart_header">
					<table class="table table-striped table-bordered">
						<tr>
							<td  align="right" class="key">
		                        <label for="name">
		                        	<?php echo JText::_('COM_CITRUSCART_USERNAME'); ?>:
		                        </label>
	                    	</td>
	                    	<td style="width:120px;">
	                        	<div class="name"><?php echo $row->username; ?></div>
	                    	</td>
	                    	<td  align="right" class="key">
		                        <label for="registerDate">
		                        	<?php echo JText::_('COM_CITRUSCART_REGISTERED'); ?>:
		                        </label>
		                    </td>
		                    <td>
		                        <div class="registerDate"><?php echo JHTML::_('date', $row->registerDate, $this->defines->get('date_format')); ?></div>
		                    </td>
		                    <td rowspan="3" align="center" valign="top">
		                    	<div style="padding:0px; margin-bottom:5px;width:auto;">
									<?php echo $helper_user->getAvatar($row->id);?>
								</div>
		                      <?php
										if(version_compare(JVERSION,'1.6.0','ge')) {
										// Joomla! 1.6+ code here
										$url = $this->defines->get( "user_edit_url", "index.php?option=com_users&task=user.edit&id=");
										} else {
										// Joomla! 1.5 code here
										$url = $this->defines->get( "user_edit_url", "index.php?option=com_users&view=user&task=edit&cid[]=");
										}
										//

										$url .= $row->id;
										$text = '<button class="btn btn-primary" >'.JText::_('COM_CITRUSCART_EDIT_USER').'</button>';
								?>
		                        <div ><?php echo CitruscartUrl::popup( $url, $text, array('update' => true) ); ?></div>
		                    </td>
						</tr>
						<tr>
							<td align="right" class="key" key">
		                        <label for="email">
		                        	<?php echo JText::_('COM_CITRUSCART_EMAIL'); ?>:
		                        </label>
	                    	</td>
	                    	<td>
	                        	<div class="name"><?php echo $row->email; ?></div>
	                    	</td>
	                    	<td align="right" class="key">
		                        <label for="lastvisitDate">
		                        	<?php echo JText::_('COM_CITRUSCART_LAST_VISITED'); ?>:
		                        </label>
		                    </td>
		                    <td>
		                        <div class="lastvisitDate"><?php echo JHTML::_('date', $row->lastvisitDate, $this->defines->get('date_format')); ?></div>
		                    </td>
						</tr>
						<tr>
							<td  align="right" class="key" style="width:85px;">
		                        <label for="id">
		                        	<?php echo JText::_('COM_CITRUSCART_ID'); ?>:
		                        </label>
		                    </td>
		                    <td>
		                        <div class="id"><?php echo $row->id; ?></div>
		                    </td>
		                    <td align="right" class="key" style="width:85px;">
		                        <label for="group_name">
		                        	<?php echo JText::_('COM_CITRUSCART_USER_GROUP'); ?>:
		                        </label>
		                    </td>
		                    <td>
		                      	<div class="id"><?php echo $row->group_name; ?></div>
		                    </td>
						</tr>
						<?php if( $this->defines->get( 'display_subnum', 0 ) ) :?>
						<tr>
							<td  align="right" class="key" style="width:85px;">
		                        <label for="sub_number">
		                        	<?php echo JText::_('COM_CITRUSCART_SUB_NUM'); ?>:
		                        </label>
		                    </td>
		                    <td>
		                        <div class="sub_number"><input name="sub_number" id="sub_number" value="<?php echo $row->sub_number; ?>" /></div>
		                    </td>
	                    	<td >
	                    			<button name="submit_number" id="submit_number" onclick="citruscartSubmitForm('change_subnum')"><?php echo JText::_('COM_CITRUSCART_CHANGE_SUB_NUM'); ?></button>
		                    </td>
		                    <td></td>
						</tr>
						<?php endif; ?>
					</table>
				</div>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td width="50%" valign="top">
			<div class="accordion" id="accordion1">
			  <div class="accordion-group">
			    <div class="accordion-heading">
			      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapseSummary">
					<?php echo JText::_('COM_CITRUSCART_SUMMARY_DATA'); ?>
			      </a>
			    </div>
			    <div id="collapseSummary" class="accordion-body collapse in">
			      <div class="accordion-inner">
					<table class="table table-striped table-bordered"  width="100%">
						<tr>
							<td class="key" align="right" style="width:250px;">
								<?php echo JText::_('COM_CITRUSCART_NUMBER_OF_COMPLETED_ORDERS'); ?>:
							</td>
							<td align="right">
								<div class="id"><?php echo count($orders); ?></div>
							</td>
						</tr>
						<tr>
							<td class="key" align="right" style="width:250px;">
								<?php echo JText::_('COM_CITRUSCART_TOTAL_AMOUNT_SPENT'); ?>:
							</td>
							<td align="right">
								<div class="id"><?php echo CitruscartHelperBase::currency ($this->spent); ?></div>
							</td>
						</tr>
						<tr>
							<td class="key" align="right" style="width:250px;">
								<?php echo JText::_('COM_CITRUSCART_TOTAL_USER_REVIEWS'); ?>:
							</td>
							<td align="right">
								<div class="id"><?php echo count($procoms); ?></div>
							</td>
						</tr>
					</table>
			      </div>
			    </div>
			  </div>
			</div>

			<div class="accordion" id="accordion2">
			  <div class="accordion-group">
			    <div class="accordion-heading">
			      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseLast5Orders">
					<?php echo JText::_('COM_CITRUSCART_LAST_5_COMPLETED_ORDERS'); ?>
			      </a>
			    </div>
			    <div id="collapseLast5Orders" class="accordion-body collapse in">
			      <div class="accordion-inner">
					<table class="table table-striped table-bordered" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 5px;">
									<?php echo JText::_('COM_CITRUSCART_ID'); ?>
								</th>
								<th style="width: 200px;">
									<?php echo JText::_('COM_CITRUSCART_DATE'); ?>
								</th>
								<th style="width: 150px; text-align: right;">
									<?php echo JText::_('COM_CITRUSCART_TOTAL'); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="20"></td>
							</tr>
						</tfoot>
						<tbody>
							<?php $i=0; ?>
							<?php foreach ($orders as $order) : ?>
								<tr>
									<td align="center">
										<?php echo $order->order_id; ?>
									</td>
									<td style="text-align:left;">
										<a href="index.php?option=com_citruscart&view=orders&task=view&id=<?php echo $order->order_id; ?>" >
											<?php echo $order->created_date; ?>
										</a>
									</td>
									<td style="text-align:right;">
										<?php echo CitruscartHelperBase::currency($order->order_total); ?>
									</td>
								</tr>
								<?php if ($i==4) break;?>
							<?php ++$i; ?>
							<?php endforeach; ?>
							<?php if (!count($orders)) : ?>
								<tr>
									<td colspan="10" align="center"><?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
			      </div>
			    </div>
			  </div>
			</div>

		</td>
		<td width="50%" valign="top">
			<div class="accordion" id="accordion3">
			  <div class="accordion-group">
			    <div class="accordion-heading">
			      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#collapseSubs">
					<?php echo JText::_('COM_CITRUSCART_LIST_OF_ACTIVE_SUBSCRIPTIONS'); ?>
			      </a>
			    </div>
			    <div id="collapseSubs" class="accordion-body collapse in">
			      <div class="accordion-inner">
					<table class="table table-striped table-bordered" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 5px;">
									<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
								</th>
								<th style="width: 200px;">
									<?php echo JText::_('COM_CITRUSCART_TYPE'); ?>
								</th>
								<th style="width: 200px;">
									<?php echo JText::_('COM_CITRUSCART_ORDER'); ?>
								</th>
								<th style="text-align: center;  width: 200px;">
									<?php echo JText::_('COM_CITRUSCART_EXPIRES'); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="20"></td>
							</tr>
						</tfoot>
						<tbody>
							<?php $i=0; ?>
							<?php foreach ($subs as $sub) : ?>
								<tr>
									<td align="center">
										<?php echo $i + 1; ?>
									</td>
									<td style="text-align:left;">
										<a href="	index.php?option=com_citruscart&view=subscriptions&task=view&id=<?php echo $sub->subscription_id; ?>" >
											<?php echo $sub->product_name; ?>
										</a>
									</td>
									<td style="text-align:center;">
										<a href="	index.php?option=com_citruscart&view=subscriptions&task=view&id=<?php echo $sub->subscription_id; ?>" >
											<?php echo $sub->order_id; ?>
										</a>
									</td>
									<td style="text-align:center;">
										<a href="	index.php?option=com_citruscart&view=subscriptions&task=view&id=<?php echo $sub->subscription_id; ?>" >
											<?php if($sub->subscription_lifetime == 1)
												{
													 echo JText::_('COM_CITRUSCART_LIFETIME');
												}
											?>
											<?php echo JHTML::_('date', $sub->expires_datetime, $this->defines->get('date_format')); ?>
										</a>
									</td>
								</tr>
							<?php ++$i; ?>
							<?php endforeach; ?>

							<?php if (!count($subs)) : ?>
								<tr>
									<td colspan="10" align="center"><?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
									</td>
								</tr>
							<?php endif; ?>

						</tbody>
					</table>
			      </div>
			    </div>
			  </div>
			</div>

			<div class="accordion" id="accordion4">
			  <div class="accordion-group">
			    <div class="accordion-heading">
			      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion4" href="#collapseCart">
					<?php echo JText::_('COM_CITRUSCART_CART'); ?>
			      </a>
			    </div>
			    <div id="collapseCart" class="accordion-body collapse in">
			      <div class="accordion-inner">
					<table class="table table-striped table-bordered" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 5px;">
									<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
								</th>
								<th style="width: 200px;">
									<?php echo JText::_('COM_CITRUSCART_PRODUCTS'); ?>
								</th>
								<th style="width: 200px;">
									<?php echo JText::_('COM_CITRUSCART_PRICE'); ?>
								</th>
								<th style="text-align: center;  width: 200px;">
									<?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?>
								</th>
								<th style="width: 150px; text-align: right;">
									<?php echo JText::_('COM_CITRUSCART_TOTAL'); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="20"></td>
							</tr>
						</tfoot>
						<tbody>
							<?php $i=0; ?>
							<?php foreach ($carts as $cart) : ?>
								<tr>
									<td align="center">
										<?php echo $i + 1; ?>
									</td>
									<td style="text-align:left;">
										<a href="index.php?option=com_citruscart&view=products&task=edit&id=<?php echo $cart->product_id; ?>" >
											<?php echo $cart->product_name; ?>
										</a>
									</td>
									<td style="text-align:right;">
										<?php echo CitruscartHelperBase::currency($cart->product_price); ?>
									</td>
									<td style="text-align:center;">
										<?php echo $cart->product_qty;?>
									</td>
									<td style="text-align:right;">
										<?php echo CitruscartHelperBase::currency($cart->total_price); ?>
									</td>
								</tr>
							<?php ++$i; ?>
							<?php endforeach; ?>
							<?php if (!count($carts)) : ?>
								<tr>
									<td colspan="5" align="center"><?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
						<thead>
							<tr>
								<th style="width: 5px;">
									&nbsp;
								</th>
								<th style="width: 200px;">
									&nbsp;
								</th>
								<th style="width: 200px;">
									&nbsp;
								</th>

								<th style="text-align: center;  width: 200px;">
									<?php echo JText::_('COM_CITRUSCART_TOTAL'); ?>
								</th>
								<th style="width: 150px; text-align: right;">
									<?php echo CitruscartHelperBase::currency($total_cart); ?>
								</th>
							</tr>
						</thead>
						</table>
					</div>
				</div>
			</div>
		</div>

			<div class="accordion" id="accordion5">
			  <div class="accordion-group">
			    <div class="accordion-heading">
			      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion5" href="#collapse5Reviews">
					<?php echo JText::_('COM_CITRUSCART_LAST_5_REVIEWS_POSTED'); ?>
			      </a>
			    </div>
			    <div id="collapse5Reviews" class="accordion-body collapse in">
			      <div class="accordion-inner">
					<table class="table table-striped table-bordered" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 5px;">
									<?php echo JText::_('COM_CITRUSCART_NUM'); ?>
								</th>
								<th>
									<?php echo JText::_('COM_CITRUSCART_PRODUCTS_PLUS_COMMENTS'); ?>
								</th>
								<th style="width: 200px;">
									<?php echo JText::_('COM_CITRUSCART_USER_RATING'); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="20"></td>
							</tr>
						</tfoot>
						<tbody>
							<?php $i=0; ?>
							<?php foreach ($procoms as $procom) : ?>
								<tr>
									<td align="center">
										<?php echo $i + 1; ?>
									</td>
									<td style="text-align:left;">
										<a href="index.php?option=com_citruscart&view=productcomments&task=edit&id=<?php echo $procom->product_id; ?>" >
											<?php echo $procom->p_name; ?></a><br/><?php echo $procom->trimcom; ?>
									</td>
									<td style="text-align:center;">
										<?php  echo $helper_product->getRatingImage( $procom->productcomment_rating ); ?>
									</td>
								</tr>
								<?php if ($i==4) break;?>
							<?php ++$i; ?>
							<?php endforeach; ?>
							<?php if (!count($procoms)) : ?>
								<tr>
									<td colspan="3" align="center"><?php echo JText::_('COM_CITRUSCART_NO_ITEMS_FOUND'); ?>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			</div>
			</div>
		</td>
	</tr>
</table>
<table style="width: 100%;">
	<tr>
		<td style="width: 70%; max-width: 70%; vertical-align: top; padding: 0px 5px 0px 5px;">
			<?php
            $modules = JModuleHelper::getModules("citruscart_user_main");
            $document   = JFactory::getDocument();
            $renderer   = $document->loadRenderer('module');
            $attribs    = array();
            $attribs['style'] = 'xhtml';
            foreach ( $modules as $mod )
            {
                echo $renderer->render($mod, $attribs);
            }
            ?>
		</td>
		<td style="vertical-align: top; width: 30%; min-width: 30%; padding: 0px 5px 0px 5px;">
			<?php
            $modules = JModuleHelper::getModules("citruscart_user_right");
            $attribs    = array();
            $attribs['style'] = 'xhtml';
            foreach ( $modules as $mod )
            {
                echo $renderer->render($mod, $attribs);
            }
            ?>
		</td>
	</tr>
</table>
	<input type="hidden" name="prev" value="<?php echo intval($surrounding["prev"]); ?>" />
    <input type="hidden" name="next" value="<?php echo intval($surrounding["next"]); ?>" />
    <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
    <input type="hidden" name="task" value="" />
</form>
