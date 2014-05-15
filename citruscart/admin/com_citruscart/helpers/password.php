<?PHP
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/***************************************************************
*  Copyright notice
*
*  Copyright (c) 2006, Suman Debnath
*  All rights reserved.
*
*  Redistribution and use in source and binary forms, with or without modification,
*  are permitted provided that the following conditions are met:
*
*  Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
*  Redistributions in binary form must reproduce the above copyright notice,
*  this list of conditions and the following disclaimer in the documentation and/or
*  other materials provided with the distribution.
*  Neither the name of Suman Debnath nor the names of its contributors may be used to endorse or promote
*  products derived from this software without specific prior written permission.
*
*  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
*  INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. *  IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
*  OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA,
*  OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
*  OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
*  EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
***************************************************************/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

class CitruscartHelperPassword extends CitruscartHelperBase
{
    /**
     * Dictionary file path
     *
     * @var string
     */
    var $dictionary = '';
    /**
     * Blacklisted word file path
     *
     * @var string
     */
    var $blackWordList = '';
    /**
     * Blacklisted character file path
     *
     * @var string
     */
    var $blackCharList = '';

    /**
     * Minimum length of the password
     *
     * @var integer
     */
    var $min_length = 8;

    /**
     * Type of password check
     *
     * @var string
     */
    var $password_check_type = 'LD';
    /**
     * Type of name & password check
     *
     * @var string
     */
    var $name_check_type = 'S';

    /**
    * Array of error messages
    *
    * @var array
    */
    var $errorMsgArray=array();

    /**
    * Array of umlauts and their equivalents
    *
    * @var array
    */
    var $umlautArr = array(
    '�' => 'ss',
    '�' => 'A',
    '�' => 'A',
    '�' => 'A',
    '�' => 'A',
    '�' => 'Ae',
    '�' => 'A',
    '�' => 'Ae',
    '�' => 'C',
    '�' => 'E',
    '�' => 'E',
    '�' => 'E',
    '�' => 'E',
    '�' => 'I',
    '�' => 'I',
    '�' => 'I',
    '�' => 'I',
    '�' => 'N',
    '�' => 'O',
    '�' => 'O',
    '�' => 'O',
    '�' => 'O',
    '�' => 'Oe',
    '�' => 'U',
    '�' => 'U',
    '�' => 'U',
    '�' => 'Ue',
    '�' => 'Y',
    '�' => 'a',
    '�' => 'a',
    '�' => 'a',
    '�' => 'a',
    '�' => 'ae',
    '�' => 'a',
    '�' => 'ae',
    '�' => 'c',
    '�' => 'e',
    '�' => 'e',
    '�' => 'e',
    '�' => 'e',
    '�' => 'i',
    '�' => 'i',
    '�' => 'i',
    '�' => 'i',
    '�' => 'n',
    '�' => 'o',
    '�' => 'o',
    '�' => 'o',
    '�' => 'o',
    '�' => 'oe',
    '�' => 'u',
    '�' => 'u',
    '�' => 'u',
    '�' => 'ue',
    '�' => 'y',
    '�' => 'y',
    '�' => 'Oe',
    '�' => 'oe',
    '�' => 'S',
    '�' => 's',
    '�' => 'Y',
    '�' => 'Z',
    '�' => 'z',
    '�' => 'f'
    );

