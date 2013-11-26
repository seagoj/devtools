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
    private $language;

    public function __construct($data=array())
    {
        $this->language = (isset($_REQUEST['language']) ? strtolower($_REQUEST['language']) : 'javascript');
        $this->status = true;
        $this->request = $_REQUEST;
        if (!empty($data)) $this->data($data);
        $this->message = ($this->status ? array() : array('Data could not be set'));
    }

    public function message($msg, $error=false)
    {
        if (is_array($msg) || is_object($msg)) {
            $msg = var_export($msg, true);
        }

        array_push($this->message, $msg);
        if($error) $this->status = false;
    }

    public function data($data)
    {
        foreach($data as $key => $value)
        {
            $this->$key = $value;
        }
    }
    
    public function send()
    {
        switch ($this->language) {
            case 'php':
                return $this;
                break;
            case 'javascript':
                header('Content-type: application/json');
                $this->status = ($this->status ? 'OK' : 'FAILED');
                $this->message = implode("\n", $this->message);
                return json_encode($this);
                break;
            default:
                throw new InvalidArgumentException("$this->language is not a valid language.");
                break;
        }
    }
}
