<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access'); ?>
<?php
$row = $this->row;

?>

	<h3>
	    <?php echo $row->name; ?>
	</h3>

	<?php
		JPluginHelper::importPlugin('Citruscart');
     	$results = JFactory::getApplication()->triggerEvent( 'onGetShippingView1', array( $row ) );

        for ($i=0; $i<count($results); $i++)
        {
            $result = $results[$i];
            echo $result;
        }
