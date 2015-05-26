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

        if (file_exists('./test.rev0.log')) {
            unlink('./test.rev0.log');
        }

        if (file_exists('./testCopy.log')) {
            unlink('./testCopy.log');
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

    function it_copies_to_new_path()
    {
        file_put_contents('./test.log', 'TEST');
        $this->name->shouldReturn('test.log');
        $this->copyTo('testCopy.log');
        $this->name->shouldReturn('testCopy.log');
        $this->contents->shouldReturn('TEST');
    }

    function it_correctly_parses_a_path()
    {
        $this->parsePath('./test.log')->shouldReturn(
            ['prefix' => './test', 'extension' => '.log']
        );
    }

    function it_safely_persists_files()
    {
        $this->open('./test.log')->exists()->shouldReturn(false);
        $this->open('./test.rev0.log')->exists()->shouldReturn(false);
        $this->open('./test.log')->contents('TEST');
        $this->exists()->shouldReturn(true);
        $this->open('./test.rev0.log')->exists()->shouldReturn(false);
        $this->open('./test.log')->contents('TEST2');
        $this->open('./test.rev0.log')->exists()->shouldReturn(true);
    }
}
