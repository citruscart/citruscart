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
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.Dioscouri.com.
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartToolPlugin', 'library.plugins.tool' );

class plgCitruscartTool_InstallSampleData extends CitruscartToolPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
    var $_element   = 'tool_installsampledata';

    var $_uploaded_file		= '';
    var $_uploaded_filename		= '';

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$language = JFactory::getLanguage();
		$language -> load('plg_citruscart_'.$this->_element, JPATH_ADMINISTRATOR, 'en-GB', true);
		$language -> load('plg_citruscart_'.$this->_element, JPATH_ADMINISTRATOR, null, true);
	}

    /**
     * Overriding
     *
     * @param $options
     * @return unknown_type
     */
    function onGetToolView( $row )
    {
        if (!$this->_isMe($row))
        {
            return null;
        }

        // go to a "process suffix" method
        // which will first validate data submitted,
        // and if OK, will return the html?
        $suffix = $this->_getTokenSuffix();
        $html = $this->_processSuffix( $suffix );

        return $html;
    }

    /**
     * Validates the data submitted based on the suffix provided
     *
     * @param $suffix
     * @return html
     */
    function _processSuffix( $suffix='' )
    {
        $html = "";
        static $checkdb=true;;

        //check if Citruscart tables have data and show warning
        if(!$checkdb = $this->_checkCitruscartDb())
        {
        	JError::raiseNotice('_verifyDB', JText::_('COM_CITRUSCART_CITRUSCART_TABLE_NOT_EMPTY'));
        }

    	$state = $this->_getState();
        switch($suffix)
        {
            case"2":
            	$verify = true;
            	if($state->install_default == '0' || empty($state->install_default))
            	{
            		$verify = $this->_verifyFile();
            	}

	            if (!$verify)
	            {
	               JError::raiseNotice('_verifyDB', $this->getError());
	               $html .= $this->_renderForm( '1' );
	            }
	            else
	            {
	                // migrate the data and output the results
	                $html .= $this->_doInstallSampleData($this->_getState());
	            }
                break;
            case"1":

            	$verify = true;
            	if($state->install_default == '0' || empty($state->install_default))
            	{
            		$verify = $this->_verifyFile();
            	}
                if (!$verify)
                {
                    JError::raiseNotice('_verifyDB', $this->getError());
                    $html .= $this->_renderForm( '1' );
                }
                else
                {
                	 $suffix++;
                	$vars = new JObject();
                    $vars->state = $this->_getState();
                    $vars->state->uploaded_file = $this->_uploaded_file;
                    if($state->install_default == '0' || empty($state->install_default))
                    {
                    	$vars->state->sampledata = $this->_uploaded_filename;
                    }

                    $vars->setError($this->getError());

                    //display sample data information
                    $html .= $this->_renderForm( $suffix, $vars );
                    $html .= $this->_renderView( $suffix, $vars );
                }
                break;
            default:
                $html .= $this->_renderForm( '1' );
                break;
        }

        return $html;
    }

    /**
     * Prepares the 'view' tmpl layout
     *
     * @return unknown_type
     */
    function _renderView( $suffix='', $vars = 0 )
    {
    	if(!$vars)
    	{
        	$vars = new JObject();
    	}
        $vars = new JObject();
        $layout = 'view_'.$suffix;
        $html = $this->_getLayout($layout, $vars);

        return $html;
    }

    /**
     * Prepares variables for the form
     *
     * @return unknown_type
     */
    function _renderForm( $suffix='', $vars = 0  )
    {
    	if(!$vars)
    	{
        	$vars = new JObject();
        	$vars->state = $this->_getState();
    	}

        $vars->token = $this->_getToken( $suffix );

        $layout = 'form_'.$suffix;
        $html = $this->_getLayout($layout, $vars);

        return $html;
    }

    /**
     * Gets the appropriate values from the request
     *
     * @return object
     */
    function _getState()
    {
    	$input = JFactory::getApplication()->input;
        $state = new JObject();
        $state->file = '';
        $state->uploaded_file = '';
        $state->sampledata = '';
        $state->install_default = '0';

        foreach ($state->getProperties() as $key => $value)
        {
            $new_value = $input->get( $key );
            $value_exists = array_key_exists( $key, $_POST );
            if ( $value_exists && !empty($key) )
            {
                $state->$key = $new_value;
            }
        }

        return $state;
    }

 /**
     *
     * Method to check Citruscart tables such us Citruscart_products, Citruscart_categories, and Citruscart_manufactures
     */
	function _checkCitruscartDb()
	{
		$empty = true;

		// Check the registry to see if our Citruscart class has been overridden
        if ( !class_exists('Citruscart') )
            JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

        // load the config class
        Citruscart::load( 'Citruscart', 'defines' );

        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
    	JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );

		if($empty)
    	{
	        // get the manufacturer model
	    	$categoryM = JModelLegacy::getInstance( 'Categories', 'CitruscartModel' );

	    	$categoryTotal = $categoryM->getTotal();
	    	if( $categoryTotal > 1 ) $empty = false;
    	}

    	if($empty)
    	{
	        // get the manufacturer model
	    	$manufacturerM = JModelLegacy::getInstance( 'Manufacturers', 'CitruscartModel' );

	    	$manufacturerTotal = $manufacturerM->getTotal();
	    	if( $manufacturerTotal > 0 ) $empty = false;
    	}

		if($empty)
    	{
	        // get the manufacturer model
	    	$productM = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );

	    	$productTotal = $productM->getTotal();
	    	if( $productTotal > 0 ) $empty = false;
    	}

    	return $empty;

	}

	/**
	 *
	 * Method to upload and verify file if uploaded
	 * @return boolean
	 */
	function _verifyFile()
	{
	    $state = $this->_getState();
	    $success = false;

    	// Uploads the file
		Citruscart::load( 'CitruscartFile', 'library.file' );
		$upload = new CitruscartFile();

		// we have to upload the file
    	if($state->uploaded_file == '')
    	{
			// handle upload creates upload object properties
			$success = $upload->handleUpload( 'file' );

			if($success)
			{
	    		if( strtolower($upload->getExtension()) != 'sql' )
				{
					$this->setError(JText::_('COM_CITRUSCART_THIS_IS_NOT_AN_SQL_FILE'));
					return false;
				}

				// Move the file to let us reuse it
				$upload->setDirectory(JFactory::getConfig()->get('tmp_path', JPATH_SITE.'/tmp'));
				$success = $upload->upload();

				if(!$success)
				{
					$this->setError($upload->getError());
					return false;
				}

				$upload->file_path = $upload->getFullPath();
			}
	    	else
			{
				$this->setError(JText::_('COM_CITRUSCART_COULD_NOT_UPLOAD_SQL_FILE'.$upload->getError()));
				$success = false;
			}
    	}
    	// File already uploaded
    	else
    	{
    		$upload->full_path = $upload->file_path = $state->uploaded_file;
    		$upload->proper_name = CitruscartFile::getProperName($state->uploaded_file);
    		$success = true;
    	}

		if( $success )
		{
			// Set the uploaded file as the file to use during the real import
			$this->_uploaded_file = $upload->getFullPath();
			$this->_uploaded_filename = str_replace( ".sql", "", $upload->physicalname );
		}

    	return $success;

	}

    /**
     * Perform the sample data installation
     *
     * @return html
     */
    function _doInstallSampleData($state)
    {

    	//check db
    	if(!$this->_checkCitruscartDb())
    	{
    		JError::raiseNotice('_verifyDB', JText::_('COM_CITRUSCART_INSTALLATION_FAILED_PLEASE_REMOVE_PRODUCTS_CATEGORIES_AND_MANUFACTURERS'));
    		return $this->_renderForm( '1' );
    	}

        $html = "";
        $vars = new JObject();
        $errors = null;

       	$installURL = '';
        $results = array();

        if($state->install_default == '0' || empty($state->install_default))
        {
        	$database = JFactory::getDBO();
        	$results[ucfirst($sample)] = $this->_populateDatabase( $database, $state->uploaded_file, $errors);
        }
        else
        {
          if(!empty($state->sampledata))
        	{
        	$database = JFactory::getDBO();

        	if(version_compare(JVERSION,'1.6.0','ge')) {
  			// Joomla! 1.6+ code here
   			$installURL = JPATH_SITE.'/administrator/components/com_citruscart/install/sampledata/joomla16/';
			} else {
    		// Joomla! 1.5 code here
			   $installURL = JPATH_SITE.'/administrator/components/com_citruscart/install/sampledata/joomla15/';
			}
        	$sqlfile = $installURL.$state->sampledata.".sql";
        	$results[ucfirst($state->sampledata)] = $this->_populateDatabase( $database, $sqlfile, $errors);

        	}

        }

		//if errors not empty
		if(!empty($errors))
		{
			$vars->_errors = $errors;
			$layout = 'view_4';
		}
		else
		{
			$vars->results = $results;
	        $suffix = $this->_getTokenSuffix();
	        $suffix++;
	        $layout = 'view_'.$suffix;

		}

		$html .= $this->_getLayout($layout, $vars);

        return $html;
    }

	function _populateDatabase (& $database, $sqlfile, & $errors)
	{

		if( !($buffer = file_get_contents($sqlfile)) )
		{
			$vars = new JObject();
            $vars->state = $this->_getState();
            $vars->setError($this->getError());
			return $this->_getLayout("view_2", $vars);;
		}

		$queries = $this->_splitSql($buffer);

		$results = array();
		$n=0;
		foreach ($queries as $query)
		{
			$query = trim($query);
			if ($query != '' && $query {0} != '#')
			{
				$database->setQuery($query);
            	$results[$n]->query = $database->getQuery();
            	$results[$n]->error = '';

            	if (!$database->query())
            	{
                	$results[$n]->error = $database->getErrorMsg();
            	}

            	$results[$n]->affectedRows = $database->getAffectedRows();

				$n++;
			}
		}

		return $results;
	}

	/**
	 * @param string - $sql
	 * @return array
	 */
	function _splitSql($sql)
	{
		$sql = trim($sql);
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);
		$buffer = array ();
		$ret = array ();
		$in_string = false;

		for ($i = 0; $i < strlen($sql) - 1; $i ++) {
			if ($sql[$i] == ";" && !$in_string)
			{
				$ret[] = substr($sql, 0, $i);
				$sql = substr($sql, $i +1);
				$i = 0;
			}

			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\")
			{
				$in_string = false;
			}
			elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\"))
			{
				$in_string = $sql[$i];
			}
			if (isset ($buffer[1]))
			{
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $sql[$i];
		}

		if (!empty ($sql))
		{
			$ret[] = $sql;
		}
		return ($ret);
	}

}
