<?php

namespace Devtools;

class Service extends Response implements IService
{
    function __construct()
    {
        parent::__construct();

        $this->data(
            array('param1'=>'response1')
        );
    }
}
