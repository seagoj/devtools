<?php
/**
 * Lib.dbg: Debug library for PHP
 * 
 * @name		lib.dbg
 * @package		seago\devtools
 * @author 		Jeremy Seago <seagoj@gmail.com>
 * @copyright 	Copyright (c) 2012, Jeremy Seago
 * @license		http://opensource.org/licenses/mit-license.php
 * @version 	1.0
 * @link		https://github.com/seagoj/lib.dbg
 *
 * @TODO	Comment dbg class in accordance to PHPDoc standard
 * @TODO	Looking into adding detailed info to message on test() failure
 * @TODO	Adjust server level error reporting
 * @TODO	Breakpoints
 * @TODO	Add ability to silence output except for unit tests and failures
 */
namespace seagoj\devtools;
//require_once('paths.php');
//require_once('../lib/randData/src/randData.php');
/**
 * dbg Class
 * Library of debug tools for PHP development
 *
 * @package		seago\devtools
 * @subpackage	dbg
 * @author		Jeremy Seago	<seagoj@gmail.com>
 * @version		1.0
 * @access		public
 * @public		__construct()
 * @public		msg(string,[bool],[string],[bool],[string],[int])
 * @public		dump(string,[bool],[string],[bool],[string],[int],[bool])
 * @public		test(bool,[bool])
 * @public		setNoCache();
 * @public		randData(string)
 */
class dbg extends randData
{
	protected $commentTags;
	private $config;
	
    public function __construct($class)
    {
    	//print get_class($obj);
    	$this->config = new config($class);
    	$this->commentTags = array();
        $this->setNoCache();
    }
    public function msg($message, $die=false, $method='', $exception=false, $file='', $line='')
    {
    	if($this->config->debug) {
        	return dbg::dump($message, $die, $method, $exception, $file, $line, false);
    	}
    }
    public function dump($var, $die=true, $label='', $exception=false, $file='', $line='', $export=true)
    {
        if($this->config->debug) {
    		if($export)
            	$var = var_export($var,true);
        	$output = "<div class='err'>";
        	$label=='' ? print '' : $output .= "<span class='errLabel'>$label</span>: ";
        	$output .= "<span class='errDesc'>$var";
        	$file=='' ? print '' : $output .= "in file $file";
        	$line=='' ? print '' : $output .= "on line $line";
        	$output .= "</span></div>";

        
        	print $output;

        	if($exception) throw new Exception ($var);

        	if($die)
            	die();
        	else
        		return $output;
        }
    }
    public function test($term, $failMsg='', $die=true)
    {
        assert_options(ASSERT_ACTIVE, true);
        assert_options(ASSERT_WARNING, false);
        assert_options(ASSERT_BAIL, false);
        assert_options(ASSERT_QUIET_EVAL, false);

        if (assert($term)) {
                //dbg::msg($msg);
            return true;
        } else {
                dbg::msg("assertion failed: $failMsg");
            if($die)
                 die("Assertion failed");
            else
                return false;
        }
    }
    public function setNoCache ()
    {
        print "<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>\n<META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>";
    }
    public function randData($type)
    {	
    	$data = new randData();
    	return $data->get($type);
    }
}