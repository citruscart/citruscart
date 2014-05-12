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

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = $this -> form; ?>
<?php $row = $this -> row; ?>
<?php

JHTML::_('script', 'citruscart.js', 'media/com_citruscart/js/'); ?>

<form action="<?php echo JRoute::_( $form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data">

    <div>
        <input type="hidden" name="task" value="" />
    </div>

    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab1" data-toggle="tab"><?php echo JText::_('COM_CITRUSCART_SHOP_INFORMATION'); ?></a>
            </li>
            <li>
                <a href="#tab2" data-toggle="tab"><?php echo JText::_('COM_CITRUSCART_CURRENCY_UNITS_AND_DATE_SETTINGS'); ?></a>
            </li>
            <li>
                <a href="#tab3" data-toggle="tab"><?php echo JText::_('COM_CITRUSCART_FEATURES_SETTINGS'); ?></a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <?php $this->setLayout( 'default_shop' ); echo $this->loadTemplate(); ?>
            </div>

            <div class="tab-pane" id="tab2">
                <?php $this->setLayout( 'default_currency' ); echo $this->loadTemplate(); ?>
            </div>

            <div class="tab-pane" id="tab3">
                <?php $this->setLayout( 'default_features' ); echo $this->loadTemplate(); ?>
            </div>
        </div>

    </div>

</form>
