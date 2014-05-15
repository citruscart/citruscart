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
if (version_compare(JVERSION,'1.6.0','ge'))
{
    // Joomla! 1.6+ code here
    echo $this->loadTemplate('login_16');
}
else
{
    // Joomla! 1.5 code here
    echo $this->loadTemplate('login_15');
}
