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

Citruscart::load( "CitruscartHelperBase", 'helpers._base' );

class CitruscartHelperCurrency extends CitruscartHelperBase 
{
    public $currencies = array();
    public $codes = array();
    
    /**
     * Format and convert a number according to currency rules
     * 
     * @param unknown_type $amount
     * @param unknown_type $currency
     * @return unknown_type
     */
    function _($amount, $currency='', $options='')
    {
        $currencies = $this->currencies;
        $codes = $this->codes;
        
        // default to whatever is in config
        $config = Citruscart::getInstance();
        $options = (array) $options;

        $default_currencyid = $config->get('default_currencyid', '1');
        if ( !isset( $currencies[$default_currencyid] ) )
        {
            // if currency is an integer, load the object for its id
            JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
            $currencies[$default_currencyid] = JTable::getInstance('Currencies', 'CitruscartTable');
            $currencies[$default_currencyid]->load( (int) $default_currencyid );
        }
        $num_decimals = isset($options['num_decimals']) ? $options['num_decimals'] : $currencies[$default_currencyid]->currency_decimals;
        $thousands = isset($options['thousands']) ? $options['thousands'] : $currencies[$default_currencyid]->thousands_separator;
        $decimal = isset($options['decimal']) ? $options['decimal'] : $currencies[$default_currencyid]->decimal_separator;
        $pre = isset($options['pre']) ? $options['pre'] : $currencies[$default_currencyid]->symbol_left;
        $post = isset($options['post']) ? $options['post'] : $currencies[$default_currencyid]->symbol_right;
				        // if currency is an object, use it's properties
        if (is_object($currency))
        {
            $table = $currency;
            $num_decimals = $table->currency_decimals;
            $thousands  = $table->thousands_separator;
            $decimal    = $table->decimal_separator;
            $pre        = $table->symbol_left;
            $post       = $table->symbol_right;
            if ($default_currencyid != $table->currency_id)
            {
                $convertTo = $table->currency_code;
            }
        }
        elseif (!empty($currency) && is_numeric($currency))
        {
            if (!isset($currencies[$currency]))
            {
                // if currency is an integer, load the object for its id
                JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
                $currencies[$currency] = JTable::getInstance('Currencies', 'CitruscartTable');
                $currencies[$currency]->load( (int) $currency );               
            }
            $table = $currencies[$currency];
            
            if (!empty($table->currency_id))
            {
                $num_decimals = $table->currency_decimals;
                $thousands  = $table->thousands_separator;
                $decimal    = $table->decimal_separator;
                $pre        = $table->symbol_left;
                $post       = $table->symbol_right;
                
                if ($default_currencyid != $currency)
                {
                    $convertTo = $table->currency_code;
                }
            }
        }
        elseif (!empty($currency))
        {
            if (!isset($codes[$currency]))
            {
                // if currency is a string (currency_code) load the object for its code
                JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
                $codes[$currency] = JTable::getInstance('Currencies', 'CitruscartTable');
                $keynames = array();
                $keynames['currency_code'] = (string) $currency;
                $codes[$currency]->load( $keynames );
            }
            $table = $codes[$currency];
            
            if (!empty($table->currency_id))
            {
                $num_decimals = $table->currency_decimals;
                $thousands  = $table->thousands_separator;
                $decimal    = $table->decimal_separator;
                $pre        = $table->symbol_left;
                $post       = $table->symbol_right;
                
                if ($default_currencyid != $table->currency_id)
                {
                    $convertTo = $table->currency_code;
                }
            }
        }

        // if the currency code we're using is diff from the store-wide currency, then we need to convert the amount
        if (!empty($convertTo))
        {
            if (!isset($currencies[$default_currencyid]))
            {
                // if currency is an integer, load the object for its id
                JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
                $currencies[$default_currencyid] = JTable::getInstance('Currencies', 'CitruscartTable');
                $currencies[$default_currencyid]->load( (int) $default_currencyid );               
            }
            $table = $currencies[$default_currencyid];

            if (isset($this) && is_a( $this, 'CitruscartHelperCurrency' )) 
            {
                $helper = $this;
            } 
                else 
            {
                $helper = CitruscartHelperBase::getInstance( 'Currency' );
            }
            $amount = $helper->convert($table->currency_code, $convertTo, $amount);
        }
        
        $return = $pre.number_format($amount, $num_decimals, $decimal, $thousands).$post;
        
        return $return;
    }
    
