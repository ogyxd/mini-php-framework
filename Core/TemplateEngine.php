<?php 

namespace Core;

class TemplateEngine {
    protected array $props = [];
    protected const DEFAULT_TITLE = "App";

    public function __construct(array $props = []) {
        $this->props = $props;
    }

    public function set_title(string $title): void {
        $this->title = $title;
    }

    public function compile(string $template): string {
        extract($this->props);
        $template = preg_replace("/\?\{\{\s*(.*?)\s*\}\}/", "<?php echo $$1 ?? ''; ?>", $template);
        $template = preg_replace("/\{\{\s*(.*?)\s*\}\}/", "<?php echo $$1; ?>", $template);
        $template = preg_replace("/\{#if (.*?)\}/", "<?php if ($1): ?>", $template);
        $template = preg_replace("/\{#else\}/", "<?php else: ?>", $template);
        $template = preg_replace("/\{#endif\}/", "<?php endif; ?>", $template);
        $template = preg_replace("/\{#isset (.*?)\}/", "<?php if (isset($$1)) : ?>", $template);
        $template = preg_replace("/\{#\? (.*?)\}/", "<?php if (isset($$1)) : ?>", $template);
        $template = preg_replace("/\{#each (.*?): (.*?)\}/", "<?php foreach ($$1 as $$2): ?>", $template);
        $template = preg_replace("/\{#endeach\}/", "<?php endforeach; ?>", $template);
        return $template;
    }

    public function page(string $view, string $layout = "main"): string {
        extract($this->props);
        $layoutContent = file_get_contents(VIEWS_PATH . "layouts/$layout.layout.php");
        $viewPath = VIEWS_PATH . $view . ".view.php";
        if (!file_exists($viewPath)) {
            throw new \Exception("No view found with name: {$view}.view.php");
        }
        $viewContent = file_get_contents($viewPath);
        preg_match("/\{#title (.*?)\}/", $viewContent, $matches);
        if ($matches) $viewContent = str_replace($matches[0], "", $viewContent);
        $layoutContent = str_replace("{title}", $matches[1] ?? self::DEFAULT_TITLE, $layoutContent);
        $page = str_replace("<content/>", $viewContent, $layoutContent);
        $cached = VIEWS_PATH . "cache/current.php";
        file_put_contents($cached, $this->compile($page));
        return $cached;
    }
}