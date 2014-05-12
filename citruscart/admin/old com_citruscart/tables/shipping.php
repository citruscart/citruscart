<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
 
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartTable', 'tables._base' );

class CitruscartTableShipping extends CitruscartTable 
{
	public function __construct( $db=null, $tbl_name=null, $tbl_key=null ) 
	{
		if (version_compare(JVERSION,'1.6.0','ge')) {
	        // Joomla! 1.6+ code here
	        $tbl_key 	= 'extension_id';
	        $tbl_suffix = 'extensions';
	    } else {
	        // Joomla! 1.5 code here
	        $tbl_key 	= 'id';
	        $tbl_suffix = 'plugins';
	    }
		
	    $this->set( '_suffix', 'shipping' );
	    
	    if (empty($db)) {
	        $db = JFactory::getDBO();
	    }
	    
		parent::__construct( "#__{$tbl_suffix}", $tbl_key, $db );		
	}
	
	public function getName( $item=null )
	{
	    if (!empty($item) && is_numeric($item)) {
	        $this->load( $item );
	    } elseif (is_object($item) || is_array($item)) {
	        $this->bind($item);
	    }
	     
	    $params = new DSCParameter( $this->params );
	    if ($params->get('label')) {
	        return $params->get('label');
	    }
	     
	    return $this->name;
	}
}
