<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Auth');
    }

    function it_should_create_and_verify_hashes()
    {
        $password = 'secretPassword1';
        $hash = $this::hash($password);
        $this::check($password, $hash)->shouldBe(true);
    }
}
