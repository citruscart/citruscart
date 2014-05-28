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

class DSCTableNested extends DSCTable
{
	/**
	 * Extends to add some fields based on calculations
	 * @see sample/admin/tables/DSCTable#load($oid, $key)
	 */
	function load( $oid=null, $key=null )
	{
		if ($result = parent::load($oid, $key))
		{
			// the number of children this node has
			$this->_children = (int) ($this->rgt - $this->lft - 1) / 2;
			// the width of the node
			$this->_width = (int) $this->rgt - $this->lft + 1;
		}
		return $result;
	}

	/**
	 * This is a generic method for getting a tree.
	 * Will often be overridden by child classes
	 * @return array
	 */
	public function getTree( $parent=null, $enabled='1', $indent=' ' )
	{
		if (intval($enabled) > 0)
		{
			$enabled_query = "
			AND node.enabled = '1'
			AND NOT EXISTS
			(
				SELECT
					*
				FROM
					{$this->_tbl} AS tbl
				WHERE
					tbl.lft < node.lft AND tbl.rgt > node.rgt
					AND tbl.enabled = '0'
				ORDER BY
					tbl.lft ASC
			)
			";
		}

		$key = $this->getKeyName();
		$query = "
			SELECT node.*, COUNT(parent.{$key}) AS level
			FROM {$this->_tbl} AS node,
			{$this->_tbl} AS parent
			WHERE node.lft BETWEEN parent.lft AND parent.rgt
			$enabled_query
			GROUP BY node.{$key}
			ORDER BY node.lft;
		";

		$this->_db->setQuery( $query );
		$return = $this->_db->loadObjectList();
		return $return;
	}

	/**
	 * Find the path to a node
	 * @return array
	 */
	function getPath()
	{
		$return = false;

		$query = "
			SELECT
				tbl.*
			FROM
				{$this->_tbl} AS tbl
			WHERE
				tbl.lft < '{$this->lft}' AND tbl.rgt > '{$this->rgt}'
			ORDER BY
				tbl.lft ASC
		";

		$this->_db->setQuery( $query );
		$return = $this->_db->loadObjectList();

		return $return;
	}

	/**
	 * Get node descendants
	 * @param object
	 * @return array
	 */
	function getDescendants( $enabled='1' )
	{
		$success = false;
		$database = JFactory::getDBO();

		$enabled_query = "";
		if (intval($enabled) > 0)
		{
			$enabled_query = "
			AND tbl.enabled = '1'
			AND NOT EXISTS
			(
				SELECT
					*
				FROM
					{$this->_tbl} AS p
				WHERE
					p.lft < tbl.lft AND p.rgt > tbl.rgt
					AND p.enabled = '0'
				ORDER BY
					p.lft ASC
			)
			";
		}

		if ($hasDescendants = $this->hasDescendants())
		{
			$query = "
				SELECT
					tbl.*
				FROM
					$this->_tbl AS tbl
				WHERE
					tbl.lft BETWEEN '{$this->lft}' AND '{$this->rgt}'
					$enabled_query
				ORDER BY
					tbl.lft ASC
			";

			$database->setQuery( $query );
			$success = $database->loadObjectList();
		}

		return $success;
	}

	/**
	 * Determines whether node has descendants
	 * @param object
	 * @return boolean
	 */
	function hasDescendants()
	{
		$success = '0';
		$descendants = ($this->rgt - $this->lft - 1) / 2;
		if (intval($descendants) > 0)
		{
			return $descendants;
		}
			else
		{
			return $success;
		}
	}

	/**
	 * Store a node
	 * @param object
	 * @return boolean
	 */
	function store($updateNulls=false)
	{
		$key = $this->getKeyName();
		if (intval($this->$key) > 0)
		{
			$store = $this->update();
		}
			else
		{
			$store = $this->insert();
		}

		return $store;
	}