    /*********************************************************
    * Public functions for use by the client program
    *********************************************************/
    /**
    * Sets some configuration veriables based on input parameters
    * @access public
    * @param string $password_check_type Type of password check to be performed � optional ('D' - Dictionary word search, 'L' -  Length check, 'H' - Heterogeneity/homogeneity check, 'W' - Blacklisted Word check or 'C' - Blacklisted character check). Can be used in combination e.g. 'DLW'. Default: 'LD'
    * @param string $name_check_type Type of name similarity check to be performed � optional ('P' - Phonetic Check, 'T' -  Similarity Check or 'S' - Checks if one string is a subset of another). Can be used in combination e.g. 'PTS'. Default: 'S'
    * @param string $dict path to a dictionary file
    * @param string $blackWords path to a blacklisted word file
    * @param string $blackChars path to a blacklisted character file
    * @param array $errArr Array of error messages
    */
    function setConfig($password_check_type = null, $name_check_type = null, $dict = null, $blackWords = null, $blackChars = null, $errArr = array()) {
        if (!empty($password_check_type)) {
            $this->password_check_type = $password_check_type;
        }
        if (!empty($name_check_type)) {
            $this->name_check_type = $name_check_type;
        }

        $this->setVar($this->dictionary, $dict);
        $this->setVar($this->blackWordList, $blackWords);
        $this->setVar($this->blackCharList, $blackChars);

        if ((!is_array($errArr)) || (1 > count($errArr))) {
            $errArr = array();
        }
        $errArr['ERR_LENGTH'] = empty($errArr['ERR_LENGTH']) ? 'This password is too short' : $errArr['ERR_LENGTH'];
        $errArr['ERR_SIMILAR'] = empty($errArr['ERR_SIMILAR']) ? 'Username and Password cannot be this similar' : $errArr['ERR_SIMILAR'];
        $errArr['ERR_PHONETIC'] = empty($errArr['ERR_PHONETIC']) ? 'Username and Password cannot be this phonetically similar' : $errArr['ERR_PHONETIC'];
        $errArr['ERR_DICT'] = empty($errArr['ERR_DICT']) ? 'The password should not be based upon a dictionary word' : $errArr['ERR_DICT'];
        $errArr['ERR_HETER'] = empty($errArr['ERR_HETER']) ? 'The password is too homogeneous' : $errArr['ERR_HETER'];
        $errArr['ERR_BLACKWORD'] = empty($errArr['ERR_BLACKWORD']) ? 'The password uses a blacklisted word' : $errArr['ERR_BLACKWORD'];
        $errArr['ERR_BLACKCHAR'] = empty($errArr['ERR_BLACKCHAR']) ? 'The password contains blacklisted characters' : $errArr['ERR_BLACKCHAR'];
        $errArr['ERR_SIMILAR_LOOKING_CHARS'] = empty($errArr['ERR_SIMILAR_LOOKING_CHARS']) ? 'The password contains similar looking characters' : $errArr['ERR_SIMILAR_LOOKING_CHARS'];

        $errArr['ERR_CONFIG_BLACKCHAR'] = empty($errArr['ERR_CONFIG_BLACKCHAR']) ? 'Could not load a valid blacklisted character file' : $errArr['ERR_CONFIG_BLACKCHAR'];
        $errArr['ERR_CONFIG_BLACKWORD'] = empty($errArr['ERR_CONFIG_BLACKWORD']) ? 'Could not load a valid blacklisted word file' : $errArr['ERR_CONFIG_BLACKWORD'];
        $errArr['ERR_CONFIG_DICT'] = empty($errArr['ERR_CONFIG_DICT']) ? 'Could not load a valid dictionary file' : $errArr['ERR_CONFIG_DICT'];
        $errArr['ERR_CONFIG_HETER'] = empty($errArr['ERR_CONFIG_HETER']) ? 'Conflict in heterogeneity check and password generation configuration' : $errArr['ERR_CONFIG_HETER'];
        $errArr['ERR_CONFIG_LENGTH'] = empty($errArr['ERR_CONFIG_LENGTH']) ? 'Generated password length cannot be less then specified minimum length' : $errArr['ERR_CONFIG_LENGTH'];

        foreach ($errArr as $key => $value) {
            define($key, $value);
        }
    }

    /**
    * Checks a password.
    * @return bool
    * @param string $pass A password string
    * @param string $name Optional user name string. If supplied, the function will also check for similarities with the password
    * @access public
    */
    function checkPassword($pass, $name = '') {
        $output = true;

        if (!$this->checkStrength($pass)) {
            $output = false;
        }

        if (!empty($name)) {
            $output &= $this->checkAgainstName($pass, $name);
        }

        return $output;
    }

    /**
    * Gets a safe password
    * NOTE: The class will give an error if heterogeneity check is on AND $char_type is U/L/D/S to prevent an infinite loop. The class will also give an error if length check is on AND $len is less than the minimum length to prevent an infinite loop.
    * @return mixed
    * @param string $username Optional user name. If one is supplied, the generated password will be checked for similarity.
    * @param integer $len Optional length of password. Default is 8.
    * @param string $char_type Optional type of characters to be included ('U' - Upper Case, 'L' -  Lower Case, 'D' - Digits, 'S' - Special Characters, 'X' - All or 'A' - Alphanumeric) Default: 'A'.
    * @access public
    */
    function getSafePassword($username = null, $len = 0, $char_type = null) {
        if (preg_match('/[h]/i', $this->password_check_type) && preg_match('/[ulds]/i', $char_type)) {
            $this->errorMsgArray[] = ERR_CONFIG_HETER;
            return false;
        }

        while ($pword = $this->generatePassword($len, $char_type)) {
            if ($this->checkPassword($pword, $username)) {
                return $pword;
            }
        }
    }

