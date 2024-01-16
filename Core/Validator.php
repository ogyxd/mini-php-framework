<?php

namespace Core;

class Validator {
    public static function string(string $string, int $min = 1, int $max = PHP_INT_MAX): bool {
        if (!is_string($string)) return false;
        if (strlen(trim($string)) < $min) return false;
        if (strlen(trim($string)) > $max) return false;
        return true;
    }

    public static function email(string $email): bool {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        return true;
    }

    public static function username(string $username): bool {
        $username = trim($username);
        preg_match_all("/[A-Za-z0-9_\.]/", $username, $matches);
        return implode("", $matches[0]) === $username;
    }
}