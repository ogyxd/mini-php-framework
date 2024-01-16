<?php

namespace Core;
use PDO;
use PDOException;

class Database {
    protected object|null $instance = null;

    public function __construct() {
        $host = ENV::get("DB_HOST");
        $dbname = ENV::get("DB_NAME");
        $port = ENV::get("DB_PORT");

        try {
            $dsn="mysql:host={$host};dbname={$dbname};port={$port}";
            $this->instance = new PDO($dsn, ENV::get("DB_USER"), ENV::get("DB_PASS"));
            $this->instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function last_id(): int {
        return $this->instance->lastInsertId();
    }

    public function query(string $sql, array $params = []): mixed
    {
        $statement = $this->instance->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    public function fetchAll(string $sql, array $params = []): mixed
    {
        $statement = $this->query($sql, $params);
        return $statement->fetchAll();
    }

    public function fetch(string $sql, array $params = []): mixed
    {
        $statement = $this->query($sql, $params);
        return $statement->fetch();
    }
}