    /**
    * Returns the current error message
    * @return array
    * @access public
    */
    function errorMsg() {
        return $this->errorMsgArray;
    }

    /*********************************************************
    * Private functions for use by the public functions
    *********************************************************/
    /**
    * Checks a password against given user name for different kinds of similarity
    * @return bool
    * @param string $pass Password string
    * @param string $name User name
    * @access private
    */
    function checkAgainstName($pass, $name) {
        $output = true;
        //$check_type = eregi_replace('[^PTS]', '', $this->name_check_type);
        $check_type = preg_replace('/[^PTS]/i', '', $this->name_check_type);
        if (empty($check_type)) {
            $check_type = 'S';
        } else {
            $temp = '';
            if (preg_match('/P/i', $check_type)) $temp .= 'P';
            if (preg_match('/T/i', $check_type)) $temp .= 'T';
            if (preg_match('/S/i', $check_type)) $temp .= 'S';

            $check_type = $temp;
            unset($temp);
        }

        for ($x = 0; $x < strlen($check_type); $x++) {
            if (!$output) continue;
            switch($check_type{$x}) {
                case 'P':
                    $output = $this->phoneticCheck($pass, $name);
                    break;
                case 'T':
                    $output = $this->similarityCheck($pass, $name);
                    break;
                case 'S':
                    $output = $this->stringCheck($pass, $name);
                    break;
            }
        }

        return $output;
    }

    /**
    * Checks password strength by conducting different checks
    * @return bool
    * @param string $password Password string
    * @access private
    */
    function checkStrength($password) {
        $output = true;
        //$check_type = eregi_replace('[^DLHWC]', '', $this->password_check_type);
        $check_type = preg_replace('/[^DLHWC]/i', '', $this->password_check_type);
        if (empty($check_type)) {
            $check_type = 'LD';
        } else {
            $temp = '';
            if (preg_match('/L/i', $check_type)) $temp .= 'L';
            if (preg_match('/H/i', $check_type)) $temp .= 'H';
            if (preg_match('/D/i', $check_type)) $temp .= 'D';
            if (preg_match('/W/i', $check_type)) $temp .= 'W';
            if (preg_match('/C/i', $check_type)) $temp .= 'C';

            $check_type = $temp;
            unset($temp);
        }

        for ($x = 0; $x < strlen($check_type); $x++) {
            switch($check_type{$x}) {
                case 'D':
                    $output &= $this->dictionaryCheck($password);
                    break;
                case 'L':
                    $output &= $this->lengthCheck($password);
                    break;
                case 'H':
                    $output &= $this->heterogeneityCheck($password);
                    break;
                case 'W':
                    $output &= $this->blackWordCheck($password);
                    break;
                case 'C':
                    $output &= $this->blackCharCheck($password);
                    break;
            }
        }

        return $output;
    }

    /**
    * Checks password strength by conducting different checks
    * @return string
    * @param integer $len Length of password. Default: 8
    * @param string $char_type Optional type of characters to be included ('U' - Upper Case, 'L' -  Lower Case, 'D' - Digits, 'S' - Special Characters, 'X' - All or 'A' - Alphanumeric) Default: 'A'.
    * @access private
    */
    function generatePassword($len = 8, $char_type = 'A') {
        $len = (intval($len) <= 0) ? $this->min_length : intval($len);
        if ($len < $this->min_length) {
            $this->errorMsgArray[] = ERR_CONFIG_LENGTH;
            return false;
        }

        //$char_type = eregi_replace('[^ULDSXA]', '', $char_type);
        $char_type = preg_replace('/[^ULDSXA]/i', '', $char_type);
        if (empty($char_type)) {
            $char_type = 'A';
        } else {
            $temp = '';
            if (preg_match('/U/i', $char_type)) $temp .= 'U';
            if (preg_match('/L/i', $char_type)) $temp .= 'L';
            if (preg_match('/D/i', $char_type)) $temp .= 'D';
            if (preg_match('/S/i', $char_type)) $temp .= 'S';
            if (preg_match('/X/i', $char_type)) $temp .= 'X';
            if (preg_match('/A/i', $char_type)) $temp .= 'A';

            $char_type = $temp;
            unset($temp);
        }
        $char_arr = array();

        //Array of allowed digits
        $digit = range(1, 9);
        //Array of allowed special characters
        $schar = array('~', '$', '&', '*', '%', '^', '!', '@', '#', '(', ')', '-');
        //Array of allowed lower case characters
        $lcase = range('a', 'z');
        //Array of allowed upper case characters
        $ucase = range('A', 'Z');

        //Seeding the random number generator if PHP version is lower than 4.2.0
        if (version_compare(PHP_VERSION, '4.2.0') == -1) {
            mt_srand((double)microtime() * 1234567890);
        }

        for($x=0; $x<strlen($char_type); $x++) {
            switch($char_type{$x}) {
                case 'U':
                    $char_arr = array_merge($char_arr, $ucase);
                    break;
                case 'L':
                    $char_arr = array_merge($char_arr, $lcase);
                    break;
                case 'D':
                    $char_arr = array_merge($char_arr, $digit);
                    break;
                case 'S':
                    $char_arr = array_merge($char_arr, $schar);
                    break;
                case 'X':
                    $char_arr = array_merge($char_arr, $ucase, $lcase, $digit, $schar);
                    break;
                case 'A':
                default:
                    $char_arr = array_merge($char_arr, $ucase, $lcase, $digit);
                    break;
            }
        }

        if (stristr($this->password_check_type, 'c')) {
            $char_arr = $this->deductBlackChar($char_arr);
        }

        shuffle($char_arr);
        $pword = '';
        for($x=0; $x < $len; $x++) {
            $pword .= $char_arr[mt_rand(0, (sizeof($char_arr)-1))];
        }

        return $pword;
    }

