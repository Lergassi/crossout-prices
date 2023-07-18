<?php

namespace App\Commands\TestCommands;

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