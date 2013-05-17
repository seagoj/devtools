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
        $closeTag = null;
        $html = "";

        foreach (explode("\n", $code) AS $line) {

            if ($line!="") {
                if(strpos($line, "> ")!==false) {
                    $closeTag = 'blockquote';
                    $line = $this->_formatBlockquote($line, $first);
                    $first = false;
                }

                if(strpos($line, "# ")!==false)
                    $line = $this->_formatHeader($line);

                if(strpos($line, "* ")!==false) {
                    $closeTag = 'ul';
                    $line = $this->_formatUnorderedList($line, $first);
                    $first = false;
                }

                if(strpos($line, '---')!==false)
                    $line = $this->_formatHR($line);

                if(strpos($line, '    ')!==false) {
                    $closeTag = 'code';
                    $line = $this->_formatCode($line, $first);
                    $first = false;
                }

                if(strpos($line, '**')!==false)
                    $line = $this->_tagReplace($line, 'b', '**');

                if(strpos($line, '*')!==false)
                    $line = $this->_tagReplace($line, 'i', '*');

                $html .= $line."\n";
            } else {
                if ($closeTag!==null) {
                    $html .= "</$closeTag>\n";
                    $closeTag = null;
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
                // throw new \Exception("$start not found in $line");
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

    private function _formatHR($line)
    {
        if(substr($line, 0, 3)==='---')
            $line = "<hr>";
        return $line;
        
    }

    private function _formatHeader($line)
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

    private function _formatUnorderedList($line, $first)
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
        $string = substr($line, 4);
        if( substr($line, 0, 4)==='    ') {
            if($first)
                $line = "<code>$string";
            else
                $line=$string;
        }

        return $line;
    }

    private function _formatBlockquote($line, $first)
    {
        $string = substr($line, 2);
        if (substr($line, 0, 2)==='> ') {
            if($first)
                $line = "<blockquote>\n\t$string";
            else
                $line = "\t".$string;
        }

        return $line;
    }
}
