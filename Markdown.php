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
        $options = array('file'=>__CLASS__.'.log');
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
        $UL = $CODE = false;
        $html = "";

        foreach (explode("\n", $code) AS $line) {

            if ($line!="") {

                if(strpos($line, "# ")!==false)
                    $line = $this->_checkHeader($line);

                if(strpos($line, "* ")!==false) {
                    $UL = true;
                    $line = $this->_checkUnorderedList($line, $first);
                    $first = false;
                }

                if(strpos($line, '---')!==false)
                    $line = $this->_checkHR($line);

                if(strpos($line, '    ')!==false) {
                    $CODE = true;
                    $line = $this->_formatCode($line, $first);
                    $first = false;
                }

                if(strpos($line, '**')!==false)
                    $line = $this->_tagReplace($line, 'b', '**');

                if(strpos($line, '*')!==false)
                    $line = $this->_tagReplace($line, 'i', '*');

                $html .= $line."\n";
            } else {
                if (!$first) {
                    if($UL) {
                        $html .= "</ul>\n";
                        $UL = false;
                    }
                    if($CODE) {
                        $html .= "</code>\n";
                        $CODE = false;
                    }
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
            if (($startLoc = strpos($line, $start))!==false) {
                $begin = $startLoc+strlen($start);
                // $end = strpos($line, "\n", $begin);
                $string = "<$tag>".substr($line, $begin)."</$tag>";
            } else {
                $string = $line;    
            }
            // throw new \Exception("$start does not equal $end");
        }

        return $string;
    }

    private function _checkHR($line)
    {
        if(substr($line, 0, 3)==='---')
            $line = "<hr>\n";
        return $line;
        
    }

    private function _checkHeader($line)
    {
        if ($line[$depth = 0]=='#') {
            while ( $line[$depth]=='#' ) {
                $depth++;
            }
            $tag = "h".$depth;
            $string = substr($line, strpos($line, ' ')+1);
            $line = "<$tag>$string</$tag>";
        }

        return $line;
    }

    private function _checkUnorderedList($line, $first)
    {
        $string = substr($line, strpos($line, ' ')+1);
        if ( $line[0] =='*' && $line[1]==' ') {
            if ($first) {
                $line = "<ul>\n<li>$string</li>";
            } else {
                $line = "<li>$string</li>";
            }
        }

        return $line;
    }

    private function _formatCode($line, $first)
    {
        $string = substr($line, 5);
        if( substr($line, 0, 4)==='    ') {
            if($first)
                $line = "<code>$string";
            else
                $line=$string;
        }

        return $line;
    }
}
