<?php namespace Devtools;

class FileLogger extends Logger
{
    public function __construct($filename, Formatter $formatter)
    {
        parent::__construct();
        $this->filename = $filename;
        $this->formatter = $formatter;
    }

    public function write($content, $result = null)
    {
        return file_put_contents(
            $this->filename,
            $this->formatter->format($content, $result) . PHP_EOL,
            FILE_APPEND
        );
    }

    public function update(\SplSubject $subject)
    {
        $status = $subject->getStatus();
        $this->write($status);
    }
}
