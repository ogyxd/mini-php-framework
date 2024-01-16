<?php

namespace Core;

class Router {
    protected array $routes = [
        "GET" => [],
        "POST" => [],
        "PATCH" => [],
        "PUT" => [],
        "DELETE" => []
    ];

    protected function register_route(string $route, string $method, mixed $controller, array $middleware): void {
        $this->routes[$method][$route] = [
            "controller" => $controller,
            "middleware" => $middleware
        ];
    }
    public function get(string $route, mixed $controller, array $middleware = []): void {
        $this->register_route($route, "GET", $controller, $middleware);
    }

    public function post(string $route, mixed $controller, array $middleware = []): void {
        $this->register_route($route, "POST", $controller, $middleware);
    }

    public function patch(string $route, mixed $controller, array $middleware = []): void {
        $this->register_route($route, "PATCH", $controller, $middleware);
    }

    public function put(string $route, mixed $controller, array $middleware = []): void {
        $this->register_route($route, "PUT", $controller, $middleware);
    }

    public function delete(string $route, mixed $controller, array $middleware = []): void {
        $this->register_route($route, "DELETE", $controller, $middleware);
    }

    public static function show_404(): void {
        require VIEWS_PATH . "404.view.php";
        exit;
    }

    public function use(string $middleware): void {
        foreach($this->routes as $key => $value) {
            foreach($this->routes[$key] as &$route) {
                array_unshift($route["middleware"], $middleware);
            }
        }
    }

    public function run_middleware(string $middleware, Action $action): void {
        $middleware::execute($action);
    }

    public function resolve(): void {
        $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
        $method = $_POST["_method"] ?? $_SERVER["REQUEST_METHOD"];

        if (!array_key_exists($method, $this->routes)) $this->show_404();
        if (!array_key_exists($uri, $this->routes[$method])) $this->show_404();

        $route = $this->routes[$method][$uri];

        $data = [
            "url" => $_SERVER["REQUEST_URI"],
            "body" => $method == "GET" ? $_GET : $_POST
        ];
        $action = new Action($data);

        foreach($route["middleware"] as $middleware) {
            $this->run_middleware($middleware, $action);
        }

        $controller = null;
        $methodName = "";
        if (is_array($route["controller"])) {
            $controller = new $route["controller"][0];
            $methodName = $route["controller"][1];
        }

        if (!method_exists($controller, $methodName)) $this->show_404();

        call_user_func([$controller, $methodName], $action);
    }   
}