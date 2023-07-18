<?php

namespace App\Services;

class Database
{
    private \PDO $_pdo;

    public function __construct(string $host, string $dbName, string $user, string $password)
    {
        $this->_pdo = new \PDO(
            sprintf('mysql:host=%s;dbname=%s', $host, $dbName),
            $user,
            $password,
            [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->_pdo;
    }
}