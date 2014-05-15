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
	//jimport('joomla.application.component.model');
	JHTML::_('behavior.modal');
	$doc = JFactory::getDocument();
	$doc->addStyleSheet(JUri::root().'media/citruscart/css/menu.css');
	$user = JFactory::getUser();
	Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
	$display_credits = Citruscart::getInstance()->get( 'display_credits', '0' );
	$menu = CitruscartMenu::getInstance();


?>

<div class='componentheading'>
	<span><?php echo JText::_('COM_CITRUSCART_MY_ACCOUNT'); ?></span>
</div>
  <div class="naviagtion header">
	<?php
		require_once(JPATH_SITE.'/administrator/components/com_citruscart/helpers/toolbar.php');
	 	$toolbar = new CitruscartToolBar();
	 	$toolbar->renderLinkbar();

	?>
</div>
<table style="width: 100%;">
<tr>
	<td style="width: 70%; vertical-align: top; padding-right: 5px;">

        <h3>
        <?php echo sprintf( JText::_('COM_CITRUSCART_WELCOME_USER'), $user->name ); ?>
        </h3>

        <?php echo JText::_('COM_CITRUSCART_DASHBOARD_TEXT'); ?>

            <table class="adminlist table table-striped table-bordered" style="margin-bottom: 5px;">
            <thead>
            <tr>
                <th colspan="2">
                    <?php echo JText::_('COM_CITRUSCART_ACCOUNT_INFORMATION'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_ORDER_HISTORY'); ?>
                </th>
                <td>
	                <a href="<?php echo JRoute::_("index.php?option=com_citruscart&view=orders"); ?>">
	                    <?php echo JText::_('COM_CITRUSCART_VIEW_ORDERS_PRINT_RECEIPTS'); ?>
	                </a>
                </td>
            </tr>
            <tr>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_PROFILE'); ?>
                </th>
                <td>
                    <a href="<?php echo JRoute::_("index.php?option=com_citruscart&view=accounts"); ?>">
                        <?php echo JText::_('COM_CITRUSCART_MODIFY_ACCOUNT_INFO'); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_ADDRESSES'); ?>
                </th>
                <td>

                    <a class="modal"  rel="{handler: 'iframe', size: {x: 800, y: 500}}"  href="<?php echo JRoute::_("index.php?option=com_citruscart&view=addresses&tmpl=component"); ?>">
                        <?php echo JText::_('COM_CITRUSCART_MANAGE_BILLING_AND_SHIPPING_ADDRESSES'); ?>
                    </a>
                </td>
            </tr>
            <?php if( $display_credits ): ?>
            <tr>
                <th style="width: 100px;">
                    <?php echo JText::_('COM_CITRUSCART_AVAILABLE_STORE_CREDIT'); ?>
                </th>
                <td>
                    <?php echo CitruscartHelperBase::currency( $this->userinfo->credits_total ); ?>
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
            </table>

		<?php
		$modules = JModuleHelper::getModules("citruscart_dashboard_main");
		$document	= JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$attribs 	= array();
		$attribs['style'] = 'xhtml';
		foreach ( @$modules as $mod )
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
			foreach ( @$modules as $mod )
			{
				echo $renderer->render($mod, $attribs);
			}
		}
		?>
	</td>
</tr>
</table>