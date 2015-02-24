<?php namespace Devtools;

class HTMLFormatter extends Formatter
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
}