    /**
     * Converts an amount from one currency to another
     * 
     * @param float $amount
     * @param str $currencyFrom
     * @param str $currencyTo
     * @return boolean
     */
    function convert( $currencyFrom, $currencyTo='USD', $amount='1', $refresh=false )
    {
        static $rates;
        
        if (!is_array($rates))
        {
            $rates = array();
        }
        
        if (empty($rates[$currencyFrom]) || !is_array($rates[$currencyFrom]))
        {
            $rates[$currencyFrom] = array();
        }
        
        if (empty($rates[$currencyFrom][$currencyTo]))
        {
            if (isset($this) && is_a( $this, 'CitruscartHelperCurrency' )) 
            {
                $helper = $this;
            } 
                else 
            {
                $helper = CitruscartHelperBase::getInstance( 'Currency' );
            }
            // get the exchange rate, and let the getexchange rate method handle refreshing the cache
            $rates[$currencyFrom][$currencyTo] = $helper->getExchangeRate( $currencyFrom, $currencyTo, $refresh );
        }
        $exchange_rate = $rates[$currencyFrom][$currencyTo];
        
        // convert the amount        
        $return = $amount * $exchange_rate;
        return $return;
    }
    
    /**
     * Gets the exchange rate 
     * 
     * @param float $amount
     * @param str $currencyFrom
     * @param str $currencyTo
     * @return boolean
     */
    function getExchangeRate( $currencyFrom, $currencyTo='USD', $refresh=false )
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
        
        $date = JFactory::getDate();
        $now = $date->toSql();
        $database = JFactory::getDBO();
        $database->setQuery( "SELECT DATE_SUB( '$now', INTERVAL 1 HOUR )" );
        $expire_datetime = $database->loadResult();

        if ($currencyTo == 'USD')
        {
            if (empty($this->codes[$currencyFrom]))
            {
                // get from DB table
                $this->codes[$currencyFrom] = JTable::getInstance('Currencies', 'CitruscartTable');
                $this->codes[$currencyFrom]->load( array('currency_code'=>$currencyFrom) );                
            }
            $tableFrom = $this->codes[$currencyFrom];
            
            if (!empty($tableFrom->currency_id))
            {
            	// Auto Update Enabled?
            	if(Citruscart::getInstance()->get('currency_exchange_autoupdate', 1))
            	{
	                // refresh if it's too old or refresh forced
	                if ($tableFrom->updated_date < $expire_datetime || $refresh)
	                {
	                    if ($currencyFrom == "USD")
	                    {
	                        $tableFrom->exchange_rate = (float) 1.0;
	                    }
	                        else
	                    {
	                        $tableFrom->exchange_rate = $this->getExchangeRateYahoo( $currencyFrom, $currencyTo );    
	                    }
	                    $tableFrom->updated_date = $now;
	                    $tableFrom->save();
	                }
            	}
               	return (float) $tableFrom->exchange_rate * 1.0;
            	
            }
                else
            {
                // invalid currency, fail
                JError::raiseError('1', JText::_('COM_CITRUSCART_INVALID_CURRENCY_TYPE'));
                return;                
            }
        }
        
        // Auto Update Enabled?
        if(Citruscart::getInstance()->get('currency_exchange_autoupdate', 1))
        {
        	$exchange_rate = $this->getExchangeRateYahoo( $currencyFrom, $currencyTo );
        }
        else
        {
            if (empty($this->codes[$currencyFrom]))
            {
                // get from DB table
                $this->codes[$currencyFrom] = JTable::getInstance('Currencies', 'CitruscartTable');
                $this->codes[$currencyFrom]->load( array('currency_code'=>$currencyFrom) );
            }
            $tableFrom = $this->codes[$currencyFrom];

            if (empty($this->codes[$currencyTo]))
            {
                // get from DB table
                $this->codes[$currencyTo] = JTable::getInstance('Currencies', 'CitruscartTable');
                $this->codes[$currencyTo]->load( array('currency_code'=>$currencyTo) );
            }
            $tableTo = $this->codes[$currencyTo];
            
            if(!empty($tableFrom->currency_id) && !empty($tableTo->currency_id))
            {
            	// Get the exchange rate manually
            	// All Values are USD based, so if (1$ = 1,3�) and (1$ = 1,6�), we have that (1� = 1,23�)
            	// so if we want to the exchange rate � => � is 1,23            	
            	$exchange_rate = $tableFrom->exchange_rate / $tableTo->exchange_rate;
            }
       		else
            {
                // invalid currency, fail
                JError::raiseError('1', JText::_('COM_CITRUSCART_INVALID_CURRENCY_TYPE'));
                return;                
            }
        }
             
