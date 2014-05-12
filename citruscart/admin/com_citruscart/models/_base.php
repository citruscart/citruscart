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

Citruscart::load( 'CitruscartQuery', 'library.query' );

require_once JPATH_SITE . '/libraries/dioscouri/library/model.php';

require_once JPATH_SITE . '/libraries/dioscouri/library/view/admin.php';

class CitruscartModelBase extends DSCModel
{
    /**
     * Define this in your model to have all the objects in a getList() array be objects of this class
     * @var unknown_type
     */
    protected $_objectClass = null;

    public function __construct($config = array())
    {
    	parent::__construct($config);

        $this->defines = Citruscart::getInstance();

        if (JDEBUG) {
            $this->cache_enabled = false;
        }
    }

    /**
     * Method to get a table object, load it if necessary.
     *
     * @access  public
     * @param   string The table name. Optional.
     * @param   string The class prefix. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  object  The table
     * @since   1.5
     */
    function getTable($name='', $prefix='CitruscartTable', $options = array())
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * Retrieves the data for a paginated list
     * @return array Array of objects containing the data from the database (cached)
     */
    public function getList($refresh = false)
    {
    	JPluginHelper::importPlugin('Citruscart');

		$app = JFactory::getApplication();
        if (empty( $this->_list ) || $refresh)
        {
            $this->_list = parent::getList($refresh);

            $overridden_methods = $this->getOverriddenMethods( get_class($this) );

            if (!(in_array('getList', $overridden_methods)))
            {
               $app->triggerEvent( 'onPrepare'.$this->getTable()->get('_suffix').'List', array( &$this->_list ) );
            }
        }
        return $this->_list;
    }

    /**
     * convert data from local to GMT
     * TODO handle solar and legal time where is present.
     */
    function local_to_GMT_data( $local_data )
    {
        $GMT_data=$local_data ;
        if(!empty($local_data))
        {
            $config = JFactory::getConfig();
            $offset = $config->get('config.offset');
            $offset=0-$offset;
            $date = date_create($local_data);
            date_modify($date,  $offset.' hour');
            $GMT_data= date_format($date, 'Y-m-d H:i:s');
        }
        return $GMT_data;
    }

    /**
     * Any errors set?  If so, check fails
     *
     */
    public function check()
    {
        $errors = $this->getErrors();
        if (!empty($errors))
        {
            foreach ($errors as $key=>$error)
            {
                $error = trim( $error );
                if (empty($error))
                {
                    unset($errors[$key]);
                }
            }

            if (!empty($errors))
            {
                return false;
            }
        }

        return true;
    }

	/**
	* Get the list of items. If needed, loads the attributes for each item
	*
	* @param  boolean  $refresh
	* @param  boolean  $getEav
	* @param  array  $options; keys: include, exclude; includes or excludes eav attributes from loading. Use their alias
	*/
	public function getListRaw($refresh = false, $getEav = true, $options = array())
	{
		$query = $this->getQuery($refresh);
		$list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );

		return $list;
	}

    /**
     * Gets an array of objects from the results of database query.
     * TODO Push this upstream after checking for potential backwards-incompatiblity issues
     *
     * @param   string   $query       The query.
     * @param   integer  $limitstart  Offset.
     * @param   integer  $limit       The number of records.
     *
     * @return  array  An array of results.
     *
     * @since   11.1
     */
    protected function _getList($query, $limitstart = 0, $limit = 0)
    {
        $key = !empty($this->_keyGetList) ? $this->_keyGetList : '';
        $class = !empty($this->_objectClass) ? $this->_objectClass : 'stdClass';

        $this->_db->setQuery($query, $limitstart, $limit);
        $result = $this->_db->loadObjectList( $key, $class );

        return $result;
    }
}
