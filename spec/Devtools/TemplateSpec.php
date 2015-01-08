<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TemplateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Template');
    }

    function it_adds_values_into_template_from_associative_array()
    {
        $this::autofill(
            'My name is {{name}}. Hello {{location}}!',
            [
                'name' => 'Jeremy',
                'location' => 'world'
            ]
        )->shouldReturn('My name is Jeremy. Hello world!');
    }

    function it_will_join_array_values_with_commas()
    {
        $this::autofill(
            'My name is {{name}}. I like {{fruit}}.',
            [
                'name' => 'Jeremy',
                'fruit' => ['apples', 'oranges', 'pears']
            ]
        )->shouldReturn(
            'My name is Jeremy. I like apples, oranges, pears.'
        );
    }
}
