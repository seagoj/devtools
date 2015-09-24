<?php namespace Devtools;

use ErrorException;

abstract class Logger extends Observer\BaseObserver
{
    public function __construct()
    {
        error_reporting(-1);
        set_exception_handler(
            array($this, 'exceptionHandler')
        );
        set_error_handler(
            array($this, 'errorHandler')
        );
    }

    public abstract function write(
        $content, $result = null
    );

    public function exceptionHandler($e)
    {
        $this->write(
            $e->getMessage() . "\n" . self::getExceptionTraceAsString($e)
        );
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
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

    public function update(\SplSubject $subject)
    {
        $status = $subject->getStatus();
        if (!in_array(gettype($status), array("object", "resource"))
        ) {
            $this->write($status);
        }

        if (is_a($status, 'Devtools\LogEntry')) {
            $this->write(
                $status->message,
                $status->result
            );
        }
    }
}
