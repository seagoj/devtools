<?php namespace Devtools;

use Exception;

class File
{
    public $path;
    public $contents;
    public $name;

    public function __construct($path = null)
    {
        if (!is_null($path)) {
            $this->open($path);
        }
    }

    public function open($path)
    {
        $this->path = $path;
        $this->contents = '';
        $this->name();
        if ($this->exists()) {
            $this->read();
        }
        return $this;
    }

    public function contents($contents, $doNotOverwrite = true)
    {
        if (!$doNotOverwrite && !file_put_contents($this->path, $contents)) {
            throw new Exception('Contents could not be written to file.');
        } else {
            $this->contents = $contents;
            return $this->safePersist($this->path);
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

    public function parsePath($path)
    {
        $extensionStarts = strrpos($path, '.');

        return array(
            'prefix' => substr($path, 0, $extensionStarts),
            'extension' => substr($path, $extensionStarts)
        );
    }

    public function safePersist($path)
    {
        var_dump($path);
        var_dump(file_exists($path));
        if (!file_exists($path)) {
            file_put_contents($path, $this->contents);
        } else {
            extract($this->parsePath($path));
            $revision = 0;

            while (file_exists("{$prefix}.rev{$revision}{$extension}")) {
                $revision++;
            }
            file_put_contents("{$prefix}.rev{$revision}{$extension}", file_get_contents($path));
            file_put_contents($path, $this->contents);
        }

        return isset($revision) ? $revision : 'new';
    }
}
