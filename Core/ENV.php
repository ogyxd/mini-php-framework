<?php

namespace Core;

class ENV
{
    public static function load($filePath = DOC_ROOT . ".env")
    {
        // Check if the file exists
        if (!file_exists($filePath)) {
            return false;
        }

        // Read the file line by line
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Process each line
        foreach ($lines as $line) {
            // Ignore comments (lines starting with #)
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Split the line into key and value
            list($key, $value) = explode('=', $line, 2);

            // Set the environment variable
            putenv("$key=$value");
        }

        return true;
    }

    public static function get($key)
    {
        return getenv($key);
    }
}