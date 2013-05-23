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
    private $_code;
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

        $this->_code = explode("\n", $code);
        
        // ROOT LEVEL: HEADER, UNORDERED LIST, ORDERED LIST, HR, CODE, BLOCKQUOTE
        // CAN BE NESTED: IMAGES, LINKS, BOLD, ITALICS, INLINE CODE

        $this->_formatInline();
        $this->_formatHeader();
        $this->_formatUnorderedList();
        $this->_formatOrderedList();
        $this->_formatHR();
        $this->_formatCode();
        $this->_formatBlockquote();
        var_dump($this->_code);
        $this->_formatParagraph();

        /*
        $first = true;
        $closeTag = null;
        $html = "";

        foreach (explode("\n", $code) AS $line) {

            $structure = false;

            if ($line!="") {

                // BLOCKQUOTE
                if(strpos($line, "> ")!==false) {
                    $closeTag = 'blockquote';
                    $line = $this->_formatBlockquote($line, $first);
                    $first = false;
                    $structure = true;
                }

                // IMAGES
                if (strpos($line, '![')!==false) {
                    $line = $this->_formatImage($line);
                    $structure = true;
                }

                // LINKS
                if (strpos($line, '[')!==false) {
                    $line = $this->_formatLink($line);
                    $structure = true;
                }

                // HEADER
                if(strpos($line, "# ")!==false) {
                    $line = $this->_formatHeader($line);
                    $structure = true;
                }


                // UNORDERED LIST
                foreach(array('*', '-', '+') AS $syntax) {
                    if(strpos($line, "$syntax ")!==false) {
                        $closeTag = 'ul';
                        $line = $this->_formatUnorderedList($line, $syntax, $first);
                        $first = false;
                        $structure = true;
                    }
                }

                // ORDERED LIST
                if($pivot = strpos($line, '. ')!==false) {
                    if(is_numeric(trim($prefix = substr($line, 0, $pivot)))) {
                        $closeTag = 'ol';
                        $line = $this->_formatOrderedList($line, $syntax, $first);
                        $first = false;
                        $structure = true;
                    }
                }

                // HR
                if(strpos($line, '---')!==false) {
                    $line = $this->_formatHR($line);
                    $structure = true;
                }

                // CODE
                if(strpos($line, '    ')!==false) {
                    $closeTag = 'code';
                    $line = $this->_formatCode($line, $first);
                    $first = false;
                    $structure = true;
                }

                if($structure===false) {
                    $line = "<p>$line</p>";
                    $structure=true;
                }

                // INLINE FORMATTING
                $syntaxMap = array(
                    '`'=>'code',
                    '**' => 'b',
                    '__' => 'b',
                    '*' => 'i',
                    '_' => 'i'
                );
                foreach($syntaxMap AS $syntax=>$tag) {
                    if(strpos($line, $syntax)!==false)
                        $line = $this->_tagReplace($line, $tag, $syntax);
                }

                $html .= $line."\n";
            } else {
                if ($closeTag!==null) {
                    $html .= "</$closeTag>\n";
                    $closeTag = null;
                    $first = true;
                }
            }

        }*/

        $html = '';
        $previous = null;
        foreach($this->_code AS $line) {
            if( $line!='' || $previous != '')
                $html .= $line."\n";
            $previous = $line;
        }
        return $html;
    }

    private function _formatParagraph()
    {
        // ROOT LEVEL: UNORDERED LIST, ORDERED LIST, HR, CODE, BLOCKQUOTE
        // CAN BE NESTED: IMAGES, LINKS, BOLD, ITALICS, INLINE CODE
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
        $triggered = false;
        $block = false;
        foreach ($this->_code as $line) {
            if($line!=='' && $line[0]==='<') {
                print "Tag found\n";
                $end = strpos($line, '>')-1;
                print substr($line, 1, $end);
                if(in_array(substr($line, 1, $end), $rootElements)) {
                    print "Tag is in rootElements\n";
                    if(in_array(substr($line, 1, $end), $blockElements))
                        $block=true;
                    else if(in_array(substr($line, 2, $end), $blockElements))
                        $block=false;

                    if($triggered) {
                        print "End P1\n";
                        array_push($result, "</p>");
                        $triggered = false;
                    }
                    print "Write Line as is\n";
                    array_push($result, $line);
                }
                else {
                    if($first) {
                        print "Begin P1 \n";
                        array_push($result, "<p>");
                        $first = false;
                        $triggered = true;
                    }
                    print "Write line as is1\n";
                    array_push($result, $line);
                }
            } else if($line!=='') {
                if($first && !$block) {
                    print "Begin P2\n";
                    array_push($result, "<p>");
                    $first = false;
                    $triggered = true;
                }
                print "Write line as is2\n";
                array_push($result, $line);
            } else if($triggered) {
                print "End P2\n";
                array_push($result, "</p>");
                print "Write line as is3\n";
                array_push($result, $line);
                $triggered = false;
            }

            if($triggered) {
                print $line."\n";
                print "End P3\n";
                array_push($result, "</p>");
                $triggered = false;
            }
        }

        $this->_code = $result;
    }

    private function _tagReplace($line, $tag, $startTag, $endTag=null)
    {
        if($startTag===$endTag || $endTag===null) {
            $begin = strpos($line, $startTag);
            $end = strpos($line, $startTag, $begin)-strlen($startTag);
            if($begin!==false && $end!==false) {
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

    private function _formatInline()
    {
        $syntaxMap = array(
            '`'=>'code',
            '**' => 'b',
            '__' => 'b',
            '*' => 'i',
            '_' => 'i'
        );
        $first = false;

        foreach($syntaxMap AS $syntax=>$tag) {
            $result = array();
            foreach($this->_code AS $line) {
                $first = strpos($line, $syntax);
                if($first!==false) {
                    $second = strpos($line, $syntax, $first+strlen($syntax));
                    if($second!==false) {
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
        foreach($this->_code as $line) {
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
        foreach($this->_code AS $line) {

            if ($line !='' && $line[$depth=0]=='#') {
                while ( $line[$depth]=='#' )
                    $depth++;

                $tag = "h".$depth;
                array_push($result, "<$tag>".substr($line, strpos($line, ' ')+1)."</$tag>");
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
        foreach($this->_code AS $line) {
            if($loc=strpos($line, "* ")!==false || strpos($line, "- ")!==false || strpos($line, "+ ")!==false) {
                $triggered = true;
                $li = substr($line, strpos($line, ' ')+1);
                if($first === true) {
                    array_push($result, "<ul>\n<li>$li</li>");
                    $first = false;
                } else {
                    array_push($result, "<li>$li</li>");
                }
            } else {
                if($triggered) {
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
        foreach($this->_code AS $line) {
            if($pivot = strpos($line, '. ')!==false) {
                if(is_numeric(trim($prefix = substr($line, 0, $pivot)))) {
                    $triggered = true;
                    if($first) {
                        array_push($result, "<ol>");
                        $first = false;
                    }
                    array_push($result, "<li>".substr($line, $pivot+2)."</li>");
                } else
                    array_push($result, $line);
            } else{
                if($triggered) {
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

        foreach($this->_code as $line) {
            $string = substr($line, 4);
            if( substr($line, 0, 4)==='    ') {
                $triggered = true;
                if($first) {
                    array_push($result, "<code>");
                    $first = false;
                }
                array_push($result, "\t".$string);
            } else {
                if($triggered) {
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

        foreach($this->_code as $line) {
            $string = substr($line, 2);
            if (substr($line, 0, 2)==='> ') {
                if($first) {
                    array_push($result, "<blockquote>");
                    $first = false;
                }
                array_push($result, "\t<p>$string</p>");
                $triggered = true;
            } else {
                if($triggered)
                    array_push($result, "</blockquote>");
                array_push($result, $line);
            }
        }

        return $this->_code = $result;
    }

    private function _formatImage($line)
    {
        $altBegin = strpos($line, '![')+2;
        $altEnd = strpos($line, ']');
        $alt = substr($line, $altBegin, $altEnd-$altBegin);

        $pathBegin = strpos($line, '(', $altBegin)+1;
        $pathEnd = strpos($line, ')', $pathBegin);
        $path = substr($line, $pathBegin, $pathEnd-$pathBegin);

        return "<img src='$path' alt='$alt' />";
    }

    private function _formatLink($line)
    {
        $textBegin = strpos($line, '[')+1;
        $textEnd = strpos($line, ']')-$textBegin;
        $text = substr($line, $textBegin, $textEnd);

        $pathBegin = strpos($line, '(', $textBegin)+1;
        $pathEnd = strpos($line, ')', $pathBegin)-$pathBegin;
        $path = substr($line, $pathBegin, $pathEnd);

        return "<a href='$path' >$text</a>";
    }
}
