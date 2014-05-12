<?php
/**
 * @version    1.5
 * @package    Citruscart
 * @author     Dioscouri Design
 * @link     http://www.dioscouri.com
 * @copyright Copyright (C) 2009 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
Citruscart::load( 'CitruscartQuery', 'library.query' );

class modCitruscartRecentOrdersHelper extends CitruscartHelperBase
{
	var $_orders = null;
	
	var $_params = null;
	
	/**
	 * Constructor to set the object's params
	 * 
	 * @param $params
	 * @return unknown_type
	 */
	function __construct( $params )
	{
        parent::__construct();
        $this->_params = $params;	
	}
	
	/**
	 * Gets the sales statistics object, 
	 * creating it if it doesn't exist
	 * 
	 * @return unknown_type
	 */
	function getOrders()
	{
		if (empty($this->_orders))
		{
			$this->_orders = $this->_orders();
		}
		return $this->_orders;
	}
	
    /**
     * _lastfive function.
     * 
     * @access private
     * @return void
     */
    function _orders()
    {
        jimport( 'joomla.application.component.model' );
    	JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'Orders', 'CitruscartModel' );
        $model->setState( 'order', 'tbl.created_date' );
        $model->setState( 'direction', 'DESC' );
        $model->setState( 'limit', $this->_params->get('num_orders', '5') );
        $model->setState( 'limitstart', '0' );
        
        $csv = Citruscart::getInstance()->get('orderstates_csv', '2, 3, 5, 17');
        $array = explode(',', $csv);
        $this->_statesCSV = "'".implode("','", $array)."'";
        // set query for orderstate range
        $ordersQuery = $model->getQuery();
        $ordersQuery->where("tbl.order_state_id IN (".$this->_statesCSV.")");
        $model->setQuery($ordersQuery);
            
        $return = $model->getList();
        return $return;
    }
}