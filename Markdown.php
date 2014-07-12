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
 **/
class Markdown
{
    /**
     * Configuration array for the class
     *
     * Sets the flavor of Markdown and logType to be used with Devtools\Log
     **/
    private $config;
    /**
     * Log object for the class. Devtools\Log
     *
     * An instance of Devtools\Log of type this.config['logType']
     **/
    private $log;
    /**
     * Array of lines to be parsed
     *
     * An array of the string or file contents exploded by the linefeed. So that
     * each line becomes an entry in the array.
     **/
    private $code;

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
    public function __construct($options = [])
    {
        $defaults = [
            'flavor' => 'multimarkdown',
            'logType' => 'stdout',
            'htmlTag' => true
        ];

        $this->config = array_merge($defaults, $options);

        $this->validateConfig();

        $logOptions = array('type'=>$this->config['logType']);
        $this->log = new \Devtools\Log($logOptions);
    }

    /**
     * Markdown::validateConfig()
     *
     * Validates Configuration
     *
     * @return boolean
     **/
    private function validateConfig()
    {
        $valid = [
            'flavor' => [
                'standard',
                'github',
                'multimarkdown'
            ],
            'logType' => [
                'stdout',
            ],
            'htmlTag' => [
                true,
                false
            ]
        ];

        foreach ($this->config as $var => $value) {
            if (!array_key_exists($var, $valid)) {
                throw new \InvalidArgumentException("$var is not a valid option.");
            } elseif (!in_array($value, $valid[$var])) {
                throw new \InvalidArgumentException("$value is not a valid value for $var.");
            }
        }

        return true;
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
        if (is_file($input)) {
            $code = file_get_contents($input);
        } else {
            $code = $input;
        }

        $this->code = explode(PHP_EOL, $code);

        $this->formatMetadata();
        $this->formatInline();
        $this->formatHeader();
        $this->formatUnorderedList();
        $this->formatOrderedList();
        $this->formatHR();
        $this->formatCode();
        $this->formatBlockquote();
        $this->formatImage();
        $this->formatLink();
        $this->formatParagraph();

        $html = '';
        foreach ($this->code as $line) {
            $html .= $line.PHP_EOL;
        }


        return $this->config['htmlTag'] ?
            "<html>\n".$html."</html>\n" :
            $html;
    }

    /**
     * Markdown::formatMetadata()
     *
     * Adds metadata tags from Multimarkdown compatible files
     *
     * @return void
     **/
    private function formatMetadata()
    {
        $syntax = [
            ' ',
            '<',
            '*',
            '_',
            '#'
        ];

        $first = true;
        $result = [];
        $ended = false;

        if ($this->config['flavor'] === 'multimarkdown') {
            foreach ($this->code as $index => $line) {
                $md = '';

                if ($ended !== true && $line !== '' &&
                    !in_array($line[0], $syntax) &&
                    ($pivot = strpos($line, ':', 1)) !==false &&
                    ($label = substr($line, 0, $pivot)) !==false &&
                    ($value = substr($line, $pivot+1)) !==false &&
                    ($link = strpos($label, 'http'))===false) {

                    $md = $first ? "<head>\n" : "";
                    $first = false;

                    switch ($label) {
                        case 'title':
                            $md .= "<title>".trim($value)."</title>";
                            break;
                        case 'author':
                        case 'description':
                        case 'keywords':
                        case 'date':
                            $md .= "<meta name='".$label."' content='".trim($value)."'>";
                            break;
                        default:
                            throw new \InvalidArgumentException("$label is not a valid metadata tag");
                    }
                } else {
                    if ($first===false && $ended===false) {
                        $md .= "</head>\n";
                        $ended = true;
                    } else {
                        $md = $line;
                    }
                }

                array_push($result, $md);

            }
            $this->code = $result;
        }
    }

    /**
     * Markdown::formatParagraph()
     *
     * Adds paragraph tags in the proper locations and stores result in
     * this.code
     *
     * @return void
     **/
    private function formatParagraph()
    {
        $rootElements = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', '/ul',
            'ol', '/ol',
            'li',
            'hr',
            'code', '/code',
            'blockquote', '/blockquote',
            'head', '/head',
            'title', '/title',
            'meta',
            'html', '/html'
        );

