<?php

namespace Core;

class Session {
    const FLASH_VALUE_KEY_NAME = "_flash";
    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key): mixed {
        return $_SESSION[$key] ?? null;
    }

    public static function clearKey(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function flush(): void {
        $_SESSION = [];
    }

    public static function flash(string $key, mixed $value): void {
        $_SESSION[self::FLASH_VALUE_KEY_NAME][$key] = $value;
    }

    public static function getFlash(string $key): mixed {
        $value = $_SESSION[self::FLASH_VALUE_KEY_NAME][$key] ?? null;
        unset($_SESSION[self::FLASH_VALUE_KEY_NAME][$key]);
        return $value;
    }
}