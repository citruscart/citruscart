<?php defined('_JEXEC') or die('Restricted access'); ?>
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
?>