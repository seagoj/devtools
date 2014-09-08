<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MarkdownSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Markdown');
    }

    function it_converts_metadata()
    {
        $this
            ->convert("title: Test Title\nauthor: Jeremy Seago\n")
            ->shouldReturn("<html>\n<head>\n<title>Test Title</title>\n<meta name='author' content='Jeremy Seago'>\n</head>\n\n</html>\n");
    }

    function it_converts_strong()
    {
        $this
            ->convert("**bold** plain __bold__\n")
            ->shouldReturn("<html>\n<p>\n<strong>bold</strong> plain <strong>bold</strong>\n</p>\n</html>\n");
    }

    function it_converts_em()
    {
        $this
            ->convert("plain _italic_ plain *italic*\n")
            ->shouldReturn("<html>\n<p>\nplain <em>italic</em> plain <em>italic</em>\n</p>\n</html>\n");
    }

    function it_converts_inline_code()
    {
        $this
            ->convert("`code` plain `code`\n")
            ->shouldReturn("<html>\n<code>code</code> plain <code>code</code>\n</html>\n");
    }

    function it_converts_headers()
    {
        $this
            ->convert("# header1\n## header2\n### header3\n")
            ->shouldReturn("<html>\n<h1>header1</h1>\n<h2>header2</h2>\n<h3>header3</h3>\n</html>\n");
    }

    function it_converts_unordered_lists()
    {
        $this
            ->convert("* item1\n* item2\n* item3\n")
            ->shouldReturn("<html>\n<ul>\n<li>item1</li>\n<li>item2</li>\n<li>item3</li>\n</ul>\n</html>\n");
    }

    function it_converts_ordered_lists()
    {
        $this
            ->convert("1. item1\n2. item2\n3. item3\n")
            ->shouldReturn("<html>\n<ol>\n<li>item1</li>\n<li>item2</li>\n<li>item3</li>\n</ol>\n</html>\n");
    }

    function it_converts_horizontal_rule()
    {
        $this
            ->convert("---")
            ->shouldReturn("<html>\n<hr>\n</html>\n");
    }

    function it_converts_bloackquote(){
        $this
            ->convert("> line1\n> line2\n")
            ->shouldReturn("<html>\n<blockquote>\n    line1\n    line2\n</blockquote>\n</html>\n");
    }

    function it_converts_code_blocks()
    {
        $this
            ->convert("    echo 'Test';\n    echo 'Again';\n")
            ->shouldReturn("<html>\n<code>\n    echo 'Test';\n    echo 'Again';\n</code>\n</html>\n");
    }

    function it_converts_links()
    {
        $this
            ->convert("[text](http://link.com)")
            ->shouldReturn("<html>\n<p>\n<a href='http://link.com' >text</a>\n</p>\n</html>\n");
    }

    function it_converts_images()
    {
        $this
            ->convert("![text](http://link.com/image.jpg)")
            ->shouldReturn("<html>\n<p>\n<img src='http://link.com/image.jpg' alt='text' />\n</p>\n</html>\n");
    }
}