	/**
	 * Inserts a node
	 * @param object
	 * @return boolean
	 */
	function insert()
	{
		$database = $this->_db;

		//LOCK TABLE nested_category WRITE;
		if (!$this->_lock())
		{
			return false;
		}

		//SELECT @myRight := rgt FROM nested_category WHERE name = 'TELEVISIONS';
		unset($parent);
		$parent = clone $this;
		if (empty($this->parent_id))
		{
			$root = $this->getRoot();
			$this->parent_id = $root->id;
		}
		$parent->load( (int) $this->parent_id );

		if ($parent->hasDescendants())
		{
			$rgt = $parent->rgt;

			//UPDATE nested_category SET rgt = rgt + 2 WHERE rgt >= @myRight;
			$query = "
				UPDATE `{$this->_tbl}`
				SET `rgt` = `rgt` + 2
				WHERE `rgt` >= '{$rgt}'
			";
			$database->setQuery( $query );
			if (!$database->query())
			{
				$this->setError( $database->getErrorMsg() );
				$this->_unlock();
				return false;
			}

			//UPDATE nested_category SET lft = lft + 2 WHERE lft > @myRight;
			$query = "
				UPDATE `{$this->_tbl}`
				SET `lft` = `lft` + 2
				WHERE `lft` > '{$rgt}'
			";
			$database->setQuery( $query );

			if (!$database->query())
			{
				$this->setError( $database->getErrorMsg() );
				$this->_unlock();
				return false;
			}

			//INSERT INTO nested_category(name, lft, rgt) VALUES('GAME CONSOLES', @myRight, @myRight + 1);
			$this->lft = $rgt;
			$this->rgt = $rgt + 1;

		}
			else
		{
			//SELECT @myLeft := lft FROM nested_category
			$lft = $parent->lft;

			//UPDATE nested_category SET rgt = rgt + 2 WHERE rgt > @myLeft;
			$query = "
				UPDATE `{$this->_tbl}`
				SET `rgt` = `rgt` + 2
				WHERE `rgt` > '{$lft}'
			";
			$database->setQuery( $query );
			if (!$database->query())
			{
				$this->setError( $database->getErrorMsg() );
				$this->_unlock();
				return false;
			}

			//UPDATE nested_category SET lft = lft + 2 WHERE lft > @myLeft;
			$query = "
				UPDATE `{$this->_tbl}`
				SET `lft` = `lft` + 2
				WHERE `lft` > '{$lft}'
			";
			$database->setQuery( $query );

			if (!$database->query())
			{
				$this->setError( $database->getErrorMsg() );
				$this->_unlock();
				return false;
			}

			//INSERT INTO nested_category(name, lft, rgt) VALUES('FRS', @myLeft + 1, @myLeft + 2);
			$this->lft = $lft + 1;
			$this->rgt = $lft + 2;
		}

		$return = parent::store();

		//UNLOCK TABLES;
		$this->_unlock();

		return $return;
	}

	/**
	 * Updates a node
	 * @param object
	 * @return boolean
	 */
	function update()
	{
		$moving = false;
		// are we moving the node? or just updating its details?
		$node = clone $this;
		$key = $this->getKeyName();
		$node->load( $this->$key );
		if ($node->parent_id != $this->parent_id)
		{
			$moving = true;
		}

		$return = parent::store();
		if ($moving)
		{
			// TODO Check why the shift() method isn't working
			// $this->shift( $this->parent_id );
			$this->rebuildTree();
		}

		return $return;
	}

	/**
	 * Delete a node
	 * @param object
	 * @return boolean
	 */
	function delete( $id=null )
	{
		if (!empty($id))
		{
			$this->load((int)$id);
		}

		$key = $this->getKeyName();
		if (empty($this->$key))
		{
			$this->setError( JText::_( "Invalid Item" ) );
			return false;
		}

	    
        $before = JFactory::getApplication()->triggerEvent( 'onBeforeDelete'.$this->get('_suffix'), array( $this, $id ) );
        if (in_array(false, $before, true))
        {
            return false;
        }

		$database = $this->_db;

		//LOCK TABLE nested_category WRITE;
		if (!$this->_lock())
		{
			return false;
		}

		//SELECT @myLeft := lft, @myRight := rgt, @myWidth := rgt - lft + 1
		//DELETE FROM nested_category WHERE lft BETWEEN @myLeft AND @myRight;
		$query = "
			DELETE
			FROM
				`{$this->_tbl}`
			WHERE
				`lft` BETWEEN '{$this->lft}' AND '{$this->rgt}';
		";
		$database->setQuery( $query );
		if (!$database->query())
		{
			$this->setError( $database->getErrorMsg() );
			$this->_unlock();
			return false;
		}

		//UPDATE nested_category SET rgt = rgt - @myWidth WHERE rgt > @myRight;
		$query = "
			UPDATE
				`{$this->_tbl}`
			SET
				`rgt` = `rgt` - {$this->_width}
			WHERE
				`rgt` > '{$this->rgt}'
		";
		$database->setQuery( $query );
		if (!$database->query())
		{
			$this->setError( $database->getErrorMsg() );
			$this->_unlock();
			return false;
		}

		//UPDATE nested_category SET lft = lft - @myWidth WHERE lft > @myRight;
		$query = "
			UPDATE `{$this->_tbl}`
			SET `lft` = `lft` - {$this->_width}
			WHERE `lft` > '{$this->rgt}'
		";
		$database->setQuery( $query );
		if (!$database->query())
		{
			$this->setError( $database->getErrorMsg() );
			$this->_unlock();
			return false;
		}

		//UNLOCK TABLES;
		$this->_unlock();

		
		JFactory::getApplication()->triggerEvent( 'onAfterDelete'.$this->get('_suffix'), array( $this, $id ) );

		return true;
	}

