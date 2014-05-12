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
defined( '_JEXEC' ) or die( 'Restricted access' );

class CitruscartControllerProductAttributeOptions extends CitruscartController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->set('suffix', 'productattributeoptions');
	}

	/**
	 * delete the object and updates the product quantities
	 */
	function delete()
	{
		$app = JFactory::getApplication();
		$this->message = '';
		$this->messagetype = '';
		$error = false;

		$cids = $app->input->getArray('cid', array (0), 'request', 'array');

		// Get the ProductQuantities model
		$qmodel = JModelLegacy::getInstance('ProductQuantities', 'CitruscartModel');
		// Filter the quantities
		$qmodel->setState('filter_attributes', implode(',', $cids));
		$quantities = $qmodel->getList();
		$qtable = $qmodel->getTable();

		// Delete the product quantities
		foreach ($quantities as $q)
		{
			if (!$qtable->delete($q->productquantity_id))
			{
				$this->message .= $qtable->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ($error)
		{
			$this->message = JText::_('COM_CITRUSCART_ERROR') . " - " . $this->message;
		}
			else
		{
			$this->message = JText::_('COM_CITRUSCART_ITEMS_DELETED');
		}

		// delete the option itself
		parent::delete();
	}

	/**
	 * Expected to be called from ajax
	 */
	public function getProductAttributeOptions()
	{
		$app = JFactory::getApplication();
		$attribute_id = $app->input->getInt('attribute_id', 0);
		$name = $app->input->getString('select_name', 'parent');
		$id = $app->input->getString('select_id', '0');

		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		if($attribute_id)
		{
			Citruscart::load('CitruscartSelect', 'library.select');
			$response['msg']  = CitruscartSelect::productattributeoptions($attribute_id, 0, $name."[".$id."]");
		}
		else
		{
			$response['msg']  = '<input type="hidden" name="'.$name."[".$id."]".'" />';
		}

		echo json_encode($response);
	}
}
	