        return (float) $exchange_rate * 1.0;
    }

   /**
     * Gets the exchange rate 
     * 
     * @param float $amount
     * @param str $currencyFrom
     * @param str $currencyTo
     * @return boolean
     */
    function getExchangeRateYahoo( $currencyFrom, $currencyTo='USD' )
    {
        static $has_run;
        
        // if refresh = true 
        // query yahoo for exchange rate
        if (!empty($has_run)) { sleep(1); }
        
        $url    = "http://quote.yahoo.com/d/quotes.csv?s={$currencyFrom}{$currencyTo}=X&f=l1&e=.csv";
        
        $handle = fopen($url, 'r');
        
        if ($handle) {
            $result = fgets($handle, 4096);
            fclose($handle);
        }
        
        $rate = (float) $result * 1.0;
        
        $has_run = true;
        
        return $rate;
    }
    
    /**
     * Format a number according to currency rules
     * 
     * @param unknown_type $amount
     * @param unknown_type $currency
     * @return unknown_type
     */
    function format($amount, $currency='', $options='')
    {
        $currencies = $this->currencies;
        $codes = $this->codes;
        
        // default to whatever is in config
        $config = Citruscart::getInstance();
        $options = (array) $options;

        // unless we specified the currency with we want to work, use the default one
        if( $currency == '' )
        {
	        $default_currencyid = $config->get('default_currencyid', '1');
	        if ( !isset( $currencies[$default_currencyid] ) )
	        {
	            // if currency is an integer, load the object for its id
	            JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
	            $currencies[$default_currencyid] = JTable::getInstance('Currencies', 'CitruscartTable');
	            $currencies[$default_currencyid]->load( (int) $default_currencyid );
	        }
	        $num_decimals = isset($options['num_decimals']) ? $options['num_decimals'] : $currencies[$default_currencyid]->currency_decimals;
	        $thousands = isset($options['thousands']) ? $options['thousands'] : $currencies[$default_currencyid]->thousands_separator;
	        $decimal = isset($options['decimal']) ? $options['decimal'] : $currencies[$default_currencyid]->decimal_separator;
	        $pre = isset($options['pre']) ? $options['pre'] : $currencies[$default_currencyid]->symbol_left;
	        $post = isset($options['post']) ? $options['post'] : $currencies[$default_currencyid]->symbol_right;
        }
            
        // if currency is an object, use it's properties
        if (is_object($currency))
        {
            $table = $currency;
            $num_decimals = $table->currency_decimals;
            $thousands  = $table->thousands_separator;
            $decimal    = $table->decimal_separator;
            $pre        = $table->symbol_left;
            $post       = $table->symbol_right;
        }
        elseif (!empty($currency) && is_numeric($currency))
        {
            if (!isset($currencies[$currency]))
            {
                // if currency is an integer, load the object for its id
                JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
                $currencies[$currency] = JTable::getInstance('Currencies', 'CitruscartTable');
                $currencies[$currency]->load( (int) $currency );               
            }
            $table = $currencies[$currency];

            if (!empty($table->currency_id))
            {
                $num_decimals = $table->currency_decimals;
                $thousands  = $table->thousands_separator;
                $decimal    = $table->decimal_separator;
                $pre        = $table->symbol_left;
                $post       = $table->symbol_right;
            }
        }
        elseif (!empty($currency))
        {
            if (!isset($codes[$currency]))
            {
                // if currency is a string (currency_code) load the object for its code
                JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
                $codes[$currency] = JTable::getInstance('Currencies', 'CitruscartTable');
                $keynames = array();
                $keynames['currency_code'] = (string) $currency;
                $codes[$currency]->load( $keynames );
            }
            $table = $codes[$currency];

            if (!empty($table->currency_id))
            {
                $num_decimals = $table->currency_decimals;
                $thousands  = $table->thousands_separator;
                $decimal    = $table->decimal_separator;
                $pre        = $table->symbol_left;
                $post       = $table->symbol_right;
            }
        }

        $return = $pre.number_format($amount, $num_decimals, $decimal, $thousands).$post;
        return $return;
    }
    
    /**
     * Loads a currency by its ID 
     * and stores it for later use by the application
     * 
     * @param unknown_type $id
     * @return unknown_type
     */
    function load( $id )
    {
        if (empty($this->currencies[$id]))
        {
            JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
            $this->currencies[$id] = JTable::getInstance('Currencies', 'CitruscartTable');
            $this->currencies[$id]->load($id);          
        }
        return $this->currencies[$id];
    }

    /**
     * Returns ID of the currency which will be saved along with order
     */
    public static function getCurrentCurrency()
    {
        // $session_currency = CitruscartHelperBase::getSessionVariable( 'currency_id', 0 );
        // if( $session_currency ) {
        // return $session_currency; 
        // }
         
        return Citruscart::getInstance()->get('default_currencyid', '1');
    }
}