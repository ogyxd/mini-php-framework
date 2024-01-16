<?php

function uuid() {
    // Generate 16 bytes of random data
    $data = random_bytes(16);

    // Set the version (4) and variant bits
    $data[6] = chr((ord($data[6]) & 0x0F) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3F) | 0x80);

    // Convert to hexadecimal format
    $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

    return $uuid;
}

function remove_last_occurrence($main_string, $substring) {
    $last_occurrence_index = strrpos($main_string, $substring);

    if ($last_occurrence_index !== false) {
        $result = substr_replace($main_string, "", $last_occurrence_index, strlen($substring));
        return $result;
    } else {
        // Substring not found, return the original string
        return $main_string;
    }
}