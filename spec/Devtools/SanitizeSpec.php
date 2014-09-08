<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SanitizeSpec extends ObjectBehavior
{
    function it_sanitizes_strings()
    {
        $this::str('<p>test</p>')->shouldReturn('&lt;p&gt;test&lt;/p&gt;');
    }
}
