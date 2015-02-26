<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Redis;

class RedisRepositorySpec extends ObjectBehavior
{
    function let(RedisMock $connection)
    {
        $this->beConstructedWith($connection);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\RedisRepository');
    }

    /* function it_gets_a_single_value_from_store($connection) */
    /* { */
    /*     $connection->get('key')->willReturn('value'); */
    /*     $this->get('key')->shouldReturn('value'); */
    /* } */

    /* function it_gets_a_single_value_from_a_collection_in_a_store($connection) */
    /* { */
    /*     $connection->hget('collection', 'key')->willReturn('collectionValue'); */
    /*     $this->get('key', 'collection')->shouldReturn('collectionValue'); */
    /* } */

    /* function it_sets_a_single_value_in_store($connection) */
    /* { */
    /*     $connection->set('key', 'value')->willReturn(true); */
    /*     $this->set('key', 'value')->shouldReturn(true); */
    /* } */

    /* function it_sets_a_single_value_in_a_collection_in_store($connection) */
    /* { */
    /*     $connection->hset('collection', 'key', 'valueCollection')->willReturn(true); */
    /*     $this->set('key', 'valueCollection', 'collection')->shouldReturn(true); */
    /* } */

    /* function it_gets_an_entire_hash($connection) */
    /* { */
    /*     $connection */
    /*         ->hgetall('collection') */
    /*         ->willReturn(['key' => 'value', 'key2'=> 'value2']); */
    /*     $this */
    /*         ->getAll('collection') */
    /*         ->shouldReturn(['key' => 'value', 'key2'=> 'value2']); */
    /* } */

    /* function it_sets_expire($connection) */
    /* { */
    /*     $connection */
    /*         ->expire('key', 10) */
    /*         ->willReturn(true); */

    /*     $this */
    /*         ->expire('key', 10) */
    /*         ->shouldReturn(true); */
    /* } */
}
$classes = get_declared_classes();
var_dump($classes['Redis']);
/* if (!class_exists('Redis')) { */
/*     class Redis */
/*     { */
/*     } */
/* } */
