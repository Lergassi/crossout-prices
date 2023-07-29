<?php

namespace App\Test;

use App\Service\ProjectPath;

class Foo
{
    public string $a;

//    public function __construct(string $a = 'default message')
    private ProjectPath $projectPath;

    public function __construct(ProjectPath $projectPath, string $a = 'default message')
//    public function __construct(ProjectPath $projectPath, string $a)
//    public function __construct(string $a)
//    public function __construct(string $a = 'default message')
    {
        $this->a = $a;
        $this->projectPath = $projectPath;
    }
}