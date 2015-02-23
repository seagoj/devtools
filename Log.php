<?php namespace Devtools;

class Log extends BaseObserver
{
    public $first;
    public $type;
    protected $timestamp;
    private $testCount;
    private $config;

    public function __construct($options = array())
    {
        error_reporting(-1);
        ini_set('display_errors', 'On');
        set_exception_handler(array('Devtools\Log', 'exception_handler'));
        set_error_handler(array('Devtools\Log', 'error_handler'));

        $defaults = array(
            'type' => 'stdout',
            'file' => 'Log.log',
            'format' => 'tap'
        );

        $this->config = array_merge($defaults, $options);
        $this->testCount = 0;
        $this->first = true;
    }

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

    private function file($content)
    {
        return file_put_contents($this->config['file'], $content.PHP_EOL, FILE_APPEND);
    }

    private function htmlify($content, $result)
    {
        $tag = 'div';

        return is_null($result) ?
            "<$tag>$content</$tag>" :
            "<$tag>$result: $content</$tag>";
    }

    private function stdout($content)
    {
        print $content.PHP_EOL;
    }

    private function stringify($content)
    {
        return (is_array($content) || is_object($content)) ?
            serialize($content) :
            $content;
    }

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
        self::output(
            $e->getMessage() . "\n"
            . self::getExceptionTraceAsString($e)
        );
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
                    } elseif (is_object($arg) || is_array($arg)) {
                        try {
                            $args[] = serialize($arg);
                        } catch (\PDOException $e) {
                            $args[] = var_export($arg, true);
                        }
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

            $rtn .= sprintf(
                "#%s %s(%s): %s(%s)\n",
                $count,
                $frame['file'],
                $frame['line'],
                $frame['function'],
                $args
            );
            $count++;
        }
        return $rtn;
    }

    private static function output($msg)
    {
        global $errorLog;

        if (isset($errorLog) && get_class($errorLog) === 'Devtools\Log') {
            $errorLog->write($msg, false);
        } else {
            echo $msg;
        }
    }

    public static function debugLog($path = '/home/www/Debug.log')
    {
        return self::newLog(
            array(
                'name' => 'debugLog',
                'path' => $path
            )
        );
    }

    public static function errorLog($path = '/home/www/Error.log')
    {
        return self::newLog(
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
        return isset($$name)
            ? $$name
            : new self(
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
        assert_options(
            ASSERT_CALLBACK,
            function ($script, $line, $message) {
                $this->write("$script:$line $message");
            }
        );
        assert($term);
    }

    public function __get($property)
    {
        return $this->$property;
    }
}