	/**
	 * Reorders the tree by moving one node either up or down
	 * @see sample/admin/tables/DSCTable#move($change, $where)
	 */
	function move($change, $where='')
	{
		$key = $this->getKeyName();
		if ($change < 0)
		{
			return $this->orderUp( $this->$key );
		}
			else
		{
			return $this->orderDown( $this->$key );
		}
	}

	/**
	 * Enable/Disable a node
	 * @param object
	 * @return boolean
	 */
	function enable($enabled=null )
	{
		if (isset($enabled))
		{
			$this->enabled = $enabled;
		}
			else
		{
			if ($this->enabled == 1)
			{
				$this->enabled = 0;
			}
				else
			{
				$this->enabled = 1;
			}
			$enabled = $this->enabled;
		}

		$return = parent::store();
		return $return;
	}

	/**
	 * Find the path to a node
	 * and if any in path are disabled,
	 * this node is disabled
	 * @return array
	 */
	function isDisabled( $lft='', $rgt='' )
	{
		$return = false;
		if (empty($lft) || empty($rgt))
		{
			$lft = $this->lft;
			$rgt = $this->rgt;
		}

		$query = "
			SELECT
				tbl.*
			FROM
				{$this->_tbl} AS tbl
			WHERE
				tbl.lft < '{$lft}' AND tbl.rgt > '{$rgt}'
				AND tbl.enabled = '0'
			ORDER BY
				tbl.lft ASC
		";

		$this->_db->setQuery( $query );
		echo (string) $query;
		if ( $data = $this->_db->loadResult())
		{
			$return = true;
		}
		return $return;
	}

	/**
	 * Rebuilds the Tree
	 * @param object
	 * @return boolean
	 */
	function rebuildTree( $parent=null, $left=1 )
	{
		$key = $this->getKeyName();
		$database = JFactory::getDBO();

		if ($parent === null)
		{
			$root = $this->getRoot();
			if ($root === false)
			{
				return false;
			}
			$parent = $root->$key;
		}

		// the right value of this node is the left value + 1
		$right = $left + 1;

		// get all children of this node
		$query = "
			SELECT
				tbl.{$key}
			FROM
				{$this->_tbl} AS tbl
			WHERE
				tbl.parent_id = '{$parent}'
			ORDER BY
				tbl.lft ASC
		";
		$database->setQuery( $query );
		$children = $database->loadObjectList();
		for ($i=0; $i<count($children); $i++)
		{
			$child = $children[$i];
			// recursive execution of this function for each
			// child of this node
			// $right is the current right value, which is
			// incremented by the rebuildTree function
			$right = $this->rebuildTree( $child->$key, $right );
		}

		// we've got the left value, and now that we've processed
		// the children of this node we also know the right value
		if (!$this->_lock())
		{
			return false;
		}

		$query = "
			UPDATE `{$this->_tbl}`
			SET
				`rgt` = '{$right}',
				`lft` = '{$left}'
			WHERE
				`{$key}` = '{$parent}'
		";
		$database->setQuery( $query );
		if (!$database->query())
		{
			$this->setError( $database->getErrorMsg() );
			$this->_unlock();
			return false;
		}
		$this->_unlock();

		// return the right value of this node + 1
		$return = $right + 1;
		return $return;
	}

