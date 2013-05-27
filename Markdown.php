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
 *
 * Converts markdown to html in the following flavors:
 *  Standard: http:// daringfireball.net/projects/markdown/syntax
 *  GitHub: https://help.github.com/articles/github-flavored-markdown
 * 
 */
class Markdown
{
    private $_config;
    private $_log;
    private $_code;
    /**
     * Markdown::__construct()
     *
     * Constructor for Markdown class
     *
     * @return void
     **/
    public function __construct($options=[])
    {
        $defaults = [
            'flavor'=>'standard',
            'logType'=>'stdout'
        ];

        $this->_config = array_merge($defaults, $options);

        $this->_validateConfig();

        $logOptions = array('type'=>$this->_config['logType']);
        $this->_log = new \Devtools\Log($logOptions);
    }

    /**
     * Markdown::_validateConfig()
     *
     * Validates Configuration
     *
     * @return void
     **/
    private function _validateConfig()
    {
        $valid = [ 
            'flavor'=>[
                'standard',
                'github'
            ],
            'logType'=>[
                'stdout',
            ]
        ];

        foreach ($this->_config as $var=>$value)
        {
            if (!array_key_exists($var, $valid))
                throw new \Exception("$var is not a valid option.");
            else if (!in_array($value, $valid[$var]))
                throw new \Exception("$value is not a valid value for $var.");
            else
                return true;
        }
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

        $this->_code = explode("\n", $code);

        $this->_formatInline();
        $this->_formatHeader();
        $this->_formatUnorderedList();
        $this->_formatOrderedList();
        $this->_formatHR();
        $this->_formatCode();
        $this->_formatBlockquote();
        $this->_formatImage();
        $this->_formatLink();

        $this->_formatParagraph();

        $html = '';
        $previous = null;
        foreach ($this->_code AS $line) {
            if( $line!='' || $previous != '')
                $html .= $line."\n";
            $previous = $line;
        }

        return $html;
    }

    private function _formatParagraph()
    {
        $headers = array();

        $rootElements = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', '/ul',
            'ol', '/ol',
            'li',
            'hr',
            'code', '/code',
            'blockquote', '/blockquote'
        );

        $blockElements = array('code', 'blockquote');

        $result = array();
        $first = true;
        $block = false;
        foreach ($this->_code as $line) {
            if ($line!=='' && $line[0]==='<') {
                $end = strpos($line, '>')-1;
                if (in_array(substr($line, 1, $end), $rootElements)) {
                    if(in_array(substr($line, 1, $end), $blockElements))
                        $block=true;
                    else if(in_array(substr($line, 2, $end), $blockElements))
                        $block=false;

                    if (!$first) {
                        array_push($result, "</p>");
                        $first = true;
                    }
                    array_push($result, $line);
                } else {
                    if ($first) {
                        array_push($result, "<p>");
                        $first = false;
                    }
                    array_push($result, $line);
                }
            } else if ($line!=='') {
                if ($first && !$block) {
                    array_push($result, "<p>");
                    $first = false;
                }
                array_push($result, $line);
            } else if (!$first) {
                array_push($result, "</p>");
                array_push($result, $line);
                $first = true;
            }

            if (!$first) {
                array_push($result, "</p>");
                $first = true;
            }
        }

