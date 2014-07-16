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
require_once JPATH_SITE.'/libraries/dioscouri/library/queryelement.php';
/**
 * Query Building Class.
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.6
 */
class DSCQuery extends JObject
{
	/** @var string The query type */
	protected $_type = '';

	/** @var object The select element */
	protected $_select = null;

    /** @var object The delete element */
    protected $_delete = null;

    /** @var object The update element */
    protected $_update = null;

    /** @var object The insert element */
    protected $_insert = null;

	/** @var object The from element */
	protected $_from = null;

	/** @var object The join element */
	protected $_join = null;

    /** @var object The set element */
    protected $_set = null;

	/** @var object The where element */
	protected $_where = null;

	/** @var object The group element */
	protected $_group = null;

	/** @var object The having element */
	protected $_having = null;

	/** @var object The order element */
	protected $_order = null;

	/**
	 * @param	mixed	A string or an array of field names
	 */
	public function select($columns)
	{
		$this->_type = 'select';
		if (is_null($this->_select)) {
			$this->_select = new DSCQueryElement('SELECT', $columns);
		} else {
			$this->_select->append($columns);
		}

		return $this;
	}

    /**
     * @param   mixed   A string or an array of field names
     */
    public function delete()
    {
        $this->_type = 'delete';
        $this->_delete = new DSCQueryElement('DELETE', array(), '');
        return $this;
    }

    /**
     * @param   mixed   A string or array of table names
     */
    public function insert($tables)
    {
        $this->_type = 'insert';
        $this->_insert = new DSCQueryElement('INSERT INTO', $tables);
        return $this;
    }

    /**
     * @param   mixed   A string or array of table names
     */
    public function update($tables)
    {
        $this->_type = 'update';
        $this->_update = new DSCQueryElement('UPDATE', $tables);
        return $this;
    }

	/**
	 * @param	mixed	A string or array of table names
	 */
	public function from($tables)
	{
		if (is_null($this->_from)) {
			$this->_from = new DSCQueryElement('FROM', $tables);
		} else {
			$this->_from->append($tables);
		}

		return $this;
	}

	/**
	 * @param	string
	 * @param	string
	 */
	public function join($type, $conditions)
	{
		if (is_null($this->_join)) {
			$this->_join = array();
		}
		$this->_join[] = new DSCQueryElement(strtoupper($type) . ' JOIN', $conditions);

		return $this;
	}

	/**
	 * @param	string
	 */
	public function &innerJoin($conditions)
	{
		$this->join('INNER', $conditions);

		return $this;
	}

	/**
	 * @param	string
	 */
	public function &outerJoin($conditions)
	{
		$this->join('OUTER', $conditions);

		return $this;
	}

	/**
	 * @param	string
	 */
	public function &leftJoin($conditions)
	{
		$this->join('LEFT', $conditions);

		return $this;
	}

	/**
	 * @param	string
	 */
	public function &rightJoin($conditions)
	{
		$this->join('RIGHT', $conditions);

		return $this;
	}

    /**
     * @param   mixed   A string or array of conditions
     * @param   string
     */
    public function set($conditions, $glue=',')
    {
        if (is_null($this->_set)) {
            $glue = strtoupper($glue);
            $this->_set = new DSCQueryElement('SET', $conditions, "\n\t$glue ");
        } else {
            $this->_set->append($conditions);
        }

        return $this;
    }

	/**
	 * @param	mixed	A string or array of where conditions
	 * @param	string
	 */
	public function where($conditions, $glue='AND')
	{
		if (is_null($this->_where)) {
			$glue = strtoupper($glue);
			$this->_where = new DSCQueryElement('WHERE', $conditions, "\n\t$glue ");
		} else {
			$this->_where->append($conditions);
		}

		return $this;
	}

	/**
	 * @param	mixed	A string or array of ordering columns
	 */
	public function group($columns)
	{
		if (is_null($this->_group)) {
			$this->_group = new DSCQueryElement('GROUP BY', $columns);
		} else {
			$this->_group->append($columns);
		}

		return $this;
	}

	/**
	 * @param	mixed	A string or array of columns
     * @param   string
	 */
	public function having($conditions, $glue='AND')
	{
		if (is_null($this->_having)) {
			$glue = strtoupper($glue);
			$this->_having = new DSCQueryElement('HAVING', $conditions, "\n\t$glue ");
		} else {
			$this->_having->append($conditions);
		}

		return $this;
	}

	/**
	 * @param	mixed	A string or array of ordering columns
	 */
	public function order($columns)
	{
		if (is_null($this->_order)) {
			$this->_order = new DSCQueryElement('ORDER BY', $columns);
		} else {
			$this->_order->append($columns);
		}

		return $this;
	}

	/**
	 * @return	string	The completed query
	 */
	public function __toString()
	{
		$query = '';

		switch ($this->_type)
		{
			case 'select':
				$query .= (string) $this->_select;
				$query .= (string) $this->_from;
				if ($this->_join) {
					// special case for joins
					foreach ($this->_join as $join) {
						$query .= (string) $join;
					}
				}
				if ($this->_where) {
					$query .= (string) $this->_where;
				}
				if ($this->_group) {
					$query .= (string) $this->_group;
				}
				if ($this->_having) {
					$query .= (string) $this->_having;
				}
				if ($this->_order) {
					$query .= (string) $this->_order;
				}
				break;
            case 'delete':
                $query .= (string) $this->_delete;
                $query .= (string) $this->_from;
                if ($this->_where) {
                    $query .= (string) $this->_where;
                }
                break;
            case 'update':
                $query .= (string) $this->_update;
                $query .= (string) $this->_set;
                if ($this->_where) {
                    $query .= (string) $this->_where;
                }
                break;
            case 'insert':
                $query .= (string) $this->_insert;
                $query .= (string) $this->_set;
                if ($this->_where) {
                    $query .= (string) $this->_where;
                }
                break;
		}

		return $query;
	}
}
