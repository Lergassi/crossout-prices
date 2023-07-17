<?php

namespace App\Test;

class Foo
{
    public string $msg;

    /**
     * @param string $_msg
     */
    public function __construct(string $_msg = '')
    {
        $this->msg = $_msg;
    }
}