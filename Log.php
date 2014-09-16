<?php namespace Devtools;
/**
 * Logger for PHP
 *
 * Log to file or stdout in various formats (TAP, HTML, plaintext)
 *
 * @category Seagoj
 * @package  Devtools
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://github.com/seagoj/Devtools/LICENSE MIT
 * @link     http://github.com/seagoj/Devtools
 **/

/**
 * Class Log
 *
 * @category Seagoj
 * @package  Devtools
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://github.com/seagoj/Devtools/LICENSE MIT
 * @link     http://github.com/seagoj/Devtools
 **/
class Log
{
    /**
     * Number of tests passed to the logger
     *
     * Stores the number of tests passed to the logger; used in TAP output
     **/
    private $testCount;
    /**
     * Configuration array for the class
     *
     * Sets the type of log, file location (if necessary), and format of output
     **/
    private $config;
    /**
     * Type of log to write
     *
     * Stores the type of log to be written
     **/
    public $type;

    /**
     * Determines if current message is the first message
     *
     * True if yes; false if no
     **/
    public $first;

    protected $timestamp;

    /**
     * Log::__construct
     *
     * Initializes this.config, writes log header and sets testCount to 0
     *
     * @param array $options Options array to configure the log object
     *
     * @return void
     *
     * @todo validate $options array
     **/
    public function __construct($options = array())
    {
        error_reporting(-1);
        ini_set('display_errors', 'On');
        set_exception_handler(array('Devtools\Log', 'exception_handler'));
        set_error_handler(array('Devtools\Log','error_handler'));

        $defaults = array(
            'type' => 'stdout',
            'file' => 'Log.log',
            'format' => 'tap'
        );

        $this->config = array_merge($defaults, $options);
        $this->testCount = 0;
        $this->first = true;
    }

    /**
     * Log::write
     *
     * Writes content and tests to the log
     *
     * @param string  $content Message to be written to the log
     * @param boolean $result  Result of test; defaults to 'EMPTY';
     *
     * @return void
     **/
    public function write($content, $result = null)
    {
        $content = $this->stringify($content);

        switch ($this->config['format']) {
            case 'tap':
                $content = $this->tapify($content, $result);
                break;
            case 'html':
                $content = $this->htmlify($content, $result);
                break;
            default:
                throw new \InvalidArgumentException($this->config['format'].' is not a valid log format.');
        }

        switch ($this->config['type']) {
            case 'file':
                $this->file($content);
                break;
            case 'stdout':
                $this->stdout($content);
                break;
            default:
                throw new \InvalidArgumentException($this->config['type'].' is not a valid Log type');
        }
        return true;
    }

    public function getTimestamp()
    {
        return (string)$this->timestamp;
    }

    /**
     * Log::file
     *
     * Writes content to log file
     *
     * @param string $content String to be written to file
     *
     * @return integer Result of writing to file
     **/
    private function file($content)
    {
        return file_put_contents($this->config['file'], $content.PHP_EOL, FILE_APPEND);
    }

    /**
     * Log::htmlify
     *
     * Formats content as HTML
     *
     * @param string    $content    String to be formatted
     * @param boolean   $result     Result of the test
     *
     * @return string
     **/
    private function htmlify($content, $result)
    {
        $tag = 'div';

        return is_null($result) ?
            "<$tag>$content</$tag>" :
            "<$tag>$result: $content</$tag>";
    }

    /**
     * Log::stdout
     *
     * Outputs log to stdout
     *
     * @param string $content String to be output to stdout
     *
     * @return void
     **/
    private function stdout($content)
    {
        print $content.PHP_EOL;
    }

    /**
     * Log::stringify
     *
     * Turns $content into string through serialization if it is another type
     *
     * @param string $content Contents to be turned into a string
     *
     * @return string Serialization of $content
     **/
    private function stringify($content)
    {
        return (is_array($content) || is_object($content)) ?
            serialize($content) :
            $content;
    }

