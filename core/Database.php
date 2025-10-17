<?php

namespace Core;

use PDO;

class Database
{
    public PDO $connection;
    public $statement;

    public function __construct($config)
    {
        $dsn = $config['driver'] . http_build_query($config, '', ';');

        $this->connection = new PDO($dsn, 'armando', '', [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function query($query, array $params = null ): static
    {
        $this->statement = $this->connection->prepare($query);

        $this->statement->execute($params);

        return $this;
    }

    public function fetch()
    {
        return $this->statement->fetch();
    }

    public function fetchAll()
    {
        return $this->statement->fetchAll();
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }
}
