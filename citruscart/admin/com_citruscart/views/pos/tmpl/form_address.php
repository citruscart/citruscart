<?php
	defined('_JEXEC') or die('Restricted access');
	JHtml::_('script', 'media/citruscart/js/citruscart.js', false, false);
	JHtml::_('stylesheet','media/citruscart/css/citruscart.css');

	switch($this->form_prefix)
	{
		case 'shipping_input_':
			$address_type = '2';
			break;
		default:
		case 'billing_input_':
			$address_type = '1';
			break;
	}
	Citruscart::load( 'CitruscartHelperAddresses', 'helpers.addresses' );
	$elements  = CitruscartHelperAddresses::getAddressElementsData( $address_type );
	$session = JFactory::getSession();
	$user_type = $session->get( 'user_type', '', 'citruscart_pos' );
	$guest = true;
	if( $user_type == "existing" || $user_type == "new" )
		$guest = false;

	$key_style = 'width: 140px; text-align: right;';
?>


	<table class="table table-striped table-bordered" style="clear: both;" data-type="<?php echo substr( $this->form_prefix, 0, -1);?>">
		<tbody>
			<?php if( $elements['address_name'][0] ) :
				if( $guest ) : ?>
			<input type="hidden" value="<?php echo JText::_('COM_CITRUSCART_TEMPORARY');?>" name="<?php echo $this->form_prefix;?>address_name" id="<?php echo $this->form_prefix;?>address_name" />
				<?php else: ?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
					<?php if( !$this->guest && $elements['address_name'][1] ): ?>
						<?php echo CitruscartGrid::required(); ?>
					<?php endif;?>
					<label class="key" for="<?php echo $this->form_prefix; ?>address_name"><?php echo JText::_('COM_CITRUSCART_ADDRESS_TITLE'); ?>
				</label>
				</th>
				<td>
					<input name="<?php echo $this->form_prefix; ?>address_name" id="<?php echo $this->form_prefix; ?>address_name" class="inputbox" type="text" maxlength="250" />
				</td>
			</tr>
			<?php
					endif;
				endif;
				if( $elements['title'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['title'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_TITLE');
				?>
				</th>
				<td>
				<input name="<?php echo $this->form_prefix;?>title" id="<?php echo $this->form_prefix;?>title" type="text" size="35" maxlength="250" />
				</td>
			</tr>
			<?php
				endif;
				if( $elements['name'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['name'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_FIRST_NAME');
				?>
				</th>
				<td>
				<input name="<?php echo $this->form_prefix;?>first_name" id="<?php echo $this->form_prefix;?>first_name" type="text" size="35" maxlength="250" />
				</td>
			</tr>
			<?php
				endif;
				if( $elements['middle'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['middle'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_MIDDLE_NAME');
				?>
				</th>
				<td>
				<input type="text" name="<?php echo $this->form_prefix;?>middle_name" id="<?php echo $this->form_prefix;?>middle_name" size="25" maxlength="250" />
				</td>
			</tr>
			<?php
				endif;
				if( $elements['last'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['last'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_LAST_NAME');
				?>
				</th>
				<td>
				<input type="text" name="<?php echo $this->form_prefix;?>last_name" id="<?php echo $this->form_prefix;?>last_name" size="45" maxlength="250" />
				</td>
			</tr>
			<?php
				endif;
				if( $elements['company'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['company'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_COMPANY');
				?>
				</th>
				<td>
				<input type="text" name="<?php echo $this->form_prefix;?>company" id="<?php echo $this->form_prefix;?>company" size="48" maxlength="250" />
				</td>
			</tr>
			<?php
				endif;
				if( $elements['address1'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['address1'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_ADDRESS_LINE_1');
				?>
				</th>
				<td>
				<input type="text" name="<?php echo $this->form_prefix;?>address_1" id="<?php echo $this->form_prefix;?>address_1" size="48" maxlength="250" />
				</td>
			</tr>
			<?php
				endif;
				if( $elements['address2'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['address2'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_ADDRESS_LINE_2');
				?>
				</th>
				<td>
				<input type="text" name="<?php echo $this->form_prefix;?>address_2" id="<?php echo $this->form_prefix;?>address_2" size="48" maxlength="250" />
				</td>
			</tr>
			<?php
				endif;
				if( $elements['city'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['city'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_CITY');
				?>
				</th>
				<td>
				<input type="text" name="<?php echo $this->form_prefix;?>city" id="<?php echo $this->form_prefix;?>city" size="48" maxlength="250" />
				</td>
			</tr>
			<?php
				endif;
				if( $elements['country'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['country'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_COUNTRY');
				?>
				</th>
				<td>
				<?php
				$url = "index.php?option=com_citruscart&format=raw&controller=pos&task=getzones&prefix={$this->form_prefix}&country_id=";
				$attribs = array('class' => 'inputbox',
				'size' => '1',
				'onchange' => 'citruscartDoTask( \'' . $url . '\'+document.getElementById(\'' . $this->form_prefix . 'country_id\').value, \'' . $this->form_prefix . 'zones_wrapper\', \'\');');
				echo CitruscartSelect::country($this->default_country_id, $this->form_prefix . 'country_id', $attribs, $this->form_prefix . 'country_id', false, true);
				?>
				</td>
			</tr>
			<?php
				endif;
				if( $elements['zone'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['zone'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_ZONE');
				?>
				</th>
				<td>
				<div id="<?php echo $this->form_prefix;?>zones_wrapper">
					<?php
					if(!empty($this->zones))
					{
						echo $this->zones;
					}
					else
					{
						echo JText::_('COM_CITRUSCART_SELECT_COUNTRY_FIRST');
					}
					?>
				</div>
				</td>
			</tr>
			<?php
				endif;
				if( $elements['zip'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['zip'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_POSTAL_CODE');
				?>
				</th>
				<td>
				<input type="text" name="<?php echo $this->form_prefix;?>postal_code" id="<?php echo $this->form_prefix;?>postal_code" size="25" maxlength="250"
				<?php if (!empty($this->showShipping)&& $this->forShipping ) { ?>onchange="citruscartGetShippingRates( 'onCheckoutShipping_wrapper', this.form );" <?php }?> />
				</td>
			</tr>
			<?php
				endif;
				if( $elements['phone'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['phone'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_PHONE');
				?>
				</th>
				<td>
				<input name="<?php echo $this->form_prefix;?>phone_1" id="<?php echo $this->form_prefix;?>phone_1" type="text" size="25" maxlength="250" />
				</td>
			</tr>
			<?php
				endif;
				if( $elements['tax_number'][0] ) :
			?>
			<tr>
				<th style="<?php echo $key_style;?>" class="key">
				<?php
					if( $elements['tax_number'][1] ):
						echo CitruscartGrid::required();
					endif;
					echo JText::_('COM_CITRUSCART_CO_TAX_NUMBER');
				?>
				</th>
				<td>
				<input name="<?php echo $this->form_prefix;?>tax_number" id="<?php echo $this->form_prefix;?>tax_number" type="text" size="25" maxlength="250" />
				</td>
			</tr>
			<?php
				endif;

				$data = new JObject();

				JFactory::getApplication()->triggerEvent('onAfterDisplayAddressDetails', array($data, $this->form_prefix));
		?>
</tbody>
</table>