    /**
     * Log::tapify
     *
     * Formats $content as TAP output based on the value of $result
     *
     * @param string  $content Content of output
     * @param boolean $result  Result of test
     *
     * @return string Content formatted as TAP output
     **/
    private function tapify($content, $result)
    {

        $nextTest = $this->testCount+1;
        $prefix = 'ok '.$nextTest.' - ';

        if (!is_null($result)) {
                $this->testCount = $nextTest;
                $content = $prefix.$content;
            if (!$result) {
                $content = 'not '.$content;
            }
        }

        if ($this->first) {
            date_default_timezone_set('America/Chicago');
            $this->timestamp = date("m-d-Y H:i:s");
            $content = $this->timestamp.PHP_EOL.$content;
            $this->first=false;
        }

        return $content;
    }

    /**
     * Log::__destruct
     *
     * Writes footer to Log upon close
     *
     * @return void
     **/
    public function __destruct()
    {
        if (!is_null($this->config)) {
            if ($this->testCount !== 0) {
                $footers = array(
                    'tap' => '1..'.$this->testCount.PHP_EOL
                );
                $this->write($footers[$this->config['format']]);
            }
        }
    }

    public static function consoleLog($var)
    {
        print '<script>console.log('.json_encode($var).');</script>';
    }

    public static function exception_handler($e)
    {
        \Devtools\Log::output($e->getMessage()."\n".\Devtools\Log::getExceptionTraceAsString($e));
    }

    public static function error_handler($errno, $errstr, $errfile, $errline)
    {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    private static function getExceptionTraceAsString($exception)
    {
        $rtn = "";
        $count = 0;
        foreach ($exception->getTrace() as $frame) {
            $args = "";
            if (isset($frame['args'])) {
                $args = array();
                foreach ($frame['args'] as $arg) {
                    if (is_string($arg)) {
                        $args[] = "'" . $arg . "'";
                    } elseif (is_null($arg)) {
                        $args[] = 'NULL';
                    } elseif (is_bool($arg)) {
                        $args[] = ($arg) ? "true" : "false";
                    } elseif (is_object($arg)) {
                        $args[] = serialize($arg);
                    } elseif (is_array($arg)) {
                        $args[] = serialize($arg);
                    } elseif (is_resource($arg)) {
                        $args[] = get_resource_type($arg);
                    } else {
                        $args[] = $arg;
                    }
                }
                $args = join(", ", $args);
            }

            foreach (array('file', 'line', 'function') as $type) {
                $frame[$type] = isset($frame[$type]) ? $frame[$type] : '';
            }

            $rtn .= sprintf( "#%s %s(%s): %s(%s)\n",
                $count,
                $frame['file'],
                $frame['line'],
                $frame['function'],
                $args );
            $count++;
        }
        return $rtn;
    }

    /**
     * @param string $msg
     */
    private static function output($msg)
    {
        global $errorLog;

        if (isset($errorLog) && get_class($errorLog)==='Devtools\Log') {
            $errorLog->write($msg, false);
        } else {
            echo $msg;
        }
    }

    public static function debugLog($path = '/home/www/Debug.log')
    {
        return \Devtools\Log::newLog(
            array(
                'name' => 'debugLog',
                'path' => $path
            )
        );
    }

    public static function errorLog($path = '/home/www/Error.log')
    {
        return \Devtools\Log::newLog(
            array(
                'name' => 'errorLog',
                'path' => $path
            )
        );
    }

    private static function newLog($log)
    {
        $name = $log['name'];
        $path = $log['path'];

         global $$name;
         return isset($$name) ?
            $$name :
             new \Devtools\Log(
             array(
                 'type'  => 'file',
                 'file'  => $path
             )
         );
    }

    public function assert($term)
    {
        assert_options(ASSERT_ACTIVE, true);
        assert_options(ASSERT_WARNING, true);
        assert_options(ASSERT_BAIL, false);
        assert_options(ASSERT_QUIET_EVAL, false);
        assert_options(ASSERT_CALLBACK, function($script, $line, $message) {
            $this->write("$script:$line $message");
        });
        assert($term);
    }

    public function __get($property) {
        return $this->$property;
    }
}

