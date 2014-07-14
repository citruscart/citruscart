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

class DSCSocialGoogleUrl extends DSCSocial
{
    public $key = null;
    public $apiURL = null;

    function __construct($config=array())
    {
        // $config should be an array of key=>value pairs
        if (empty($config['apiURL'])) {
            $config['apiURL'] = 'https://www.googleapis.com/urlshortener/v1/url';
        }

        // Set the API Url
        if (!empty($config['key'])) {
            $config['apiURL'] = $config['apiURL'] . '?key=' . $config['key'];
        }

        parent::__construct($config);
    }

    // Shorten a URL
    function shorten($url)
    {
        // Send information along
        $response = $this->send($url);
        // Return the result
        return isset($response['id']) ? $response['id'] : false;
    }

    // Expand a URL
    function expand($url)
    {
        // Send information along
        $response = $this->send($url, false);
        // Return the result
        return isset($response['longUrl']) ? $response['longUrl'] : false;
    }

    // Send information to Google
    function send($url, $shorten = true)
    {
        // Create cURL
        $ch = curl_init();
        // If we're shortening a URL...
        if ($shorten) {
            curl_setopt($ch, CURLOPT_URL, $this->apiURL);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
                "longUrl" => $url
            )));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"
            ));
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->apiURL . '&shortUrl=' . $url);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Execute the post
        $result = curl_exec($ch);
        // Close the connection
        curl_close($ch);
        // Return the result
        return json_decode($result, true);
    }
}