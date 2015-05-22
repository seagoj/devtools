<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FileSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('./test.log');
    }

    function letgo()
    {
        if (file_exists('./test.log')) {
            unlink('./test.log');
        }
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\File');
    }

    function it_pulls_name_from_path()
    {
        $this->name->shouldReturn('test.log');
    }

    function it_returns_existance_of_file()
    {
        $this->exists()->shouldReturn(false);
        file_put_contents('./test.log', '');
        $this->exists()->shouldReturn(true);
    }

    function it_creates_and_sets_contents_of_file()
    {
        $this->exists()->shouldReturn(false);
        $this->contents('TEST');
        $this->exists()->shouldReturn(true);
        $this->contents->shouldReturn('TEST');
    }

    function it_deletes_file()
    {
        file_put_contents('./test.log', 'TEST');
        $this->exists()->shouldReturn(true);
        $this->delete();
        $this->exists()->shouldReturn(false);
    }
}