        $this->_code = $result;
    }

    private function _tagReplace($line, $tag, $startTag, $endTag=null)
    {
        if ($startTag===$endTag || $endTag===null) {
            $begin = strpos($line, $startTag);
            $end = strpos($line, $startTag, $begin)-strlen($startTag);
            if ($begin!==false && $end!==false) {
                $line = str_replace(" $startTag", " <$tag>", $line);
                $line = str_replace("$startTag ", "</$tag> ", $line);
            }

            // Check for leading tag
            if(substr($line, 0, strlen($startTag))==$startTag)
                $line = "<$tag>".substr($line, strlen($startTag));

            // Check for ending tag
            if(substr($line, -strlen($startTag))==$startTag)
                $line = substr($line, 0, strlen($line)-strlen($startTag))."</$tag>";
        }

        return $line;
    }

    private function _textBetween($firstDelim, $secondDelim, $string)
    {
        if(($open = strpos($string, $firstDelim)) && $close = strpos($string,
            $secondDelim, ($textBegin = $open+strlen($firstDelim)))) {
                $textLength = $close-$textBegin;

                return substr($string, $textBegin, $textLength);
        } else {
            return false;
        }
    }

    private function _formatInline()
    {
        $syntaxMap = array(
            '`'=>'code',
            '**' => 'strong',
            '__' => 'strong',
            '*' => 'em',
            '_' => 'em'
        );
        $first = false;

        foreach ($syntaxMap AS $syntax=>$tag) {
            $result = array();
            foreach ($this->_code AS $line) {
                $first = strpos($line, $syntax);
                if ($first!==false) {
                    $second = strpos($line, $syntax, $first+strlen($syntax));
                    if ($second!==false) {
                        array_push($result, $this->_tagReplace($line, $tag, $syntax));
                    } else {
                        array_push($result, $line);
                    }
                } else {
                    array_push($result, $line);
                }
            }
            $this->_code = $result;
        }
    }

    private function _formatHR()
    {
        $result = array();
        foreach ($this->_code as $line) {
            if(substr($line, 0, 3)==='---')
                array_push($result, "<hr>");
            else
                array_push($result, $line);
        }

        $this->_code = $result;
    }

    private function _formatHeader()
    {
        $result = array();
        foreach ($this->_code AS $line) {

//            if ($line !='' && $line[$depth=0]=='#') {
            if (($start = strpos($line, '#'))!==false) {
                $depth = $start;
                while ( $line[$depth]=='#' )
                    $depth++;

                $depth = $depth-$start;
                $tag = "h".$depth;
                $prefix = substr($line, 0, $start);
                array_push($result, "$prefix<$tag>".substr($line, $start+$depth+1)."</$tag>");
            } else {
                array_push($result, $line);
            }
        }

        $this->_code = $result;
    }

    private function _formatUnorderedList()
    {
        $result = array();
        $first = true;
        $loc = null;
        $triggered = false;
        foreach ($this->_code AS $line) {
            if ($loc=strpos($line, "* ")!==false || strpos($line, "- ")!==false || strpos($line, "+ ")!==false) {
                $triggered = true;
                $li = substr($line, strpos($line, ' ')+1);
                if ($first === true) {
                    array_push($result, "<ul>\n<li>$li</li>");
                    $first = false;
                } else {
                    array_push($result, "<li>$li</li>");
                }
            } else {
                if ($triggered) {
                    array_push($result, "</ul>");
                    $triggered = false;
                }
                if($line!="\n")
                    array_push($result, $line);
            }
        }
        $this->_code = $result;
    }

    private function _formatOrderedList()
    {
        $result = array();
        $first = true;
        $triggered = false;
        foreach ($this->_code AS $line) {
            if ($pivot = strpos($line, '. ')!==false) {
                if (is_numeric(trim($prefix = substr($line, 0, $pivot)))) {
                    $triggered = true;
                    if ($first) {
                        array_push($result, "<ol>");
                        $first = false;
                    }
                    array_push($result, "<li>".substr($line, $pivot+2)."</li>");
                } else
                    array_push($result, $line);
            } else {
                if ($triggered) {
                    array_push($result, "</ol>");
                    $triggered = false;
                }
                if($line != "\n")
                    array_push($result, $line);
                $first = true;
            }
        }
        $this->_code = $result;
    }

    private function _formatCode()
    {
        $first = true;
        $result = array();
        $triggered = false;

        foreach ($this->_code as $line) {
            $string = substr($line, 4);
            if ( substr($line, 0, 4)==='    ') {
                $triggered = true;
                if ($first) {
                    array_push($result, "<code>");
                    $first = false;
                }
                array_push($result, "\t".$string);
            } else {
                if ($triggered) {
                    array_push($result, "</code>");
                }
                array_push($result, $line);
                $first = true;
            }
        }

        return $this->_code = $result;
    }

    private function _formatBlockquote()
    {
        $first = true;
        $result = array();
        $triggered = false;

        foreach ($this->_code as $line) {
            $string = substr($line, 2);
            if (substr($line, 0, 2)==='> ') {
                if ($first) {
                    array_push($result, "<blockquote>");
                    $first = false;
                }
                array_push($result, "    $string");
                $triggered = true;
            } else {
                if($triggered)
                    array_push($result, "</blockquote>");
                array_push($result, $line);
            }
        }

        return $this->_code = $result;
    }

    private function _formatLink()
    {
        $result = array();

        foreach ($this->_code as $line) {
            while(($squareOpen = strpos($line, '['))!==false &&
                ($squareClose = strpos($line, ']', ($textBegin = $squareOpen+1)))!==false &&
                ($parensOpen = strpos($line, '(', $squareClose))!==false &&
                ($parensClose = strpos($line, ')',($pathBegin = $parensOpen+1)))!==false
            ) {
                $text = substr($line, $textBegin, $squareClose-$textBegin);
                $path = substr($line, $pathBegin, $parensClose-$pathBegin);
                $prefix = substr($line, 0, $squareOpen);
                $postfix = substr($line, $parensClose+1);

                $line = "$prefix<a href='$path' >$text</a>$postfix";
            }
            array_push($result, $line);    
        }

        $this->_code = $result;
    }

    private function _formatImage()
    {
        $result = array();

        foreach ($this->_code as $line) {
            while(($squareOpen = strpos($line, '!['))!==false &&
                ($squareClose = strpos($line, ']', ($textBegin = $squareOpen+2)))!==false &&
                ($parensOpen = strpos($line, '(', $squareClose))!==false &&
                ($parensClose = strpos($line, ')',($pathBegin = $parensOpen+1)))!==false
            ) {
                $text = substr($line, $textBegin, $squareClose-$textBegin);
                $path = substr($line, $pathBegin, $parensClose-$pathBegin);
                $prefix = substr($line, 0, $squareOpen);
                $postfix = substr($line, $parensClose+1);

                $line = "$prefix<img src='$path' alt='$text' />$postfix";
            }
            array_push($result, $line);    
        }

        $this->_code = $result;
    }
}
