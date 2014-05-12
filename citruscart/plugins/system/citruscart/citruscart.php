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
defined('_JEXEC') or die('Restricted access');

/** Import library dependencies */
jimport('joomla.plugin.plugin');

class plgSystemCitruscart extends JPlugin
{
    /**
     * This holds the html output by our override controller
     * @var unknown_type
     */
    var $_html = null;

    function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

    /**
     * Loads overrides if they exist
     * otherwise just keeps quiet
     *
     * We should execute here so we run before the core component does
     * (until we can figure out how to completely stop the core component
     * from loading at all)
     *
     * @return unknown_type
     */
    function onAfterRoute()
    {
        $success = null;

        // Should check that Citruscart is installed first before executing
        if (!$this->_isInstalled())
        {
            return $success;
        }

        // clean expired session carts
        $this->deleteExpiredSessionCarts();

        // clean expired session products compared
        $this->deleteExpiredSessionProductsCompared();

        // get the option variable
        // and get rid of the com_
        $option = JFactory::getApplication()->input->get( 'option', '', 'get' );
        $name = str_replace("com_", "", $option);
        // does an override exist for this component?
        // if so, include it!  Hooray!  Drinks for everyone!
        if (!$this->overrideExists( $name ))
        {
            // if not, quietly exit stage left
            return $success;
        }
        // hee hee, this method returns the same thing no matter what :-)
        return $success;
    }

    /**
     * Checks if a component-specific override exists
     *
     * @return boolean
     */
    function overrideExists( $name )
    {
        $success = false;

        $app = JFactory::getApplication();
        $site = 'site';
        if ( $app->isAdmin() )
        {
            $site = 'admin';
        }

        jimport('joomla.filesystem.file');
        $file = JPATH_SITE."/plugins/system/citruscart/citruscart/components/".$name."/".$site."/{$name}.php";

        // Enable each override to be disabled by a param in the xml file
        if (JFile::exists( $file ) && $this->_activeOverride( $name, $site ) )
        {
        	// this includes the override for the entrypoint file of the component
            // which starts the entire override
            // enjoy the ride!
            ob_start();

            if ($disable_error_reporting = $this->params->get( "disable_error_reporting", '0' ))
            {
                // disable error reporting if this is a live site
                ini_set('display_errors', 0);
                ini_set('error_reporting', 0);
            }

            require_once( $file );
            $this->_html = ob_get_contents();
            ob_end_clean();
						echo $this->_html;

            $success = true;
        }
        return $success;
    }

    /**
     * This method determines, if the override is active
     */
    function _activeOverride( $name, $site )
    {
      $aliases = array( 'users' => 'user' );

      $param = "{$site}_override_{$name}";
      if( isset($aliases[$name] ) )
        $param = "{$site}_override_{$aliases[$name]}";

      return $this->params->get( $param, '0' );
    }

    /**
     * This sets the document buffer to whatever came out of our overrides
     */
    function onAfterDispatch()
    {
        $success = null;

        // Should check that Citruscart is installed first before executing
        if (!$this->_isInstalled())
        {
            return $success;
        }

        if (!empty($this->_html))
        {
            $document = JFactory::getDocument();
            $document->setBuffer( $this->_html, 'component' );
        }
    }

    /**
     * Checks the extension is installed
     *
     * @return boolean
     */
    function _isInstalled()
    {
        $success = false;

        jimport('joomla.filesystem.file');
        if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_citruscart/defines.php'))
        {
            $success = true;
            // Check the registry to see if our Citruscart class has been overridden
            if ( !class_exists('Citruscart') ) {
                JLoader::register( "Citruscart", JPATH_ADMINISTRATOR."/components/com_citruscart/defines.php" );

            }
        }
        return $success;
    }

    /**
     *
     * Enter description here ...
     * @return unknown_type
     */
    function deleteExpiredSessionCarts()
    {
        $config = Citruscart::getInstance();
        $last_run = $config->get('last_deleted_expired_sessioncarts');

        Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
        $helper = new CitruscartHelperBase();

        $date = JFactory::getDate();
        $now = $date->toSql();

        $three_hours_ago = $helper->getOffsetDate($now, '-3');

        // when was this last run?
        // if it was run more than 3 hours ago, run again
        if ($last_run < $three_hours_ago)
        {
            // run it
            jimport( 'joomla.application.component.model' );
            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
            $model = JModelLegacy::getInstance( 'Carts', 'CitruscartModel');
            $model->deleteExpiredSessionCarts();
        }

        return;
    }

	/**
     * Method to delete expired session compared products
     * @return void
     */
    function deleteExpiredSessionProductsCompared()
    {
        $config = Citruscart::getInstance();
        $last_run = $config->get('last_deleted_expired_sessionproductscompared');

        Citruscart::load( "CitruscartHelperBase", 'helpers._base' );
        $helper = new CitruscartHelperBase();

        $date = JFactory::getDate();
        $now = $date->toSql();

        $three_hours_ago = $helper->getOffsetDate($now, '-3');

        // when was this last run?
        // if it was run more than 3 hours ago, run again
        if ($last_run < $three_hours_ago)
        {
            // run it
            jimport( 'joomla.application.component.model' );
            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
            $model = JModelLegacy::getInstance( 'ProductCompare', 'CitruscartModel');
            $model->deleteExpiredSessionProductCompared();
        }

        return;
    }
}
