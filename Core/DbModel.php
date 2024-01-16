<?php

/**
 * Class used to interact with database.
 * To create a model, create a class file in *models* folder.
 * The model class you create has to extend this class.
 * On the model class you create, define a constant for the table name.
 * Like this:
 * * class User extends DbModel {
 * *      protected const TABLE_NAME = "users";
 * * }
 * 
 * * METHODS:
 * ?       findById(int $id): array|null;
 * ?       findOne(array ...$where): array|null;
 * ?       findMany(array ...$where): array|null; 
 * ?       all(): array|null;
 * ?       create(array $attributes): array;
 * ?       deleteById(int $id): void;
 * ?       deleteOne(array ...$where): void;
 * ?       deleteMany(array ...$where): void;
 * ?       updateById(int $id, array $attributes): void;
 */

namespace Core;
use Core\Database;

abstract class DbModel {
    protected const TABLE_NAME = "";

    protected static function form_sql_from_where(array $where, string $sql_start): array {
        $sql = $sql_start;
        $sql .= " WHERE ";
        foreach($where as $item) {
            $sql .= $item[0];
            $sql .= " ";
            $sql .= $item[1];
            $sql .= " ";
            $sql .= ":".$item[0];
            $sql .= " AND ";
        }
        $params = [];
        foreach($where as $item) {
            $params[$item[0]] = $item[2];
        }
        $sql = remove_last_occurrence($sql, " AND ");
        return [
            "sql" => $sql,
            "params" => $params
        ];
    }

    public static function findById(int $id): array|null {
        $db = new Database();
        $table_name = static::TABLE_NAME;
        $record = $db->fetch("SELECT * FROM $table_name WHERE id = :id", ["id" => $id]);
        return $record ? $record : null;
    }

    public static function findOne(array ...$where): array|null {
        $db = new Database();
        $table_name = static::TABLE_NAME;
        $sqlInfo = static::form_sql_from_where($where, "SELECT * FROM $table_name");
        $record = $db->fetch($sqlInfo["sql"], $sqlInfo["params"]);
        return $record ? $record : null;
    }

    public static function findMany(array ...$where): array|null {
        $db = new Database();
        $table_name = static::TABLE_NAME;
        $sqlInfo = static::form_sql_from_where($where, "SELECT * FROM $table_name");
        $record = $db->fetchAll($sqlInfo["sql"], $sqlInfo["params"]);
        return $record ? $record : null;
    }

    public static function all(): array|null {
        $db = new Database();
        $table_name = static::TABLE_NAME;
        $records = $db->fetch("SELECT * FROM $table_name;");
        return $records ? $records : null;
    }

    public static function create(array $attributes): array {
        $columns = implode(",", array_keys($attributes));
        $wildcards = ":" . implode(",:", array_keys($attributes));
        $table_name = static::TABLE_NAME;
        $sql = "INSERT INTO $table_name ($columns) VALUES ($wildcards);";
        $db = new Database();
        $db->query($sql, $attributes);
        $created_record = $db->fetch("SELECT * FROM $table_name WHERE id = :id", [ "id" => $db->last_id() ]);
        return $created_record ? $created_record : null;
    }

    public static function deleteById(int $id): void {
        $db = new Database();
        $table_name = static::TABLE_NAME;
        $db->query("DELETE FROM $table_name WHERE id = :id", ["id" => $id]);
    }

    public static function deleteOne(array ...$where): void {
        $db = new Database();
        $table_name = static::TABLE_NAME;
        $sqlInfo = static::form_sql_from_where($where, "DELETE FROM $table_name");
        $record = $db->query($sqlInfo["sql"] . " LIMIT 1;", $sqlInfo["params"]);
    }

    public static function deleteMany(array ...$where): void {
        $db = new Database();
        $table_name = static::TABLE_NAME;
        $sqlInfo = static::form_sql_from_where($where, "DELETE FROM $table_name");
        $record = $db->query($sqlInfo["sql"], $sqlInfo["params"]);
    }

    public static function updateById(int $id, array $attributes): void {
        $db = new Database();
        $table_name = static::TABLE_NAME;
        $set = "";
        $attributes = array_merge($attributes, ["id" => $id]);
        foreach($attributes as $key => $value) {
            $set .= $key . " = :" . $key . ", ";
        }
        $set = remove_last_occurrence($set, ", ");
        $db->query("UPDATE $table_name SET $set WHERE id = :id", $attributes);
    }
}