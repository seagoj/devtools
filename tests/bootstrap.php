<?php

function loader($class)
{
    $file = $class . '.php';
    if (file_exists($file)) {
        require $file;
    } else if (file_exists('lib/Devtools/'.$file)) {
        require 'lib/Devtools/'.$file;        
    }
}

spl_autoload_register('loader');
