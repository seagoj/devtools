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
    public $data;

    public function __construct($data=array())
    {

        $this->status = (is_array($data) && !empty($data)) ? 'OK' : 'FAILED';
        $this->request = $_REQUEST;
        $this->data = $data;
    }
}
