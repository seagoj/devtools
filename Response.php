<?php

namespace Devtools;

/**
 * Defines response object for AJAX and API returns
 **/
namespace Devtools;

class Response
{
    public $status;
    public $request;
    public $message;

    public function __construct($data=array())
    {
        $this->status = 'OK';
        $this->request = $_REQUEST;
        if (!empty($data)) $this->data($data);
        $this->message = ($this->status ? "" : "Data could not be set\n");
    }

    public function message($msg, $error=false)
    {
        if (is_array($msg) || is_object($msg)) {
            $msg = var_export($msg, true);
        }

        $this->message .= "$msg\n";
        if($error) $this->status = 'FAILED';
    }

    public function data($data)
    {
        foreach($data as $key => $value)
        {
            $this->$key = $value;
        }
    }
    
    public function json()
    {
        header('Content-type: application/json');
        return json_encode($this);
    }
}
