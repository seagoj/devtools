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
     * @param array $options Array of options for class
     * @option  string  flavor      Type of Markdown to be used in the
     *                              conversion
     * @option  string  logType     Type of log to write
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

        foreach ($this->_config as $var=>$value) {
            if (!array_key_exists($var, $valid))
                throw new \Exception("$var is not a valid option.");
            else if (!in_array($value, $valid[$var]))
                throw new \Exception("$value is not a valid value for $var.");
            else
                return true;
        }
    }

    /**
     * Markdown::convert()
     *
     * Converts Markdown syntax into HTML and returns as string
     *
     * @param string $input Filename or string to be converted
     *
     * @return string Formatted string
     **/
    public function convert($input)
    {
        // Pull contents of file if input is a path to a file
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
        foreach ($this->_code AS $line) {
            $html .= $line."\n";
        }

        return $html;
    }

    /**
     * Markdown::_formatParagraph()
     *
     * Adds paragraph tags in the proper locations and stores result in
     * this._code
     *
     * @return void
     **/
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
                $tag = substr($line, 1, ($end = strpos($line, '>')-1));
                if (in_array($tag, $rootElements)) {
                    if (in_array($tag, $blockElements))
                        $block=true;
                    array_push($result, $line);
                } else {
                    if ($first) {
                        array_push($result, "<p>");
                        $first = false;
                    }
                    array_push($result, $line);
                }
            } elseif ($line!=='') {
                if ($first && !$block) {
                    array_push($result, "<p>");
                    $first = false;
                }
                array_push($result, $line);
            }

            if (!$first) {
                array_push($result, "</p>");
                $first = true;
            }
        }

        $this->_code = $result;
    }

    /**
     * Markdown::_tagReplace()
     *
     * Replaces Markdown syntax with tags
     *
     * @param string $line     String to have items replaced
     * @param string $tag      HTML tag to replace the Markdown syntax
     * @param string $startTag Initial Markdown tag
     * @param string $endTag   Ending Markdown tag (assumed to be the same
     *                              as $startTag if not passed
     *
     * @return string Formatted line
     **/
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

    /**
     * Markdown::_formatInline
     *
     * Formats inline Markdown to HTML and stores result in this._code
     *
     * @return void
     **/
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

    /**
     * Markdown::_formatHR
     *
     * converts Markdown HR to HTML and stores result in this._code
     *
     * @return void
     **/
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

    /**
     * Markdown::_formatHeader
     *
     * Converts Markdown headers into HTML and stores result in this._code
     *
     * @return void
     **/
    private function _formatHeader()
    {
        $result = array();
        foreach ($this->_code AS $line) {

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

    /**
     * Markdown::_formatUnorderedList
     *
     * Converts Markdown UL into HTML and stores reult in this._code
     *
     * @return void
     **/
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

    /**
     * Markdown::_formatOrderedList
     *
     * Converts Markdown OL into HTML and stores result in this._code
     *
     * @return void
     **/
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

    /**
     * Markdown::_formatCode
     *
     * Converts Markdown code blocks into HTML and stores result in this._code
     *
     * @return void
     **/
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

    /**
     * Markdown::_formatBlockquote
     *
     * Converts Markdown blockquote into HTML and stores result in this._code
     *
     * @return void
     **/
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

    /**
     * Markdown::_formatLink
     *
     * Converts Markdown links into HTML and stores result in this._code
     *
     * @return void
     **/
    private function _formatLink()
    {
        $result = array();

        foreach ($this->_code as $line)
            array_push($result, $this->_getTextPath($line, 'link'));

        $this->_code = $result;
    }

    /**
     * Markdown::_formatImage
     *
     * Converts Markdown images into HTML and stores result in this._code
     *
     * @return void
     **/
    private function _formatImage()
    {
        $result = array();

        foreach ($this->_code as $line)
            array_push($result, $this->_getTextPath($line, 'image'));

        $this->_code = $result;
    }

    /**
     * Markdown::_getTextPath
     *
     * Collects text and path for image and link for a line of Markdown and
     * return as string
     *
     * @param string $line Line of raw Markdown to be converted
     * @param string $type Type of element to look for and return values
     *                          formatted accordingly
     *
     * @return string Formatted line
    private function _getTextPath($line, $type)
    {
        switch ($type) {
            case 'link':
                $textDelimStart = '[';
                $textDelimEnd =  ']';
                $pathDelimStart = '(';
                $pathDelimEnd = ')';
                $template = "{{prefix}}<a href='{{path}}' >{{text}}</a>{{postfix}}";
                break;
            case 'image':
                $textDelimStart = '![';
                $textDelimEnd =  ']';
                $pathDelimStart = '(';
                $pathDelimEnd = ')';
                $template = "{{prefix}}<img src='{{path}}' alt='{{text}}' />{{postfix}}";
                break;
        }

        while(($squareOpen = strpos($line, $textDelimStart))!==false &&
            ($squareClose = strpos($line, $textDelimEnd, ($textBegin = $squareOpen+strlen($textDelimStart))))!==false &&
            ($parensOpen = strpos($line, $pathDelimStart, $squareClose))!==false &&
            ($parensClose = strpos($line, $pathDelimEnd,($pathBegin=$parensOpen+strlen($pathDelimStart))))!==false
        ) {
            $text = substr($line, $textBegin, $squareClose-$textBegin);
            $path = substr($line, $pathBegin, $parensClose-$pathBegin);
            $prefix = substr($line, 0, $squareOpen);
            $postfix = substr($line, $parensClose+1);

            $line = str_replace('{{prefix}}', $prefix,
                str_replace('{{path}}', $path,
                    str_replace('{{text}}', $text,
                        str_replace('{{postfix}}', $postfix, $template)
                    )
                )
            );
        }

        return $line;
    }
}
