<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RedisModelSpec extends ObjectBehavior
{
    function let(\Predis\Client $connectionMock)
    {
        $this->beConstructedWith($connectionMock);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\RedisModel');
    }

    function it_gets_a_single_value_from_store(\Predis\Client $connectionMock)
    {
        $connectionMock->get('key')->willReturn('value');
        $this->get('key')->shouldReturn('value');
    }

    function it_gets_a_single_value_from_a_collection_in_a_store(\Predis\Client $connectionMock)
    {
        $connectionMock->hget('collection', 'key')->willReturn('collectionValue');
        $this->get('key', 'collection')->shouldReturn('collectionValue');
    }

    function it_sets_a_single_value_in_store(\Predis\Client $connectionMock)
    {
        $connectionMock->set('key', 'value')->willReturn(true);
        $this->set('key', 'value')->shouldReturn(true);
    }

    function it_sets_a_single_value_in_a_collection_in_store(\Predis\Client $connectionMock)
    {
        $connectionMock->hset('collection', 'key', 'valueCollection')->willReturn(true);
        $this->set('key', 'valueCollection', 'collection')->shouldReturn(true);
    }

    function it_gets_an_entire_hash(\Predis\Client $connectionMock)
    {
        $connectionMock
            ->hgetall('collection')
            ->willReturn(['key' => 'value', 'key2'=> 'value2']);
        $this
            ->getAll('collection')
            ->shouldReturn(['key' => 'value', 'key2'=> 'value2']);
    }

    function it_sets_expire(\Predis\Client $connectionMock)
    {
        $connectionMock
            ->expire('key', 10)
            ->willReturn(true);

        $this
            ->expire('key', 10)
            ->shouldReturn(true);
    }
}
