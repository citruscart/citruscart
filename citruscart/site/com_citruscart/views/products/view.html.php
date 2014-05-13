<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartViewBase', 'views._base', array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_citruscart' ) );
Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
Citruscart::load( 'CitruscartHelperCategory', 'helpers.category' );
Citruscart::load( 'CitruscartUrl', 'library.url' );

class CitruscartViewProducts extends CitruscartViewBase
{
    function __construct( $config=array() )
    {
        parent::__construct( $config );

        if (empty($this->helpers)) {
            $this->helpers = array();
        }

        Citruscart::load( "CitruscartHelperProduct", 'helpers.product' );
        $this->helpers['product'] = new CitruscartHelperProduct();
    }

	/**
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	public function getLayoutVars($tpl=null)
	{
		$layout = $this->getLayout();

		switch(strtolower($layout))
		{
			case "view":
				$this->_form( $tpl );
				break;
			case "product_comments":
				$this->_default( $tpl, true );
				break;
			default:
			    $this->_default( $tpl );
				$this->_form($tpl);
				break;
		}
	}

}