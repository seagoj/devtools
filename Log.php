<?php
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

namespace Devtools;

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
    public function __construct($options = [])
    {
        $defaults = [
            'type' => 'stdout',
            'file' => 'Log.log',
            'format' => 'tap'
        ];

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
                break;
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
                break;
        }
    }

    /**
     * Log::file
     *
     * Writes content to log file
     *
     * @param string $content String to be written to file
     *
     * @return boolean Result of writing to file
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
     * @return void
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
        return is_array($content) ?
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
            $content = date("m-d-Y H:i:s").PHP_EOL.$content;
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
        if ($this->testCount !== 0) {
            $footers = array(
                'tap'=>'1..'.$this->testCount.PHP_EOL
            );

            $this->write($footers[$this->config['format']]);
        }
    }
}
