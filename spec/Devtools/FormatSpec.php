<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FormatSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Format');
    }

    function it_converts_underscore_to_camelcase()
    {
        $this::underscoreToCamelCase('test_string')->shouldReturn('testString');
    }

    function it_converts_camelcase_to_underscore()
    {
        $this::camelCaseToUnderscore('testString')->shouldReturn('test_string');
    }
}
