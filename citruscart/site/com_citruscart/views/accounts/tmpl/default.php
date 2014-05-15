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

defined('_JEXEC') or die('Restricted access'); ?>
<?php
JHTML::_('behavior.modal');
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root().'media/citruscart/css/menu.css');
$doc->addScript(JUri::root().'media/citruscart/js/citruscart.js');
?>
<?php require_once(JPATH_SITE.'/components/com_citruscart/views/sitemenu.php');?>

<table style="width: 100%;">
<tr>
	<td style="width: 70%; max-width: 70%; vertical-align: top; padding-right: 5px;">

            <table class="adminlist" style="margin-bottom: 5px;">
            <thead>
            <tr>
                <th colspan="3">
                    <?php echo JText::_('COM_CITRUSCART_PROFILE_INFORMATION'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_BASICS'); ?>
                </th>
                <td>
                    <?php
                    Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
                    $userinfo = CitruscartHelperUser::getBasicInfo( JFactory::getUser()->id );
                    if (empty($userinfo->user_id))
                    {
                    	echo JText::_('COM_CITRUSCART_PLEASE_CLICK_EDIT_TO_DEFINE_YOUR_BASIC_PROFILE_INFORMATION');
                    }
                    else
                    {
                        echo $userinfo->first_name." ".$userinfo->last_name."<br/>";
                    }
                    ?>
                </td>
                <td>
                    <a href="<?php echo JRoute::_("index.php?option=com_citruscart&view=accounts&task=edit"); ?>">
                        <?php echo JText::_('COM_CITRUSCART_EDIT'); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_EMAIL'); ?>
                </th>
                <td>
                    <?php echo JFactory::getUser()->email; ?>
                </td>
                <td>
                    <a href="<?php echo JRoute::_( $this->url_profile ); ?>">
                        <?php echo JText::_('COM_CITRUSCART_EDIT'); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_PASSWORD'); ?>
                </th>
                <td>
                    **********
                </td>
                <td>
                    <a href="<?php echo JRoute::_( $this->url_profile ); ?>">
                        <?php echo JText::_('COM_CITRUSCART_EDIT'); ?>
                    </a>
                </td>
            </tr>
            <?php if ( Citruscart::getInstance()->get( 'display_subnum', 0 ) ) : ?>
            <tr>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_SUB_NUM'); ?>
                </th>
                <td colspan="2">
		            	<?php Citruscart::load( 'CitruscartHelperSubscription', 'helpers.subscription' ); ?>
    	        		<?php echo CitruscartHelperSubscription::displaySubNum( $userinfo->sub_number ); ?>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_PRIMARY_SHIPPING_ADDRESS'); ?>
                </th>
                <td>
                    <?php
                    Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
                    if ($address = CitruscartHelperUser::getPrimaryAddress( JFactory::getUser()->id, 'shipping' ))
                    {
                        echo $address->title . " ". $address->first_name . " ". $address->last_name . "<br>";
			            echo $address->company . "<br>";
			            echo $address->address_1 . " " . $address->address_2 . "<br>";
			            echo $address->city . ", " . $address->zone_name .", " . $address->postal_code . "<br>";
			            echo $address->country_name . "<br>";
                    }
                    else
                    {
                        echo JText::_('COM_CITRUSCART_NONE_SELECTED');
                    }
                    ?>
                </td>
                <td>
                    <a href="<?php echo JRoute::_("index.php?option=com_citruscart&view=addresses"); ?>">
                        <?php echo JText::_('COM_CITRUSCART_EDIT'); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_PRIMARY_BILLING_ADDRESS'); ?>
                </th>
                <td>
                    <?php
                    if ($address = CitruscartHelperUser::getPrimaryAddress( JFactory::getUser()->id, 'billing' ))
                    {
                        echo $address->title . " ". $address->first_name . " ". $address->last_name . "<br>";
                        echo $address->company . "<br>";
                        echo $address->address_1 . " " . $address->address_2 . "<br>";
                        echo $address->city . ", " . $address->zone_name .", " . $address->postal_code . "<br>";
                        echo $address->country_name . "<br>";
                    }
                    else
                    {
                        echo JText::_('COM_CITRUSCART_NONE_SELECTED');
                    }
                    ?>
                </td>
                <td>
                    <a href="<?php echo JRoute::_("index.php?option=com_citruscart&view=addresses"); ?>">
                        <?php echo JText::_('COM_CITRUSCART_EDIT'); ?>
                    </a>
                </td>
            </tr>
            </tbody>
            </table>

		<?php
		$modules = JModuleHelper::getModules("citruscart_dashboard_main");
		$document	= JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$attribs 	= array();
		$attribs['style'] = 'xhtml';
		foreach ( $modules as $mod )
		{
			echo $renderer->render($mod, $attribs);
		}
		?>

		<?php
		$modules = JModuleHelper::getModules("citruscart_dashboard_right");
		if ($modules)
		{
            ?>
            </td>
            <td style="vertical-align: top; width: 30%; min-width: 30%; padding-left: 5px;">
            <?php

			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$attribs 	= array();
			$attribs['style'] = 'xhtml';
			foreach ( $modules as $mod )
			{
				echo $renderer->render($mod, $attribs);
			}
		}
		?>
	</td>
</tr>
</table>
