<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HTMLFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\HTMLFormatter');
    }

    function it_displays_log()
    {
        $this->format('LOG1', null)->shouldReturn('<p>LOG1</p>');
        $this->format('LOG2', true)->shouldReturn('<p>true: LOG2</p>');
        $this->format('LOG3', false)->shouldReturn('<p>false: LOG3</p>');
    }
}
