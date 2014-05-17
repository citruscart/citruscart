<?php
	defined('_JEXEC') or die('Restricted access');
	JHtml::_('stylesheet', 'media/citruscart/css/pos.css');
	JHtml::_('stylesheet',  'media/citruscart/css/citruscart.css');

	$row = @$this->product;
	$values = @$this->values;
	$changed_attr = isset( $values['changed_attr'] ) ? $values['changed_attr'] : -1;
	$changed_pao = -1;
	if( $changed_attr > -1 ) {
		$changed_pao = $values['attribute_'.$changed_attr];
	}

    Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
	$js_strings = array( 'COM_CITRUSCART_UPDATING_ATTRIBUTES' );
	CitruscartHelperBase::addJsTranslationStrings( $js_strings );
    $helper_product = CitruscartHelperBase::getInstance( 'Product' );
    // Selected attribute options (for child attributes)
    $selected_opts = (!empty($this->selected_opts)) ? json_decode($this->selected_opts) : 0;

    if(!count($selected_opts))
    {
    	$selected_opts = 0;
    }
    $attributes = CitruscartHelperProduct::getAttributes( $row->product_id, $selected_opts );
    $default = CitruscartHelperProduct::getDefaultAttributeOptions($attributes);

    // First view of the page: select the first value in the list
    if(!$selected_opts)
    {
    	$selected_opts = $default;
    	$selected_opts[] = 0;
    }

    foreach ($attributes as $attribute)
    {
        ?>
        <div class="pao" id='productattributeoption_<?php echo $attribute->productattribute_id; ?>'>
            <?php
            echo "<span>".$attribute->productattribute_name." : </span>";

            $key = 'attribute_'.$attribute->productattribute_id;
            $selected = (!empty($values[$key])) ? $values[$key] : $default[$attribute->productattribute_id];
            $attribs = array('class' => 'inputbox',
            				'size' => '1',
            				'onchange'=>"Citruscart.UpdateChangedAttribute( document.adminForm, ".$attribute->productattribute_id.");Citruscart.UpdateAddToCart(  'pos', 'product_buy', document.adminForm, true );",
		        			'changed_attr' => $changed_attr,
		        			'changed_pao' => $changed_pao,
		        			'pid'	=> $row->product_id
							);
            echo CitruscartSelect::productattributeoptions( $attribute->productattribute_id, $selected, $key, $attribs, null, $selected_opts  );
            ?>
        </div>
        <?php
    }
	if( count( $attributes ) ) : ?>
    <input type="hidden" name="changed_attr" value="" />
	<?php
	endif;
    ?>

    <?php if (!empty($this->onDisplayProductAttributeOptions)) : ?>
        <div class='onDisplayProductAttributeOptions_wrapper'>
        <?php echo $this->onDisplayProductAttributeOptions; ?>
        </div>
    <?php endif; ?>

    <?php echo JText::_('COM_CITRUSCART_QUANTITY'); ?>
    <input type="text" name="quantity" value="1" size="10" />
    <br/>
    <?php echo JText::_('COM_CITRUSCART_BASE_PRICE'); ?>: <?php echo CitruscartHelperBase::currency( $row->price ); ?>
    <br/>

    <input type="submit" name="add_to_cart" value="<?php echo JText::_('COM_CITRUSCART_ADD_TO_ORDER'); ?>" class="btn btn-success" />
    <input type="hidden" name="task" id="task" value="addtocart" />
    <input type="hidden" name="product_id" value="<?php echo $row->product_id; ?>" />