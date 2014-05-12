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

    /**
     * File: Browser.php
     * Author: Chris Schuld (http://chrisschuld.com/)
     * Last Modified: November 08, 2009
     * @version 1.6
     * @package PegasusPHP
     *
     * Copyright (C) 2008-2009 Chris Schuld  (chris@chrisschuld.com)
     *
     * This program is free software; you can redistribute it and/or
     * modify it under the terms of the GNU General Public License as
     * published by the Free Software Foundation; either version 2 of
     * the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details at:
     * http://www.gnu.org/copyleft/gpl.html
     *
     *
     * Typical Usage:
     *
     *   $browser = new Browser();
     *   if( $browser->getBrowser() == Browser::BROWSER_FIREFOX && $browser->getVersion() >= 2 ) {
     *      echo 'You have FireFox version 2 or greater';
     *   }
     *
     * User Agents Sampled from: http://www.useragentstring.com/
     *
     * This implementation is based on the original work from Gary White
     * http://apptools.com/phptools/browser/
     *
     * Gary White noted: "Since browser detection is so unreliable, I am
     * no longer maintaining this script. You are free to use and or
     * modify/update it as you want, however the author assumes no
     * responsibility for the accuracy of the detected values."
     *
     * Anyone experienced with Gary's script might be interested in these notes:
     *
     *   Added class constants
     *   Added detection and version detection for Google's Chrome
     *   Updated the version detection for Amaya
     *   Updated the version detection for Firefox
     *   Updated the version detection for Lynx
     *   Updated the version detection for WebTV
     *   Updated the version detection for NetPositive
     *   Updated the version detection for IE
     *   Updated the version detection for OmniWeb
     *   Updated the version detection for iCab
     *   Updated the version detection for Safari
     *   Updated Safari to remove mobile devices (iPhone)
     *   Added detection for iPhone
     *   Added detection for robots
     *   Added detection for mobile devices
     *   Added detection for BlackBerry
     *   Removed Netscape checks (matches heavily with firefox & mozilla)
     *
     *
     * ADDITIONAL UPDATES:
     *
     * 2008-11-07:
     *  + Added Google's Chrome to the detection list
     *  + Added isBrowser(string) to the list of functions special thanks to
     *    Daniel 'mavrick' Lang for the function concept (http://mavrick.id.au)
     *
     * 2008-12-09:
     *  + Removed unused constant
     *
     * 2009-02-16: (Rick Hale)
     *  + Added version detection for Android phones.
     *
     * 2009-03-14:
     *  + Added detection for iPods.
     *  + Added Platform detection for iPhones
     *  + Added Platform detection for iPods
     *
     * 2009-04-22:
     *  + Added detection for GoogleBot
     *  + Added detection for the W3C Validator.
     *  + Added detection for Yahoo! Slurp
     *
     * 2009-04-27:
     *  + Updated the IE check to remove a typo and bug (thanks John)
     *
     * 2009-08-18:
     *  + Updated to support PHP 5.3 - removed all deprecated function calls
     *  + Updated to remove all double quotes (") -- converted to single quotes (')
     *
     * 2009-11-08:
     *  + PHP 5.3 Support
     *  + Added support for BlackBerry OS and BlackBerry browser
     *  + Added support for the Opera Mini browser
     *  + Added additional documenation
     *  + Added support for isRobot() and isMobile()
     *  + Added support for Opera version 10
     *  + Added support for deprecated Netscape Navigator version 9
     *  + Added support for IceCat
     *  + Added support for Shiretoko
     */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

    class CitruscartBrowser extends DSCBrowser
    {

    }

