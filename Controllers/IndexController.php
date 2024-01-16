<?php

namespace Controllers;
use Core\Action;

class IndexController {
    public function home(Action $action) {
        $action->render("index");
    }
}