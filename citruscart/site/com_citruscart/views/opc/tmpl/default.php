<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
?>

<?php $active = false;
$doc->addScript(JUri::root().'media/citruscart/js/citruscart.js');
$doc->addScript(JUri::root().'media/citruscart/js/class.js');
$doc->addScript(JUri::root().'media/citruscart/js/validation.js');
$doc->addScript(JUri::root().'media/citruscart/js/opcaccordion.js');
$doc->addScript(JUri::root().'media/citruscart/js/opc.js');
$doc->addStyleSheet(JUri::root().'media/citruscart/css/opc.css');

?>

<!-- Get the application -->
<?php $app = JFactory::getApplication();?>

<?php
$cart_itemid = $this->router->findItemid( array('view'=>'carts') );
if (empty($cart_itemid)) {
    //$cart_itemid = JRequest::getInt('Itemid');
	$cart_itemid = $app->input->getInt('Itemid');
}

$guest_checkout_enabled = $this->defines->get('guest_checkout_enabled');
$failureUrl = $this->defines->get('opc_failure_url', JRoute::_( "index.php?option=com_citruscart&view=carts&Itemid=".$cart_itemid ) );

Citruscart::load( 'CitruscartHelperAddresses', 'helpers.addresses' );
$js_strings = array( 'COM_CITRUSCART_PLEASE_CHOOSE_REGISTER', 'COM_CITRUSCART_PLEASE_CHOOSE_REGISTER_OR_CHECKOUT_AS_GUEST' );
CitruscartHelperAddresses::addJsTranslationStrings( $js_strings );

$doc = JFactory::getDocument();
$js = 'CitruscartJQ(document).ready(function(){
    Opc = new CitruscartOpc("#opc-checkout-steps", { guestCheckoutEnabled: '.$guest_checkout_enabled.', urls: { failure: "'.$failureUrl.'" } });';
    if (empty($this->user->id)) {
        $js .= 'Opc.gotoSection("checkout-method");';
    } else {
        $js .= 'Opc.gotoSection("billing");';
    }
    if (!empty($this->showShipping)) {
        $js .= 'Opc.shipping = new CitruscartShipping("#opc-shipping-form");';
    }
    $js .= 'Opc.payment = new CitruscartPayment("#opc-payment-form");';
$js .= '});';
$doc->addScriptDeclaration($js);
?>

<ol id="opc-checkout-steps">

    <?php if (empty($this->user->id)) { ?>
    <li id="opc-checkout-method" class="opc-section allow <?php if (empty($active)) { $active = 'opc-checkout-method'; echo 'active'; } ?>">
        <div class="opc-section-title dsc-wrap">
            <h4>
                <?php echo JText::_( "COM_CITRUSCART_CHECKOUT_METHOD" ); ?>
                <a class="opc-change" href="#"><?php echo JText::_( "COM_CITRUSCART_CHANGE" ); ?></a>
            </h4>
        </div>
        <div id="opc-checkout-method-body" class="opc-section-body <?php echo ($active != 'opc-checkout-method') ? 'opc-hidden' : 'opc-open'; ?>">
            <?php $this->setLayout('loginregister'); echo $this->loadTemplate(); ?>
        </div>
        <div id="opc-checkout-method-summary" class="opc-summary muted opc-hidden"></div>
    </li>
    <?php } ?>

    <li id="opc-billing" class="opc-section <?php if (empty($active)) { $active = 'opc-billing'; echo 'allow active'; } ?>">
        <div class="opc-section-title dsc-wrap">
            <h4>
                <?php echo JText::_( "COM_CITRUSCART_BILLING_INFORMATION" ); ?>
                <a class="opc-change" href="#"><?php echo JText::_( "COM_CITRUSCART_CHANGE" ); ?></a>
            </h4>
        </div>
        <div id="opc-billing-body" class="opc-section-body <?php echo ($active != 'opc-billing') ? 'opc-hidden' : 'opc-open'; ?>">
            <?php $this->setLayout('billing'); echo $this->loadTemplate(); ?>
        </div>
        <div id="opc-billing-summary" class="opc-summary muted opc-hidden"></div>
    </li>

    <?php if (!empty($this->showShipping)) { ?>
    <li id="opc-shipping" class="opc-section <?php if (empty($active)) { $active = 'opc-shipping'; echo 'active'; } ?>">
        <div class="opc-section-title dsc-wrap">
            <h4>
                <?php echo JText::_( "COM_CITRUSCART_SHIP_TO" ); ?>
                <a class="opc-change" href="#"><?php echo JText::_( "COM_CITRUSCART_CHANGE" ); ?></a>
            </h4>
        </div>
        <div id="opc-shipping-body" class="opc-section-body <?php echo ($active != 'opc-shipping') ? 'opc-hidden' : 'opc-open'; ?>">
            <?php $this->setLayout('shipping'); echo $this->loadTemplate(); ?>
        </div>
        <div id="opc-shipping-summary" class="opc-summary muted opc-hidden"></div>
    </li>

    <li id="opc-shipping-method" class="opc-section <?php if (empty($active)) { $active = 'opc-shipping-method'; echo 'active'; } ?>">
        <div class="opc-section-title dsc-wrap">
            <h4>
                <?php echo JText::_( "COM_CITRUSCART_SHIPPING_METHOD" ); ?>
                <a class="opc-change" href="#"><?php echo JText::_( "COM_CITRUSCART_CHANGE" ); ?></a>
            </h4>
        </div>
        <div id="opc-shipping-method-body" class="opc-section-body <?php echo ($active != 'opc-shipping-method') ? 'opc-hidden' : 'opc-open'; ?>">
            <?php //$this->setLayout('shippingmethod'); echo $this->loadTemplate(); ?>
        </div>
        <div id="opc-shipping-method-summary" class="opc-summary muted opc-hidden"></div>
    </li>
    <?php } ?>

    <li id="opc-payment" class="opc-section <?php if (empty($active)) { $active = 'opc-payment'; echo 'active'; } ?>">
        <div class="opc-section-title dsc-wrap">
            <h4>
                <?php echo JText::_( "COM_CITRUSCART_PAYMENT_INFORMATION" ); ?>
                <a class="opc-change" href="#"><?php echo JText::_( "COM_CITRUSCART_CHANGE" ); ?></a>
            </h4>
        </div>
        <div id="opc-payment-body" class="opc-section-body <?php echo ($active != 'opc-payment') ? 'opc-hidden' : 'opc-open'; ?>">
            <?php //$this->setLayout('payment'); echo $this->loadTemplate(); ?>
        </div>
        <div id="opc-payment-summary" class="opc-summary muted opc-hidden"></div>
    </li>

    <li id="opc-review" class="opc-section <?php if (empty($active)) { $active = 'opc-review'; echo 'active'; } ?>">
        <div class="opc-section-title dsc-wrap">
            <h4>
                <?php echo JText::_( "COM_CITRUSCART_ORDER_REVIEW" ); ?>
                <a class="opc-change" href="#"><?php echo JText::_( "COM_CITRUSCART_CHANGE" ); ?></a>
            </h4>
        </div>
        <div id="opc-review-body" class="opc-section-body <?php echo ($active != 'opc-review') ? 'opc-hidden' : 'opc-open'; ?>">
            <?php //$this->setLayout('review'); echo $this->loadTemplate(); ?>
        </div>
        <div id="opc-review-summary" class="opc-summary muted opc-hidden"></div>
        <div id="opc-payment-prepayment" class="opc-hidden"></div>
    </li>
</ol>