    /**
    * Checks Heterogeneity/homogeneity of a password.
    * @return bool
    * @param string $pword Password string
    * @access private
    */
    function heterogeneityCheck($pword) {
        $output = false;

        //Counts how many lowercase, uppercase, and digits are in the password
        $ucase = 0;
        $lcase = 0;
        $digit = 0;
        $special = 0;
        $j = strlen($pword);
        for ($i = 0; $i < $j; $i++) {
            $char = substr($pword, $i, 1);
            if (preg_match('/^[[:upper:]]$/', $char)) {
                $ucase++;
            } elseif (preg_match('/^[[:lower:]]$/', $char)) {
                $lcase++;
            } elseif (preg_match('/^[[:digit:]]$/', $char)) {
                $digit++;
            } else {
                $special++;
            }
        }

        //No character type should dominate
        $maximum = ($j <= 3) ? $j : ($j-3);
        if (($ucase >= $maximum) || ($lcase >= $maximum) || ($digit >= $maximum) || ($special >= $maximum)) {
            $this->errorMsgArray[] = ERR_HETER;
            return $output;
        }

        return true;
    }

    /**
    * Compares a password with a user name to check whether they are similar sounding
    * @return bool
    * @param string $pass Password string
    * @param string $name User name string
    * @access private
    */
    function phoneticCheck($pass, $name) {
        $pass = soundex($pass);
        $name = soundex($name);
        $lev = levenshtein($name, $pass);
        if ($lev > 2) {
            return true;
        } else {
            $this->errorMsgArray[] = ERR_PHONETIC;
            return false;
        }
    }

    /**
    * Compares a password with a user name to check whether they are similar
    * @return bool
    * @param string $pass Password string
    * @param string $name User name string
    * @access private
    */
    function similarityCheck($pass, $name) {
        $p = '';
        similar_text($pass, $name, $p);
        if ($p < 50) {
            return true;
        } else {
            $this->errorMsgArray[] = ERR_SIMILAR;
            return false;
        }
    }

    /**
    * Compares a password with a user name to check whether they are superset/subset of one another
    * @return bool
    * @param string $pass Password string
    * @param string $name User name string
    * @access private
    */
    function stringCheck($pass, $name) {
        $str_bigger = (strlen($name)>=strlen($pass)) ? $name : $pass;
        $str_smaller = ($name == $str_bigger) ? $pass : $name;

        if (stristr($str_bigger, $str_smaller) || stristr($str_bigger, strrev($str_smaller))) {
            $this->errorMsgArray[] = ERR_SIMILAR;
            return false;
        } else {
            return true;
        }
    }

    /**
    * Checks if the word (or its reversed form) is one of the blacklisted words
    * @return bool
    * @param string $word The word to be checked
    * @access private
    */
    function blackWordCheck($word) {
        if (empty($word)) {
            return false;
        }

        if (is_readable(($this->blackWordList))) {
            if ($this->wordCheck($word, $this->blackWordList)) {
                return true;
            } else {
                $this->errorMsgArray[] = ERR_BLACKWORD;
                return false;
            }
        } else {
            die(ERR_CONFIG_BLACKWORD);
        }
    }

