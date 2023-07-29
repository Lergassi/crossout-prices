<?php

namespace App\Service;

class ProjectPath
{
    private string $_projectDir;

    /**
     * @param string $_projectDir
     */
    public function __construct(string $_projectDir)
    {
        $this->_projectDir = $_projectDir;
    }

    public function build(string ...$paths): string
    {
        if (!count($paths)) return '';

        if ($paths[0][0] === '/') {
            $paths[0] = substr($paths[0], 1, strlen($paths[0]));
        }

        array_unshift(
            $paths,
            $this->_projectDir
        );

        return implode('/', $paths);
    }
}