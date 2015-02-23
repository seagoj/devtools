<?php namespace Devtools;

class HTMLFormatter
{
    public function format($content, $result)
    {
        if (is_bool($result)) {
            $result = $result ? 'true' : 'false';
        }

        return is_null($result)
            ? "<p>{$content}</p>"
            : "<p>{$result}: {$content}</p>";
    }

    public function header()
    {
        return '';
    }

    public function footer()
    {
        return '';
    }
}
