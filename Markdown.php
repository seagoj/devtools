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

                // Check for hash
                if ($line[$depth = 0]=='#') {
                    while ( $line[$depth]=='#' ) {
                        $depth++;
                    }
                    $tag = "h".$depth;
                    $string = "<$tag>$string</$tag>";
                } else {
                    // Check for unmatched star
                    if ( $line[0]=='*' && $line[1]==' ') {
                        if ($first) {
                            $first = false;

                            $string = "<ul>\n\t<li>$string</li>";
                        } else {
                            $string = "\t<li>$string</li>";
                        }
                    }
                }

                $this->_tagReplace($line, 'b', '**');
                $this->_tagReplace($line, 'i', '*');

                $html .= $string."\n";
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
        if($start===$end || $end===null) {
            if (strpos($line, $start)) {
                $array = explode($start, $line);
                $this->_log->write($array);
                $string = '';
                for ($i=0; $i<count($array); $i++) {
                    if ($i%2===0) {
                        $string .= $array[$i];
                    } else {
                        $string .= "<$tag>".$array[$i]."</$tag>";
                    }
                }
            }
        } else {
            
            
        }
    }
}
