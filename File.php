<?php namespace Devtools;

use Exception;

class File
{
    public $path;
    public $contents;
    public $name;

    public function __construct($path)
    {
        $this->path = $path;
        $this->name();
        if ($this->exists()) {
            $this->read();
        }
    }

    public function contents($contents)
    {
        if (!file_put_contents($this->path, $contents)) {
            throw new Exception('Contents could not be written to file.');
        }

        $this->contents = $contents;
    }

    public function exists()
    {
        return file_exists($this->path);
    }

    public function delete()
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }

    private function name()
    {
        $this->name = basename($this->path);
    }

    private function read()
    {
        $contents = file_get_contents($this->path);

        if (!$contents) {
            throw new Exception('File could not be read.');
        }

        $this->contents = $contents;
    }

    public function copyTo($newPath)
    {
        $this->path = $newPath;
        $this->name();
        $this->contents($this->contents);
    }
}