	/**
	 * Gets the root item in the tree
	 * @return boolean if fail, stdClass object if true
	 */
	public function getRoot()
	{
		$return = false;

		if ( in_array( 'isroot', array_keys( $this->getProperties() ) ) )
		{
			$query = "
				SELECT
					*
				FROM
					{$this->_tbl}
				WHERE
					`isroot` = '1'
				AND
					`parent_id` = '0'
			";
			$this->_db->setQuery( $query );
			$result = $this->_db->loadObjectList();
			if ($this->_db->getErrorNum())
			{
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}

			if (count($result) == 1)
			{
				// there is a row identified as root
				$return = $result[0];
			}
				else
			{
				// TODO what to do if there is more than one defined as root?
				$this->setError( JText::_( "Multiple Roots Defined" ) );
			}
		}

		if (!$return)
		{
			$query = "
				SELECT
					*
				FROM
					{$this->_tbl}
				WHERE
					`parent_id` = '0'
			";
			$this->_db->setQuery( $query );
			$result = $this->_db->loadObjectList();
			if ($this->_db->getErrorNum())
			{
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}

			if (count($result) == 1)
			{
				// there is only one record with parent_id = 0
				$return = $result[0];
			}
				else
			{
				$query = "
					SELECT
						*
					FROM
						{$this->_tbl}
					WHERE
						`lft` = '0'
				";
				$this->_db->setQuery( $query );
				$result = $this->_db->loadObjectList();
				if ($this->_db->getErrorNum())
				{
					$this->setError( $this->_db->getErrorMsg() );
					return false;
				}

				if (count($result) == 1)
				{
					// there is only one record with lft = 0
					$return = $result[0];
				}
					else
				{
					$this->setError( JText::_( "Root Not Found By Uniqueness" ) );
					return false;
				}
			}
		}

		return $return;
	}

	/**
	 * Method to move a node one position to the left in the same level.
	 *
	 * @param	integer	Primary key of the node to move.
	 * @return	boolean	True on success.
	 * @since	1.6
	 * @link	http://docs.joomla.org/JTableNested/orderUp
	 */
	public function orderUp($oid=null)
	{
		$key = $this->getKeyName();
		$oid = (is_null($oid)) ? $this->$key : $oid;

		if (!$this->_lock())
		{
			return false;
		}

		$node = clone $this;
		$node->load( $oid );
		if (empty($node->$key))
		{
			$this->setError( JText::_( "Could not load node" ) );
			$this->_unlock();
			return false;
		}

		// Get the left sibling node
		$sibling = clone $this;
		$sibling->load( array( "rgt"=>$node->lft - 1 ) );
		if (empty($sibling->$key))
		{
			$this->setError( JText::_( "Could not load sibling" ) );
			$this->_unlock();
			return false;
		}
	    if ($sibling->parent_id != $node->parent_id || $sibling->$key == $node->$key)
        {
            $this->setError( JText::_( "Node cannot be ordered any higher up" ) );
            $this->_unlock();
            return false;
        }

		// Get the primary keys of node and its children
		$this->_db->setQuery(
			'SELECT `'.$this->_tbl_key.'`' .
			' FROM `'.$this->_tbl.'`' .
			' WHERE `lft` BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt
		);
		$children = $this->_db->loadColumn();

		// Shift left and right values for the node and it's children.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `lft` = `lft` - '.(int) $sibling->_width.', `rgt` = `rgt` - '.(int) $sibling->_width.'' .
			' WHERE `lft` BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt
		);
		$this->_db->query();

		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Shift left and right values for the sibling and it's children
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `lft` = `lft` + '.(int) $node->_width.', `rgt` = `rgt` + '.(int) $node->_width .
			' WHERE `lft` BETWEEN '.(int) $sibling->lft.' AND '.(int) $sibling->rgt .
			' AND `'.$this->_tbl_key.'` NOT IN ('.implode(',', $children).')'
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Unlock the table for writing.
		$this->_unlock();

