<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools;

class ComponentSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Devtools\ComponentMock');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\ComponentInterface');
    }

    function it_should_register_the_component()
    {
        $this::register()->shouldReturn(true);
    }
}

class ComponentMock implements Devtools\ComponentInterface
{
    public static function register()
    {
        return true;
    }
}
