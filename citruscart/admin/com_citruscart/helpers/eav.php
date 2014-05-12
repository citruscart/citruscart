<?php
/*------------------------------------------------------------------------
 # com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

if ( !class_exists('Citruscart') ) 
    JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

Citruscart::load( "CitruscartHelperBase", 'helpers._base' );

class CitruscartHelperEav extends CitruscartHelperBase 
{
    /**
     * Gets an Attribute type based on its alias 
     * 
     * @param $alias
     * @return unknown_type
     */
    function getType( $alias )
    {
        static $sets;
        if (!is_array($sets)) { $sets = array(); }
        
        if (!isset($sets[$alias]))
        {
            JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
            $table = JTable::getInstance('EavAttributes', 'CitruscartTable');
            $table->load(array('eavattribute_alias' => $alias));
            switch( $table->eavattribute_type )
            {
            	case 'bool' :
            	case 'hidden':
            			$type ='varchar';
            			break;
            	default:
            			$type = $table->eavattribute_type;
            			break;
            }
            $sets[$alias] = $type;            
        }
        return $sets[$alias];
    }
    
	/**
	 * Get the Eav Attributes for a particular entity
	 * @param unknown_type $entity
	 * @param unknown_type $id
	 * @param boolean $only_enabled
	 */
   public static function getAttributes( $entity, $id, $only_enabled = false, $editable_by = '' )
    {
        // $sets[$entity][$id]
        static $sets;
        if (!is_array($sets)) { $sets = array(); }
		
        if( is_array( $editable_by ) )
        	$editable_by = implode( ',', $editable_by );
        else
	        if( !strlen( $editable_by ) )
  	      	$editable_by = '-1';
        
        if (!isset( $sets[$entity][$id][$editable_by] ) )
        {
            DSCModel::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
            $model = DSCModel::getInstance('EavAttributes', 'CitruscartModel');
            $model->setState('filter_entitytype', $entity);
            $model->setState('filter_entityid', $id);
            $model->setState('filter_enabled', '1');
            if( $editable_by != '-1' )
            	$model->setState( 'filter_editable',$editable_by );
            	
            $sets[$entity][$id][$editable_by] = $model->getList();
        }
    	
        // Let the plugins change the list of custom fields
        
        JFactory::getApplication()->triggerEvent('onAfterGetCustomFields', array( &$sets[$entity][$id][$editable_by], $entity, $id ) );
        
    	return $sets[$entity][$id][$editable_by];
    }
    
    /**
     * Get the value of an attribute
     * @param EavAttribute $eav
     * @param string $entity_type
     * @param string $entity_id
     * @param bool $no_post - only value from db will be used
     * @param bool $cache_values - If the values should be cached in the static array
     */
    public static function getAttributeValue($eav, $entity_type, $entity_id, $no_post = false, $cache_values = true )
    {
    	// $sets[$eav->eavattribute_type][$eav->eavattribute_id][$entity_type][$entity_id]
        static $sets;
        
        /* Get the application */
        $app = JFactory::getApplication();
               
        if (!is_array($sets)) { $sets = array(); }
        		
        if (!isset($sets[$eav->eavattribute_type][$eav->eavattribute_id][$entity_type][$entity_id]))
        {
        	Citruscart::load('CitruscartTableEavValues', 'tables.eavvalues');
        	
            // get the value table
            $table = JTable::getInstance('EavValues', 'CitruscartTable');
            // set the type based on the attribute
            $table->setType($eav->eavattribute_type);
            // load the value based on the entity id
            $keynames = array();
            $keynames['eavattribute_id'] = $eav->eavattribute_id; 
            $keynames['eaventity_id'] = $entity_id;
            $keynames['eaventity_type'] = $entity_type;
            
            $loaded = $table->load($keynames);
            if($loaded)
            {
                // Fetch the value from the value tables
            	$value = $table->eavvalue_value;
            }
            else
            {
            	if( !$no_post ) // we allowed using post variables
            	{            	
            		if( $table->getType() == 'text' )
            			
            			$value = $app->input->get($eav->eavattribute_alias, null, 'default','string', JREQUEST_ALLOWHTML);
            			//$value = JRequest::getVar( $eav->eavattribute_alias, null, 'default','string', JREQUEST_ALLOWHTML );	
            		else
						{
						$value = $app->input->get($eav->eavattribute_alias, null, 'POST');
						//$value = JRequest::getVar($eav->eavattribute_alias, null, 'POST');
						}
            	}
							else
							{
								$value = null;
							}
            }
            if( $value !== null && $cache_values )
	            $sets[$eav->eavattribute_type][$eav->eavattribute_id][$entity_type][$entity_id] = $value;
	            
        }
				
				if( $cache_values )
				{
					if( isset( $sets[$eav->eavattribute_type][$eav->eavattribute_id][$entity_type][$entity_id] ) )
						return $sets[$eav->eavattribute_type][$eav->eavattribute_id][$entity_type][$entity_id];
					else
					{
						if($eav->eavattribute_type == 'datetime' )
						{
							return JFactory::getDbo()->getNullDate();
						}
						else
							return null;
					}
				}
	      else
	      {
	      	if ( isset( $value ) )
	      		return $value;
	      	else
	      	{
						if($eav->eavattribute_type == 'datetime' )
						{
							return JFactory::getDbo()->getNullDate();
						}
						else
							return null;
	      	}
	      }
    }
    
    /**
     * Show the correct edit field based on the eav type
     * @param EavAttribute $eav
     * @param unknown_type $value
     */
    public static function editForm($eav, $value = null)
    {
    	// Type of the field
    	switch($eav->eavattribute_type)
    	{
    		case "bool":
    			Citruscart::load('CitruscartSelect', 'library.select');
    			return CitruscartSelect::booleans($value, $eav->eavattribute_alias, $attribs = array('class' => 'inputbox cf_'.$eav->eavattribute_alias, 'size' => '1'), $idtag = null, $allowAny = false, $title='Select State', $yes = 'Yes', $no = 'No' );
    			break;
    		case "datetime":
    		    $format = !empty($eav->eavattribute_format_strftime) ? $eav->eavattribute_format_strftime : '%Y-%m-%d %H:%M:%S';
    			return JHTML::calendar( $value, $eav->eavattribute_alias, "eavattribute_alias", $format, array( 'size'=>24, 'class'=>'input-medium' ) );
    			break;
    		case "text":
    			$editor = JFactory::getEditor();
    			return $editor->display($eav->eavattribute_alias, $value, '300', '200', '50', '20');
    			break;
    		case "hidden":
    			return '<input type="hidden" name="'.$eav->eavattribute_alias.'" id="'.$eav->eavattribute_alias.'" value="'.$value.'"/>';
    			break;
    		case "decimal":
    		case "int":
    		    return '<input type="text" name="'.$eav->eavattribute_alias.'" id="'.$eav->eavattribute_alias.'" value="'.$value.'" class="input-mini cf_'.$eav->eavattribute_alias.'"/>';
    		    break;
    		case "varchar":
    		default:
    			return '<input type="text" name="'.$eav->eavattribute_alias.'" id="'.$eav->eavattribute_alias.'" value="'.$value.'" class="input-medium cf_'.$eav->eavattribute_alias.'"/>';
    			break;
    	}
    	
    	return '';
    }
    
    /**
     * Show the field based on the eav type
     * @param EavAttribute $eav
     * @param unknown_type $value
     */
    public static function showValue($eav, $value = null)
    {
    	// Type of the field
    	switch($eav->eavattribute_type)
    	{
    		case "bool":
    			if($value)
    			{
    				echo JText::_('COM_CITRUSCART_YES');
    			}
    			else
    			{
    				echo JText::_('COM_CITRUSCART_NO');
    			}
    			break;
    		case "datetime":
    		    $format = !empty($eav->eavattribute_format_date) ? $eav->eavattribute_format_date : 'Y-m-d H:i:s';
    		    $datetime = date('Y-m-d H:i:s', strtotime( $value ));
    			return JHTML::date($datetime, $format);
    			break;
    		case "text":
    			
    			$item = new JObject();
		        $item->text = $value;  
		        $item->params = array();
		        if( Citruscart::getInstance()->get( 'eavtext_content_plugin', 1 ) )
		        {
		        	if( $eav->editable_by == 1 )
		        	{
			        	JPluginHelper::importPlugin('content'); 
			        	JFactory::getApplication()->triggerEvent('onPrepareContent', array (& $item, & $item->params, 0));
		        	}
		        }
		        else // trigger the event on all fields
		        {
		        	JPluginHelper::importPlugin('content'); 
		        	JFactory::getApplication()->triggerEvent('onPrepareContent', array (& $item, & $item->params, 0));
		        }
		        return $value;
    		case "decimal":
    			if( Citruscart::getInstance()->get( 'eavinteger_use_thousand_separator', 0 ) )
    				return self::number( $value );
    			else
	    			return self::number( $value, array( 'thousands' => '' ) );
    		case "int":	
    			if( Citruscart::getInstance()->get( 'eavinteger_use_thousand_separator', 0 ) )
	    			return self::number( $value, array( 'num_decimals' => 0 ) );
    			else
    				return self::number( $value, array( 'thousands' => '', 'num_decimals' => 0 ) );
    		case "hidden":
    		case "varchar":
    			default:
    			return $value;
    			break;
    	}
    	
    	return '';
    }
    
    /**
     * Show the edit form or the field value based on the eav status
     * @param EavAttribute $eav
     * @param unknown_type $value
     */
    public static function showField($eav, $value = null)
    {
    	$isAdmin = DSCAcl::isAdmin();
    	
    	switch($eav->editable_by)
    	{
    		// No one
    		case "0":
    			return self::showValue($eav, $value);
    			break;
    		// Admin
    		case "1":
    			if($isAdmin)
    			{
    				return self::editForm($eav, $value);
    			}
    			else
    			{
    				return self::showValue($eav, $value);
    			}
    			break;
    		case "2":
    		default:
    			return self::editForm($eav, $value);
    			break;
    	}	
    	
    }

    /**
     * This method removes all eav values from an entity with a specified ID
     * 
     * @params $entity_type 	Type of the entity
     * @params $entity-id			Entity ID
     */
    public static function deleteEavValuesFromEntity( $entity_type, $entity_id, $entity_type_mirror = null, $entity_id_mirror = null )
    {
        if( !$entity_type_mirror )
            $entity_type_mirror = $entity_type;
        if( !$entity_id_mirror )
            $entity_id_mirror = $entity_id;

        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
        $tbl_eav = JTable::getInstance( 'Eavvalues', 'CitruscartTable' );
        $eavs = CitruscartHelperEav::getAttributes( $entity_type, $entity_id ); // get list of EAV fields
        for( $i = 0, $c = count( $eavs ); $i < $c; $i++ )
        {
            $tbl_eav->setType( $eavs[$i]->eavattribute_type );
            $tbl_eav->load( array( 'eaventity_type' => $entity_type_mirror, 'eaventity_id' => $entity_id_mirror, 'eavattribute_id' => $eavs[$i]->eavattribute_id ) );
            $tbl_eav->delete();
        }
    }
}
