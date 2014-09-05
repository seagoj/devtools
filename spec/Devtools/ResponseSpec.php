<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseSpec extends ObjectBehavior
{
    function let(\Devtools\MysqlModel $modelMock)
    {
        $_REQUEST = [
            'param1' => 'val1',
            'param2' => 'val2',
            'param3'
        ];

        $this->beConstructedWith($modelMock);
    }

    function letgo()
    {
        unset($_REQUEST);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Response');
    }

    function it_collects_request()
    {
        $this->getRequest()->shouldReturn(
            [
                'param1' => 'val1',
                'param2' => 'val2',
                'param3'
            ]
        );
    }

    function it_speaks_json()
    {
        $this->json()->shouldReturn(
            json_encode(
                [
                    'status' => 'OK',
                    'request' => array(
                        'param1' => 'val1',
                        'param2' => 'val2',
                        'param3'
                    ),
                    'message' => ''
                ]
            )
        );
    }

    function it_should_sleep()
    {
        $this->data(
            [
                'key1' => 'value',
                'key2' => 1,
            ]
        )->__sleep()->shouldReturn(
            [
                'status',
                'request',
                'message',
                'key1',
                'key2'
            ]
        );
    }

    function it_speaks_php_objects()
    {
        $this->php()->shouldReturn(
            [
                'status' => 'OK',
                'request' => array(
                    'param1' => 'val1',
                    'param2' => 'val2',
                    'param3'
                ),
                'message' => ''
            ]
        );
    }

    function it_speaks_bar_delimitted_strings()
    {
        $this->data(
            [
                array("val1","val2"),
                array("val3", "val4")
            ]
        )->delimited()->shouldReturn(
            "val1|val2\nval3|val4\n"
        );
    }

    function it_is_serializable()
    {
        $this->data(
            [
                "var1" => "val1",
                "var2" => "val2"
            ]
        );

        $this->serialize()
            ->shouldReturn(
                'a:5:{s:6:"status";s:2:"OK";s:7:"request";a:3:{s:6:"param1";s:4:"val1";s:6:"param2";s:4:"val2";i:0;s:6:"param3";}s:7:"message";s:0:"";s:4:"var1";s:4:"val1";s:4:"var2";s:4:"val2";}'
            );

        $this->unserialize(
            'a:5:{s:6:"status";s:2:"OK";s:7:"request";a:3:{s:6:"param1";s:4:"val1";s:6:"param2";s:4:"val2";i:0;s:6:"param3";}s:7:"message";s:0:"";s:4:"var1";s:4:"val1";s:4:"var2";s:4:"val2";}'
        );
        $this->json()->shouldReturn(
            json_encode(
                [
                    'status'  => 'OK',
                    'request' => array(
                        'param1' => 'val1',
                        'param2' => 'val2',
                        'param3'
                    ),
                    'message' => '',
                    'var1'    => 'val1',
                    'var2'    => 'val2'
                ]
            )
        );
    }

    function it_loads_data_from_model(\Devtools\MysqlModel $modelMock)
    {
        $modelMock
            ->query(
                'select `user_name`, `last_name` from users where `user_id`=:user_id',
                ['user_id' => 1],
                true
            )
            ->willReturn(['user_name' =>'seagoj', 'last_name' => 'Seago']);

        $this
            ->load(
                'select `user_name`, `last_name` from users where `user_id`=:user_id',
                ['user_id' => 1]
            )
            ->shouldReturn(['user_name' => 'seagoj', 'last_name' => 'Seago']);
        $this->json()->shouldReturn(
            json_encode(
                [
                    'status'    => 'OK',
                    'request'   => $_REQUEST,
                    'message'   => '',
                    'user_name' => 'seagoj',
                    'last_name' => 'Seago'
                ]
            )
        );
    }

    function it_sets_and_gets_magically()
    {
        $this->param1 = 'val1';
        $this->param1->shouldBe('val1');

        $this->json()->shouldReturn(
            json_encode(
                [
                    'status'  => 'OK',
                    'request' => $_REQUEST,
                    'message' => '',
                    'param1'  => 'val1'
                ]
            )
        );
    }
}
