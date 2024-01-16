<?php

namespace Middleware;
use Core\Action;

class Example {
    public static function execute(Action $action) {
        echo "Hello from middleware.";
    }
}