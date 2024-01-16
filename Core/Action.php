<?php

namespace Core;

use Core\Session;

class Action {
    protected array $renderProps = [];
    protected array $reqData;
    protected array $data = [];

    public function __construct(array $reqData) {
        $this->reqData = $reqData;
    }
    public function redirect(string $url): void {
        header("Location: " . $url);
        exit;
    }

    public function refresh(): void {
        header("Refresh: 0");
        exit;
    }

    public function render(string $view, string $layout = "main"): void {
        extract($this->renderProps);
        $t = new TemplateEngine($this->renderProps);
        $cached = $t->page($view, $layout);
        require $cached;
        if (ENV::get("CLEAR_VIEW_CACHE_FILE") == "true") {
            file_put_contents($cached, "");
        }
    }

    public function props(array $props): self {
        $this->renderProps = array_merge($this->renderProps, $props);
        return $this;
    }

    public function show_404(): void {
        Router::show_404();
    }

    public function dd(mixed $value): void {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
        exit;
    }

    public function show_errors($old, $errors, $url): void {
        Session::flash("errors", $errors);
        Session::flash("old", $old);
        $this->redirect($url);
    }

    public function body(): array {
        return $this->reqData["body"];
    }

    public function url(): string|null {
        return $this->reqData["url"];
    }

    public function setData(string $key, mixed $data): void {
        $this->data[$key] = $data;
    }

    public function data(string $key): mixed {
        $data = $this->data[$key] ?? null;
        return $data ? $data : null;
    }

    public function sendStatus(int $code): void {
        http_response_code($code);
    }
}