    /**
    * Checks if the word contains one of the blacklisted characters
    * @return bool
    * @param string $word The word to be checked
    * @access private
    */
    function blackCharCheck($word) {
        if (empty($word)) return false;

        if ($fp = fopen($this->blackCharList, 'r')) {
            $found = false;
            while (!($found || feof($fp))) {
                $blackChar = trim(fgets($fp));
                if (empty($blackChar)) {
                    continue;
                }
                if (stristr($word, $blackChar)) {
                    $found = true;
                }
            }
            fclose($fp);

            if ($found) {
                $this->errorMsgArray[] = ERR_BLACKCHAR;
                return false;
            }
            else {
                return true;
            }
        } else {
            die(ERR_CONFIG_BLACKCHAR);
        }
    }

    /**
    * Checks if the word (or its reversed form) is one of the dictionary words
    * @return bool
    * @param string $password The word to be checked
    * @access private
    */
    function dictionaryCheck($password) 
    {
        if (empty($this->dictionary))
        {
            return true;    
        }
        
        if (empty($password)) {
            return false;
        }

        if (is_readable(($this->dictionary))) {
            if ($this->wordCheck($password, $this->dictionary)) {
                return true;
            } else {
                $this->errorMsgArray[] = ERR_DICT;
                return false;
            }
        } else {
            die(ERR_CONFIG_DICT);
        }
    }

    /**
    * Checks the given word (and its reversed form) and checks a file for a match. Uses grep.
    * @return bool
    * @param string $word The word to be checked
    * @param string $file The word file
    * @access private
    */
    function wordCheck($word, $file) {
        $lc_word = $this->escapeSpecial(strtolower(trim($word)));
        $rev_word = $this->escapeSpecial(strrev($lc_word));

        $lc_word = $this->convertSpecial($lc_word);
        $rev_word = $this->convertSpecial($rev_word);

        $file = ($file);

        $cmd = 'grep -i -x "'.$lc_word.'\\|'.$rev_word.'" '.$file;
        $fp = popen($cmd, 'r');
        $buffer = fgetss($fp, 4096);
        pclose($fp);

        if (!empty($buffer)) {
            return false;
        } else {
            return true;
        }
    }

    /**
    * Converts umlauts to ASCII counterparts
    * @return string
    * @param string $word The word to be converted
    * @access private
    */
    function convertSpecial($word) {
        foreach ($this->umlautArr as $actual => $converted) {
            $word = str_replace($actual, $converted, $word);
        }

        return $word;
    }

    /**
    * Escapes special characters to use with grep
    * @return string
    * @param string $word The word to be checked
    * @access private
    */
    function escapeSpecial($word) {
        $special_chars = array('?', '\\', '.', '[', ']', '^', '$');
        foreach ($special_chars as $special) {
            $word = str_replace($special, '\\' . $special, $word);
        }

        return $word;
    }

    /**
    * Checks the length of the given word against the minimum length
    * @return bool
    * @param string $word The word to be checked
    * @access private
    */
    function lengthCheck($word) {
        if (strlen($word) < $this->min_length) {
            $this->errorMsgArray[] = ERR_LENGTH;
            return false;
        } else {
            return true;
        }
    }

    /**
    * Filters a character array by comparing with the blacklisted characters
    * @return array
    * @param $char_arr The array of characters to be filtered
    * @access private
    */
    function deductBlackChar($char_arr) {
        if ($fp = fopen($this->blackCharList, 'r')) {
            $black_char_arr = array();
            while (!feof($fp)) {
                $black_char = trim(fgets($fp));
                $black_char_arr[] = strtoupper($black_char);
                $black_char_arr[] = strtolower($black_char);
            }
            fclose($fp);
        }

        return array_diff($char_arr, $black_char_arr);
    }

    /**
    * Tests a file and sets the relevant class variable to the file path.
    * phpdoc may not work correctly here.
    * @return bool
    * @param reference &$var Class variable
    * @param string $file Path to the given file
    * @access private
    */
    function setVar(&$var, $file = null) {
        if (!empty($file) && $this->testFile($file)) {
            $var = $file;
            return true;
        } else {
            if ($this->testFile($var)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
    * Tests a file for validity
    * @return bool
    * @param string $file Path to the given file
    * @access private
    */
    function testFile($file) {
        clearstatcache();
        if (is_readable($file)) {
            return true;
        } else {
            return false;
        }
    }
}
