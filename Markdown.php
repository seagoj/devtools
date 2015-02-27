<?php namespace Devtools;

class Markdown
{
    private $config;
    private $code;

    public function __construct($options = [])
    {
        $defaults = [
            'flavor'  => 'multimarkdown',
            'logType' => 'stdout',
            'htmlTag' => true
        ];
        $this->config = array_merge($defaults, $options);
        $this->validateConfig();
        $logOptions = array('type'=>$this->config['logType']);
        $this->log = new \Devtools\Log($logOptions);
    }

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
            } else if (!in_array($value, $valid[$var])) {
                throw new \InvalidArgumentException("$value is not a valid value for $var.");
            }
        }
        return true;
    }

    public function convert($input)
    {
        if (!is_string($input)) {
            throw new \InvalidArgumentException('Input is not a string or path.');
        }

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
                if ($ended !== true
                    && $line !== ''
                    && !in_array($line[0], $syntax)
                    && ($pivot = strpos($line, ':', 1)) !==false
                    && ($label = substr($line, 0, $pivot)) !==false
                    && ($value = substr($line, $pivot+1)) !==false
                    && (strpos($label, 'http')) === false
                ) {
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
                $tag = substr($line, 1, (strpos($line, '>', 1))-1);
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
            if (substr($line, 0, $tagLength)==$startTag) {
                $line = "<$tag>".substr($line, $tagLength);
            }

            if (substr($line, -$tagLength)==$startTag) {
                $line = substr($line, 0, strlen($line)-$tagLength)."</$tag>";
            }
        }

        return $line;
    }

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
                $state = $this->endTag(
                    'ul',
                    compact('triggered', 'result', 'line')
                );
                extract($state);
            }
        }
        $this->code = $result;
    }

    private function formatOrderedList()
    {
        $result = array();
        $first = true;
        $triggered = false;
        foreach ($this->code as $line) {
            if (($pivot=strpos($line, '. '))!==false) {
                if (is_numeric(trim(substr($line, 0, $pivot)))) {
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
                $state = $this->endTag(
                    'ol',
                    compact('triggered', 'result', 'line')
                );
                extract($state);
                $first = true;
            }
        }
        $this->code = $result;
    }

    private function endTag($tag, $state)
    {
        if ($state['triggered']) {
            array_push($state['result'], "</".$tag.">");
            $state['triggered'] = false;
        }
        if ($state['line'] != PHP_EOL) {
            array_push($state['result'], $state['line']);
        }
        return $state;
    }

    private function formatCode()
    {
        return $this->formatBlock('    ', 'code');
    }

    private function formatBlockquote()
    {
        return $this->formatBlock('> ', 'blockquote');
    }

    private function formatBlock($symbol, $tag)
    {
        $first = true;
        $result = array();
        $triggered = false;
        $symbolLength = strlen($symbol);
        foreach ($this->code as $line) {
            $string = substr($line, $symbolLength);
            if (substr($line, 0, $symbolLength)===$symbol) {
                if ($first) {
                    array_push($result, "<".$tag.">");
                    $first = false;
                }
                array_push($result, "    ".$string);
                $triggered = true;
            } else {
                if ($triggered) {
                    array_push($result, "</".$tag.">");
                }
                array_push($result, $line);
            }
        }
        return $this->code = $result;
    }

    private function formatLink()
    {
        $result = array();

        foreach ($this->code as $line) {
            array_push($result, $this->getTextPath($line, 'link'));
        }

        $this->code = $result;
    }

    private function formatImage()
    {
        $result = array();

        foreach ($this->code as $line) {
            array_push($result, $this->getTextPath($line, 'image'));
        }

        $this->code = $result;
    }

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
