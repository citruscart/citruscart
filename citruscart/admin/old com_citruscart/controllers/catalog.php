<?php
/**
 * @version	1.5
 * @package	com_citruscart
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class CitruscartControllerCatalog extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		$app = JFactory::getApplication();
        $app->redirect("index.php?option=com_citruscart&view=products");
		return;

	}


}
