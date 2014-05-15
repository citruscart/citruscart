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

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class CitruscartControllerTools extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'tools');
	}

    /**
     * Sets the model's state
     *
     * @return array()
     */
    function _setModelState()
    {
        $state = parent::_setModelState();
        $app = JFactory::getApplication();
        $model = $this->getModel( $this->get('suffix') );
        $ns = $this->getNamespace();

        $state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
        $state['filter_name']         = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');

        foreach ($state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }

    /**
     * Displays item
     * @return void
     */
    function view($cachable = false, $urlparams = false)
    {
        $model = $this->getModel( $this->get('suffix') );
        $model->getId();
        $row = $model->getItem();

        if (version_compare(JVERSION, '1.6.0', 'ge')) {
			// Joomla! 1.6+ code here
			if (empty($row -> enabled)) {
				$table = $model -> getTable();
				$table -> load($row -> id);
				$table -> enabled = 1;
				if ($table -> save()) {
					$redirect = "index.php?option=com_citruscart&view=" . $this -> get('suffix') . "&task=view&id=" . $model -> getId();
					$redirect = JRoute::_($redirect, false);
					$this -> setRedirect($redirect);
					return;
				}
			}
		} else {
			// Joomla! 1.5 code here
			if (empty($row -> published)) {
				$table = $model -> getTable();
				$table -> load($row -> id);
				$table -> published = 1;
				if ($table -> save()) {
					$redirect = "index.php?option=com_citruscart&view=" . $this -> get('suffix') . "&task=view&id=" . $model -> getId();
					$redirect = JRoute::_($redirect, false);
					$this -> setRedirect($redirect);
					return;
				}
			}
		}

        parent::view($cachable, $urlparams);
    }
}