        $blockElements = array('code', 'blockquote');

        $result = array();
        $first = true;
        $block = false;
        foreach ($this->code as $line) {
            if ($line!=='' && $line[0]==='<') {
                $tag = substr($line, 1, ($end = strpos($line, '>', 1))-1);
                if (($space = strpos($tag, ' ')) !== false) {
                    $tag = substr($tag, 0, $space);
                }
                if (in_array($tag, $rootElements)) {
                    if (in_array($tag, $blockElements)) {
                        $block=true;
                    }
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

        $this->code = $result;
    }

    /**
     * Markdown::tagReplace()
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
    private function tagReplace($line, $tag, $startTag, $endTag = null)
    {
        if ($startTag===$endTag || $endTag===null) {
            $begin = strpos($line, $startTag);
            $end = strpos($line, $startTag, $begin)-strlen($startTag);
            if ($begin!==false && $end!==false) {
                $line = str_replace(" $startTag", " <$tag>", $line);
                $line = str_replace("$startTag ", "</$tag> ", $line);
            }

            $tagLength = strlen($startTag);
            // Check for leading tag */
            if (substr($line, 0, $tagLength)==$startTag) {
                $line = "<$tag>".substr($line, $tagLength);
            }

            // Check for ending tag */
            if (substr($line, -$tagLength)==$startTag) {
                $line = substr($line, 0, strlen($line)-$tagLength)."</$tag>";
            }
        }

        return $line;
    }

    /**
     * Markdown::formatInline
     *
     * Formats inline Markdown to HTML and stores result in this.code
     *
     * @return void
     **/
    private function formatInline()
    {
        $syntaxMap = [
            '`'=>'code',
            '**' => 'strong',
            '__' => 'strong',
            '*' => 'em',
            '_' => 'em'
        ];

        foreach ($syntaxMap as $syntax => $tag) {
            $result = array();
            foreach ($this->code as $line) {
                $first = strpos($line, $syntax);
                if ($first!==false) {
                    $second = strpos($line, $syntax, $first+strlen($syntax));
                    if ($second!==false) {
                        array_push($result, $this->tagReplace($line, $tag, $syntax));
                    } else {
                        array_push($result, $line);
                    }
                } else {
                    array_push($result, $line);
                }
            }
            $this->code = $result;
        }
    }

    /**
     * Markdown::formatHR
     *
     * converts Markdown HR to HTML and stores result in this.code
     *
     * @return void
     **/
    private function formatHR()
    {
        $result = array();
        foreach ($this->code as $line) {
            if (substr($line, 0, 3)==='---') {
                array_push($result, "<hr>");
            } else {
                array_push($result, $line);
            }
        }

        $this->code = $result;
    }

    /**
     * Markdown::formatHeader
     *
     * Converts Markdown headers into HTML and stores result in this.code
     *
     * @return void
     **/
    private function formatHeader()
    {
        $result = array();
        foreach ($this->code as $line) {

            if (($start = strpos($line, '#'))!==false) {
                $depth = $start;
                while ($line[$depth] == '#') {
                    $depth++;
                }

                $depth = $depth-$start;
                $tag = "h".$depth;
                $prefix = substr($line, 0, $start);
                array_push($result, "$prefix<$tag>".substr($line, $start+$depth+1)."</$tag>");
            } else {
                array_push($result, $line);
            }
        }

        $this->code = $result;
    }

    /**
     * Markdown::formatUnorderedList
     *
     * Converts Markdown UL into HTML and stores reult in this.code
     *
     * @return void
     **/
    private function formatUnorderedList()
    {
        $result = array();
        $first = true;
        $triggered = false;
        foreach ($this->code as $line) {
            if (strpos($line, "* ")!==false || strpos($line, "- ")!==false || strpos($line, "+ ")!==false) {
                $triggered = true;
                $li = substr($line, strpos($line, ' ')+1);
                if ($first === true) {
                    array_push($result, "<ul>".PHP_EOL."<li>$li</li>");
                    $first = false;
                } else {
                    array_push($result, "<li>$li</li>");
                }
            } else {
                extract(
                    $this->endTag(
                        'ul',
                        compact($triggered, $result, $line)
                    )
                );
            }
        }
        $this->code = $result;
    }

