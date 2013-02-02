<?php
/**
 * Dbg: Debug library for PHP
 * 
 * @name      Dbg
 * @category  Seagoj
 * @package   Devtools
 * @author    Jeremy Seago <seagoj@gmail.com>
 * @copyright 2012 Jeremy Seago
 * @license   http://opensource.org/licenses/mit-license.php, MIT
 * @version   1.0
 * @link      https://github.com/seagoj/devtools
 *
 * @TODO	Comment dbg class in accordance to PHPDoc standard
 * @TODO	Looking into adding detailed info to message on test() failure
 * @TODO	Adjust server level error reporting
 * @TODO	Breakpoints
 * @TODO	Add ability to silence output except for unit tests and failures
 */
namespace Devtools;

/**
 * dbg Class
 * Library of debug tools for PHP development
 *
 * @category   Seagoj
 * @package    Devtools
 * @subpackage Dbg
 * @author     Jeremy Seago	<seagoj@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php, MIT
 * @version    1.0
 * @link       https://github.com/seagoj/devtools
 */
class Dbg extends RandData
{
    protected $commentTags;
    private $_config;

    /**
     * public Dbg::__construct
     *
     * Constructor for Dbg class
     *
     * @param string $class Class to debug
     *
     * @return void
     */
    public function __construct($class)
    {
        $this->_config = new \Devtools\Config($class);
        $this->commentTags = array();
        $this->setNoCache();
    }

    /**
     * public Dbg::msg
     *
     * Display messages for debuging purposes
     *
     * @param string $message   Message to display
     * @param bool   $die       True: die() on message
     * @param string $method    Method name
     * @param bool   $exception True: throw an exception
     * @param string $file      Filename
     * @param int    $line      Line number
     *
     * @return void
     */
    public function msg($message, $die=false, $method='', $exception=false, $file='', $line='')
    {
        if ($this->_config->debug) {
            return dbg::dump($message, $die, $method, $exception, $file, $line, false);
        }
    }

    /**
     * public Dbg::dump
     *
     * Dump messages for debuging purposes
     *
     * @param multi  $var       Variable to be dumped
     * @param bool   $die       True: die() on message
     * @param string $label     Message to display before dump
     * @param bool   $exception True: throw an exception
     * @param string $file      Filename
     * @param int    $line      Line number
     * @param bool   $export    True: export instead of dump
     * 
     * @return string Generated output
     */
    public function dump($var, $die=true, $label='', $exception=false, $file='', $line='', $export=true)
    {
        
        if ($this->_config->debug) {
            if ($export) {
                $var = var_export($var, true);
            }
            $output = "<div class='err'>";
            $label=='' ? print '' : $output .= "<span class='errLabel'>$label</span>: ";
            $output .= "<span class='errDesc'>$var";
            $file=='' ? print '' : $output .= " in file $file";
            $line=='' ? print '' : $output .= " on line $line";
            $output .= "</span></div>";
            print $output;

            if ($exception) {
                throw new Exception($var);
            }

            if($die)
                die();
            else
                return $output;
        }
    }

    /**
     * public Dbg::test() 
     *
     * Tests statements and passes or fails accordingly
     *
     * @param bool   $term    Term to be evaluated
     * @param string $failMsg Message to be displayed upon failure
     * @param bool   $die     True: die upon failure
     * 
     * @return bool Success
     */
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

    /**
     * public Dbg::setNoCache()
     *
     * Prints meta tag to disable caching
     *
     * @return void
     */
    public function setNoCache ()
    {
        
        print "<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>\n<META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>";
    }

    /**
     * Dbg::randData()
     *
     * returns a random value of the passed type
     *
     * @param string $type Type of data to return
     * 
     * @return multi
     */
    public function randData($type)
    {	
        
        $data = new \Devtools\RandData();
        return $data->get($type);
    }

    /**
     * Dbg::unit()
     *
     * returns true if all tests pass; false with error on failure
     *
     * @param object $object Object of class to be tested
     * @param string $method Method to test in class $object
     * @param array  $params Array of parameters to pass to $method
     * 
     * @return bool
     */
    public function unit ($object, $method, $params)
    {

        $validOutput = $this->_config->test;
        $class = get_class($object);

        $output = $validOutput[$class.'\\'.$method];

        for ($i=0; $i<count($params); $i++) {
            $output = str_replace("#{".$i."}", $params[$i], $output);
        }

        $paramStr = implode(', ', $params);

        ob_start();
        $result = call_user_func_array(array($object, $func), $params);
        if($result == null)
            $result = ob_get_contents();
        ob_end_clean();

        if ($output==$result) {
            print "<div>$class->$method($paramStr) test passed.</div>";
            return true;
        } else {
            print "<div>$class->$method($paramStr) test failed.</div><div>\n\n$output\n\n</div><div>\n\n$result\n\n</div>";
            return false;
        }
    }
}
