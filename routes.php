<?php

use Controllers\IndexController;

$router = new Core\Router();

$router->get("/", [IndexController::class, "home"]);

$router->resolve();