<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools;

class ViewSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Devtools\MockView');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\View');
    }

    function it_formats_stylesheet_tags()
    {
        $this->stylesheet->shouldReturn(
            "<link rel='stylesheet' href='sheet1.css'>\n<link rel='stylesheet' href='sheet2.css'>\n"
        );
    }

    function it_formats_script_tags()
    {
        $this->script->shouldReturn(
            "<script src='script1.js'></script>\n"
        );
    }
}

class MockView extends Devtools\View
{
    protected $stylesheetCollection = ['sheet1.css', 'sheet2.css'];
    protected $scriptCollection     = 'script1.js';
}
