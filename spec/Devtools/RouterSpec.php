<?php

namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools;

class RouterSpec extends ObjectBehavior
{
    function let(
        Devtools\RestInterface $entity,
        Devtools\RestInterface $user
    ) {
        $this::resource('/Entity/:id', $entity);
        /* $this::resource(['/Users/:userid' => $user]); */
        $this::resource([
            '/Users/(:userid)' => $user
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Router');
    }

    function it_should_match_paths()
    {
        $this::parseRequest('/Users')->shouldReturn(
            [
                'route'   => '/Users/(:userid)',
                'params'  => []
            ]
        );

        $this::parseRequest('/Users/1')->shouldReturn(
            [
                'route'   => '/Users/(:userid)',
                'params'  => [
                    'userid' => 1
                ]
            ]
        );

        $this::parseRequest('/Entity/23')->shouldReturn(
            [
                'route'  => '/Entity/:id',
                'params' => [
                    'id'    => 23
                ]
            ]
        );
    }

    function it_should_handle_calls($entity, $user)
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $entity->get(['id' => 23])->shouldBeCalled();
        $this::call('/Entity/23');

        /* $user->get(['userid' => 1])->shouldBeCalled(); */
        /* $this::call('/Users/1'); */

        /* $user->get([])->shouldBeCalled(); */
        /* $this::call('/Users'); */
    }
}
