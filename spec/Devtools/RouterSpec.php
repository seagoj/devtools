<?php

namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools;

class RouterSpec extends ObjectBehavior
{
    function let(
        Devtools\RestInterface $entity,
        Devtools\RestInterface $user,
        Devtools\RestInterface $repository
    ) {
        $this::resource('/Entity/:id', $entity);
        $this::resource(
            [
                '/Users/(:userid)'            => $user,
                '/Repository/(:repositoryId)' => $repository
            ]
        );

        $this::resources()->shouldReturn(
            [
                '/Entity/:id'                 => $entity,
                '/Users/(:userid)'            => $user,
                '/Repository/(:repositoryId)' => $repository
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Router');
    }

    function it_should_handle_calls($entity, $user, $repository)
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $entity->get(['id' => 23])->shouldBeCalled();
        $this::call('/Entity/23');

        $user->get(['userid' => 1])->shouldBeCalled();
        $this::call('/Users/1');

        $user->get([])->shouldBeCalled();
        $this::call('/Users');

        $repository->get(['repositoryId' => 74])->shouldBeCalled();
        $this::call('/Repository/74');
    }
}
