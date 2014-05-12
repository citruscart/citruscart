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
defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'citruscart.js', 'media/citruscart/js/'); ?>
<?php $form = $this->form; ?>
<?php $row = $this->row; ?>

<p>
<?php
	echo $row->title . " ". $row->first_name . " ". $row->last_name . "<br>";
	if( strlen( $row->company ) )
		echo $row->company . "<br>";
	echo $row->address_1 . " " . $row->address_2 . "<br>";
	echo $row->city . ", " . $row->zone_name ." " . $row->postal_code . "<br>";
	echo $row->country_name . "<br>";
	if( strlen( $row->tax_number ) )
		echo $row->tax_number . "<br>";
?>
</p>