    /**
     * Markdown::formatOrderedList
     *
     * Converts Markdown OL into HTML and stores result in this.code
     *
     * @return void
     **/
    private function formatOrderedList()
    {
        $result = array();
        $first = true;
        $triggered = false;
        foreach ($this->code as $line) {
            if (($pivot=strpos($line, '. '))!==false) {
                if (is_numeric(trim($prefix = substr($line, 0, $pivot)))) {
                    $triggered = true;
                    if ($first) {
                        array_push($result, "<ol>");
                        $first = false;
                    }
                    array_push($result, "<li>".substr($line, $pivot+2)."</li>");
                } else {
                    array_push($result, $line);
                }
            } else {
                extract(
                    $this->endTag(
                        'ol',
                        compact($triggered, $result, $line)
                    )
                );
                $first = true;
            }
        }
        $this->code = $result;
    }

    private function endTag($tag, $state)
    {
        if ($state['triggered']) {
            array_push($state['result'], "</$tag>");
            $state['triggered'] = false;
        }
        if ($state['line'] != PHP_EOL) {
            array_push($state['result'], $state['line']);
        }
        return $state;
    }

    /**
     * Markdown::formatCode
     *
     * Converts Markdown code blocks into HTML and stores result in this.code
     *
     * @return void
     **/
    private function formatCode()
    {
        $first = true;
        $result = array();
        $triggered = false;

        foreach ($this->code as $line) {
            $string = substr($line, 4);
            if (substr($line, 0, 4)==='    ') {
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

        return $this->code = $result;
    }

    /**
     * Markdown::formatBlockquote
     *
     * Converts Markdown blockquote into HTML and stores result in this.code
     *
     * @return void
     **/
    private function formatBlockquote()
    {
        $first = true;
        $result = array();
        $triggered = false;

        foreach ($this->code as $line) {
            $string = substr($line, 2);
            if (substr($line, 0, 2)==='> ') {
                if ($first) {
                    array_push($result, "<blockquote>");
                    $first = false;
                }
                array_push($result, "    $string");
                $triggered = true;
            } else {
                if ($triggered) {
                    array_push($result, "</blockquote>");
                }
                array_push($result, $line);
            }
        }

        return $this->code = $result;
    }

    /**
     * Markdown::formatLink
     *
     * Converts Markdown links into HTML and stores result in this.code
     *
     * @return void
     **/
    private function formatLink()
    {
        $result = array();

        foreach ($this->code as $line) {
            array_push($result, $this->getTextPath($line, 'link'));
        }

        $this->code = $result;
    }

    /**
     * Markdown::formatImage
     *
     * Converts Markdown images into HTML and stores result in this.code
     *
     * @return void
     **/
    private function formatImage()
    {
        $result = array();

        foreach ($this->code as $line) {
            array_push($result, $this->getTextPath($line, 'image'));
        }

        $this->code = $result;
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
     * @param string $type Type of element to look for and return values
     *                          formatted accordingly
     *
     * @return string Formatted line
     **/
    private function getTextPath($line, $type)
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

        while (
            isset($textDelimStart)
            && isset($textDelimEnd)
            && isset($pathDelimStart)
            && isset($pathDelimEnd)
            && isset($template)
            && ($squareOpen = strpos($line, $textDelimStart))!==false
            && ($squareClose = strpos($line, $textDelimEnd, ($textBegin = $squareOpen+strlen($textDelimStart))))!==false
            && ($parensOpen = strpos($line, $pathDelimStart, $squareClose))!==false
            && ($parensClose = strpos($line, $pathDelimEnd, ($pathBegin=$parensOpen+strlen($pathDelimStart))))!==false
        ) {
            $vars = [
                'text' => substr($line, $textBegin, $squareClose-$textBegin),
                'path' => substr($line, $pathBegin, $parensClose-$pathBegin),
                'prefix' => substr($line, 0, $squareOpen),
                'postfix' => substr($line, $parensClose+1)
            ];

            $line = \Devtools\Template::autofill($template, $vars);
        }

        return $line;
    }
}
