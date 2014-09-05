<?php

namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseSpec extends ObjectBehavior
{
    function let()
    {
        $_REQUEST = array(
            'param1' => 'val1',
            'param2'=> 'val2',
            'param3'
        );
    }

    function letgo()
    {
        unset($_REQUEST);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Response');
    }

    /* function it_collects_request() */
    /* { */
    /*     $this->getRequest()->shouldReturn( */
    /*         array( */
    /*             'param1' => 'val1', */
    /*             'param2' => 'val2', */
    /*             'param3' */
    /*         ) */
    /*     ); */
    /* } */

    /* function it_speaks_json() */
    /* { */
    /*     $this->json()->shouldReturn( */
    /*         json_encode( */
    /*             array( */
    /*                 'status' => 'OK', */
    /*                 'request' => array( */
    /*                     'param1' => 'val1', */
    /*                     'param2' => 'val2', */
    /*                     'param3' */
    /*                 ), */
    /*                 'message' => '' */
    /*             ) */
    /*         ) */
    /*     ); */
    /* } */

    /* function it_should__sleep() */
    /* { */
    /*     $this->data( */
    /*         array( */
    /*             'key1' => 'value', */
    /*             'key2' => 1, */
    /*         ) */
    /*     )->__sleep()->shouldReturn( */
    /*         array( */
    /*             'status', */
    /*             'request', */
    /*             'message', */
    /*             'key1', */
    /*             'key2' */
    /*         ) */
    /*     ); */
    /* } */

    /* function it_speaks_php_objects() */
    /* { */
    /*     $this->php()->shouldReturn( */
    /*         array( */
    /*             'status' => 'OK', */
    /*             'request' => array( */
    /*                 'param1' => 'val1', */
    /*                 'param2' => 'val2', */
    /*                 'param3' */
    /*             ), */
    /*             'message' => '' */
    /*         ) */
    /*     ); */
    /* } */

    /* function it_speaks_bar_delimitted_strings() */
    /* { */
    /*     $this->data( */
    /*         array( */
    /*             array("val1","val2"), */
    /*             array("val3", "val4") */
    /*         ) */
    /*     )->delimited()->shouldReturn( */
    /*         "val1|val2\nval3|val4\n" */
    /*     ); */
    /* } */

    /* function it_is_serializable() */
    /* { */
    /*     $this->data( */
    /*         array( */
    /*             "var1" => "val1", */
    /*             "var2" => "val2" */
    /*         ) */
    /*     ); */

    /*     $this->serialize() */
    /*         ->shouldReturn( */
    /*             'a:5:{s:6:"status";s:2:"OK";s:7:"request";a:3:{s:6:"param1";s:4:"val1";s:6:"param2";s:4:"val2";i:0;s:6:"param3";}s:7:"message";s:0:"";s:4:"var1";s:4:"val1";s:4:"var2";s:4:"val2";}' */
    /*         ); */

    /*     $this->unserialize( */
    /*         'a:5:{s:6:"status";s:2:"OK";s:7:"request";a:3:{s:6:"param1";s:4:"val1";s:6:"param2";s:4:"val2";i:0;s:6:"param3";}s:7:"message";s:0:"";s:4:"var1";s:4:"val1";s:4:"var2";s:4:"val2";}' */
    /*     )->shouldReturn( */
    /*         array( */
    /*             'status'  => 'OK', */
    /*             'request' => array( */
    /*                 'param1' => 'val1', */
    /*                 'param2' => 'val2', */
    /*                 'param3' */
    /*             ), */
    /*             'message' => '', */
    /*             'var1'    => 'val1', */
    /*             'var2'    => 'val2' */
    /*         ) */
    /*     ); */
    /* } */
}
