<?php

namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ServiceSpec extends ObjectBehavior
{
    function let() {
        $_REQUEST = array(
            'param1' => 'val1',
            'param2'=> 'val2',
            'param3'
        );
    }

    function letgo() {
        unset($_REQUEST);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Service');
    }

    function it_responds_correctly_in_json()
    {
        $this->json()->shouldReturn(
            json_encode(
                array(
                    'status' => 'OK',
                    'request' => array(
                        'param1' => 'val1',
                        'param2' => 'val2',
                        'param3'
                    ),
                    'message' => '',
                    'param1'  => 'response1'
                )
            )
        );
    }

    function it_responds_correctly_in_php_objects()
    {
        $this->php()->shouldReturn(
            array(
                'status' => 'OK',
                'request' => array(
                    'param1' => 'val1',
                    'param2' => 'val2',
                    'param3'
                ),
                'message' => '',
                'param1'  => 'response1'
            )
        );
    }
}
