<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FormatSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Format');
    }

    function it_converts_snakecase_to_camelcase()
    {
        $this::snakeToCamelCase('test_string')->shouldReturn('testString');
    }

    function it_converts_camelcase_to_snakecase()
    {
        $this::camelToSnakeCase('testString')->shouldReturn('test_string');
    }
}
