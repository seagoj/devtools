<?php namespace Devtools;

use ErrorException;

abstract class Logger extends BaseObserver
{
    public function __construct()
    {
        error_reporting(-1);
        ini_set('display_errors', 'On');
        set_exception_handler(
            array($this, 'exception_handler')
        );
        set_error_handler(
            array($this, 'error_handler')
        );
    }

    public abstract function write(
        $content, $result = null
    );

    public static function exception_handler($e)
    {
        self::output(
            $e->getMessage() . "\n" . self::getExceptionTraceAsString($e)
        );
    }

    public static function error_handler($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
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
}
