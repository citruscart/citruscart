<?php
/**
 * @version	1.5
 * @package	Citruscart
 * @user 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class CitruscartControllerElementUser extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'elementuser');
	}

    /**
     * Sets the model's default state based on values in the request
     *
     * @return array()
     */
    function _setModelState()
    {
    	$state = parent::_setModelState();
        $model = $this->getModel( $this->get('suffix') );
        $ns = $this->getNamespace();

        $state = array();

        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }

    /**
     * (non-PHPdoc)
     * @see Citruscart/Citruscart/site/CitruscartController::display()
     */
    function display($cachable=false, $urlparams = false)
    {
    	$app = JFactory::getApplication();
        $this->hidefooter = true;

        $object = $app->input->get('object');
        //$object = JRequest::getVar('object');
        $view = $this->getView( $this->get('suffix'), 'html' );
        $view->assign( 'object', $object );
				$view->setTask(true);
        parent::display( $cachable, $urlparams );
    }
}

