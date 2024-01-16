<?php

use Core\ENV;

define("DOC_ROOT", __DIR__ . "/../");
define("VIEWS_PATH", DOC_ROOT . "views/");

require DOC_ROOT . "Core/functions.php";

if (session_status() === PHP_SESSION_NONE) session_start();

spl_autoload_register(function ($className) {
    $className = str_replace('\\', '/', $className);
    require DOC_ROOT . $className . '.php';
});

ENV::load();

require DOC_ROOT . "routes.php";