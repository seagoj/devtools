<?php
/**
 * Markdown translator
 * 
 * @category Seagoj
 * @package  Devtools
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://github.com/seagoj/Devtools/LICENSE MIT
 * @link     http://github.com/seagoj/Devtools
 **/

namespace Devtools;
/**
 * Class Markdown
 *
 * @category Seagoj
 * @package  Markdown
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://github.com/seagoj/Devtools/LICENSE MIT
 * @link     http://github.com/seagoj/Devtools
 */
class Markdown
{
    private $_log;
    /**
     * Markdown::__construct()
     *
     * Constructor for Markdown class
     *
     * @return void
     **/
    public function __construct()
    {
        $options = array('type'=>'stdout');
        $this->_log = new \Devtools\Log($options);
    }

    /**
     * Markdown::__convert()
     *
     * Prints the body of the portfolio
     *
     * @param string $file Filename of the file to be converted
     *
     * @return void
     **/
    public function convert($input)
    {
        if(is_file($input))
            $code = file_get_contents($input);
        else
            $code = $input;

        $first = true;

        $html = "";

        foreach (explode("\n", $code) AS $line) {

            if ($line!="") {

                $tag = substr($line, 0, strpos($line, ' '));
                $string = substr($line, strpos($line, ' ')+1);

                // Check for header
                if ($line[$depth = 0]=='#') {
                    while ( $line[$depth]=='#' ) {
                        $depth++;
                    }
                    $tag = "h".$depth;
                    $line = "<$tag>$string</$tag>";
                } else if ( $line[0]=='*' && $line[1]==' ') {
                    // Check for unordered list
                    if ($first) {
                        $first = false;

                    $line = "<ul>\n<li>$string</li>";
                    } else {
                        $line = "<li>$string</li>";
                    }
                } else if (substr($line, 0, 333=='---') {
                    $line = "<hr>\n";
                }

                $line = $this->_tagReplace(
                    $this->_tagReplace($line, 'b', '**'),
                    'i','*'
                );

                $html .= $line."\n";
            } else {
                if (!$first) {
                    $html .= "</ul>\n";
                    $first = true;
                }
            }
        }
        return $html;
    }

    private function _tagReplace($line, $tag, $start, $end=null)
    {
        $string = '';

        if($start===$end || $end===null) {
            if (strpos($line, $start)!==false) {
                $array = explode($start, $line);
                for ($i=0; $i<count($array); $i++) {
                    if ($i%2===0) {
                        $string .= $array[$i];
                    } else {
                        $string .= "<$tag>".$array[$i]."</$tag>";
                    }
                }
            } else {
//                throw new \Exception("$start not found in $line");
                $string = $line;
            }

        } else {
            throw new \Exception("$start does not equal $end");
        }

        return $string;
    }
}
