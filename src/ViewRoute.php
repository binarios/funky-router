<?php

namespace FunkyRouter;

use Exception;

class ViewRoute extends AbstractRoute
{
    private string $viewPath;

    public function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
    }

    protected function handle(array $request): mixed
    {
        if (!file_exists($this->viewPath)) {
            throw new Exception("View-Datei {$this->viewPath} nicht gefunden.");
        }

        extract($request);
        ob_start();
        include $this->viewPath;
        return ob_get_clean();
    }
}