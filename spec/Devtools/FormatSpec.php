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

    function it_converts_american_date_format_into_mysql()
    {
        $this::mysqlDate('10/15/1982')->shouldReturn('1982/10/15');
    }

    function it_converts_a_number_to_currency()
    {
        $this->toCurrency(12)->shouldReturn('$12.00');
        $this::toCurrency(12.3456789)->shouldReturn('$12.35');
        $this::toCurrency(12.3, array('separation' => ','))->shouldReturn('$12,30');

    }
}