		return true;
	}

	/**
	 * Method to move a node one position to the right in the same level.
	 *
	 * @param	integer	Primary key of the node to move.
	 * @return	boolean	True on success.
	 * @since	1.6
	 * @link	http://docs.joomla.org/JTableNested/orderDown
	 */
	public function orderDown($oid)
	{
		$key = $this->getKeyName();
		$oid = (is_null($oid)) ? $this->$key : $oid;

		if (!$this->_lock())
		{
			return false;
		}

		$node = clone $this;
		$node->load( $oid );
		if (empty($node->$key))
		{
			$this->_unlock();
			return false;
		}

		// Get the left sibling node.
		$sibling = clone $this;
		$sibling->load( array( "lft"=>$node->rgt + 1 ) );
		if (empty($sibling->$key))
		{
			// if one doesn't exist, #fail
			$this->_unlock();
			return false;
		}
	    if ($sibling->parent_id != $node->parent_id || $sibling->$key == $node->$key)
        {
            $this->setError( JText::_( "Node cannot be ordered any lower" ) );
            $this->_unlock();
            return false;
        }

		// Get the primary keys of child nodes.
		$this->_db->setQuery(
			'SELECT `'.$this->_tbl_key.'`' .
			' FROM `'.$this->_tbl.'`' .
			' WHERE `lft` BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt
		);
		$children = $this->_db->loadColumn();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Shift left and right values for the node and it's children.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `lft` = `lft` + '.(int) $sibling->_width.', `rgt` = `rgt` + '.(int) $sibling->_width.'' .
			' WHERE `lft` BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Shift left and right values for the sibling and it's children.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `lft` = `lft` - '.(int) $node->_width.', `rgt` = `rgt` - '.(int) $node->_width .
			' WHERE `lft` BETWEEN '.(int) $sibling->lft.' AND '.(int) $sibling->rgt .
			' AND `'.$this->_tbl_key.'` NOT IN ('.implode(',', $children).')'
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Unlock the table for writing.
		$this->_unlock();

		return true;
	}

    /**
     * Moves the node with ID $nodeId as child to the node with ID $targetParentId.
     *
     * @param string $targetParentId
     * @param string $nodeId
     */
    public function shift( $targetParentId, $oid=null )
    {
    	$database = $this->_db;
    	$key = $this->getKeyName();
		$oid = (is_null($oid)) ? $this->$key : $oid;

		if (!$this->_lock())
		{
			return false;
		}

		$node = clone $this;
		$node->load( $oid );
		if (empty($node->$key))
		{
			$this->_unlock();
			return false;
		}

		// Get the primary keys of child nodes.
		$this->_db->setQuery(
			'SELECT `'.$this->_tbl_key.'`' .
			' FROM `'.$this->_tbl.'`' .
			' WHERE `lft` BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt
		);
		$children = $this->_db->loadColumn();

        // Update parent ID for the node
        //   UPDATE indexTable
        //   SET parent_id = $targetParentId
        //   WHERE id = $nodeId
        $node->parent_id = $targetParentId;

        // Update the nested values to account for the moved subtree (delete part)
        $this->updateNestedValuesForSubtreeDeletion( $node->rgt, $node->_width );

        // Fetch node information
    	$target = clone $this;
		$target->load( $targetParentId );
		if (empty($target->$key))
		{
			$this->_unlock();
			return false;
		}

        // Update the nested values to account for the moved subtree (addition part)
        $this->updateNestedValuesForSubtreeAddition( $target->rgt, $node->_width, $children );

        // Update nodes in moved subtree
        $adjust = $target->rgt - $node->lft;

        // UPDATE indexTable
        // SET rgt = rgt + $adjust
        // WHERE id in $nodeIds
        $query = "
			UPDATE
        		{$this->_tbl} AS tbl
			SET
				tbl.rgt = tbl.rgt + {$adjust}
			WHERE
				tbl.{$this->getKeyName()} IN ('".implode( "', '", $children )."')
        ";
		$database->setQuery( $query );
		$database->query();

        // UPDATE indexTable
        // SET lft = lft + $adjust
        // WHERE id in $nodeIds
        $query = "
			UPDATE
        		{$this->_tbl} AS tbl
			SET
				tbl.lft = tbl.lft + {$adjust}
			WHERE
				tbl.{$this->getKeyName()} IN ('".implode( "', '", $children )."')
        ";
		$database->setQuery( $query );
		$database->query();

		// unlock the table for writing
		$this->_unlock();
    }

    /**
     * Updates the left and right values of the nodes that are added while
     * adding a whole subtree as child of a node.
     *
     * The method does not update nodes where the IDs are in the $excludedIds
     * list.
     *
     * @param int $right
     * @param int $width
     * @param array(string) $excludedIds
     */
    protected function updateNestedValuesForSubtreeAddition( $right, $width, $excludedIds = array() )
    {
        $database = JFactory::getDBO();

        // Move all the right values + $width for nodes where the the right value >=
        // the parent right value:
        //   UPDATE indexTable
        //   SET rgt = rgt + $width
        //   WHERE rgt >= $right
        $query = "
			UPDATE
        		{$this->_tbl} AS tbl
			SET
				tbl.rgt = tbl.rgt + {$width}
			WHERE
				tbl.rgt >= {$right}
        ";
		if ( count( $excludedIds ) )
        {
            $query .= "AND tbl.{$this->getKeyName()} NOT IN ('".implode( "', '", $excludedIds )."')";
        }
		$database->setQuery( $query );
		$database->query();

        // Move all the left values + $width for nodes where the the right value >=
        // the parent left value
        //   UPDATE indexTable
        //   SET lft = lft + $width
        //   WHERE lft >= $right
		$query = "
			UPDATE
        		{$this->_tbl} AS tbl
			SET
				tbl.lft = tbl.lft + {$width}
			WHERE
				tbl.lft >= {$right}
        ";
		if ( count( $excludedIds ) )
        {
            $query .= "AND tbl.{$this->getKeyName()} NOT IN ('".implode( "', '", $excludedIds )."')";
        }
		$database->setQuery( $query );
		$database->query();
    }

    /**
     * Updates the left and right values in case a subtree is deleted.
     *
     * @param int $right
     * @param int $width
     */
    protected function updateNestedValuesForSubtreeDeletion( $right, $width )
    {
		$database = JFactory::getDBO();

        // Move all the right values + $width for nodes where the the right
        // value > the parent right value
        //   UPDATE indexTable
        //   SET rgt = rgt - $width
        //   WHERE rgt > $right
        $query = "
			UPDATE
        		{$this->_tbl} AS tbl
			SET
				tbl.rgt = tbl.rgt - {$width}
			WHERE
				tbl.rgt > {$right}
        ";
		$database->setQuery( $query );
		$database->query();

        // Move all the right values + $width for nodes where the the left
        // value > the parent right value
        //   UPDATE indexTable
        //   SET lft = lft - $width
        //   WHERE lft > $right
        $query = "
			UPDATE
        		{$this->_tbl} AS tbl
			SET
				tbl.lft = tbl.lft - {$width}
			WHERE
				tbl.lft > {$right}
        ";
		$database->setQuery( $query );
		$database->query();
    }

    /**
     * Compacts the ordering sequence of the selected records
     *
     * @access public
     * @param string Additional where query to limit ordering to a particular subset of records
     */
    function reorder( $parent=null, $where='' )
    {
        if (!in_array( 'ordering', array_keys($this->getProperties() ) ))
        {
            $this->setError( get_class( $this ).' does not support ordering');
            return false;
        }

        $key = $this->getKeyName();
        $database = JFactory::getDBO();

        if ($parent === null)
        {
            $root = $this->getRoot();
            if ($root === false)
            {
                return false;
            }
            $parent = $root->$key;
        }

        // get all children of this node
        $query = "
            SELECT
                tbl.{$key}
            FROM
                {$this->_tbl} AS tbl
            WHERE
                tbl.parent_id = '{$parent}'
            ORDER BY
                tbl.lft ASC
        ";
        $database->setQuery( $query );
        $children = $database->loadObjectList();
        for ($i=0; $i<count($children); $i++)
        {
            $child = $children[$i];
            // recursive execution of this function for each
            // child of this node
            $this->reorder( $child->$key );
        }

        $k = $this->_tbl_key;

        $query = 'SELECT '.$this->_tbl_key.', ordering'
        . ' FROM '. $this->_tbl
        . ' WHERE ordering >= 0' . ( $where ? ' AND '. $where : '' )
        . " AND parent_id = '{$parent}' "
        . ' ORDER BY ordering, lft ASC'
        ;
        $this->_db->setQuery( $query );
        if (!($orders = $this->_db->loadObjectList()))
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // compact the ordering numbers
        for ($i=0, $n=count( $orders ); $i < $n; $i++)
        {
            if ($orders[$i]->ordering >= 0)
            {
                if ($orders[$i]->ordering != $i+1)
                {
                    $orders[$i]->ordering = $i+1;
                    $query = 'UPDATE '.$this->_tbl
                    . ' SET ordering = '. (int) $orders[$i]->ordering
                    . ' WHERE '. $k .' = '. $this->_db->q($orders[$i]->$k)
                    ;
                    $this->_db->setQuery( $query);
                    if (!$this->_db->query())
                    {
                        $this->setError($this->_db->getErrorMsg());
                    }
                }
            }
        }

        return true;
    }
}
