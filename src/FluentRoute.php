<?php

namespace FunkyRouter;

use Exception;

class FluentRoute
{
    protected string $path;
    protected ?AbstractRoute $route = null;
    protected array $data = [];
    protected array $middleware = [];

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function callback(callable $callback): self
    {
        $this->route = new CallbackRoute($callback);
        return $this;
    }

    public function view(string $viewPath): self
    {
        $this->route = new ViewRoute($viewPath);
        return $this;
    }

    public function controller(string $controller, string $action): self
    {
        $this->route = new ControllerRoute($controller, $action);
        return $this;
    }

    public function default(): self
    {
        $this->route = new DefaultRoute();
        return $this;
    }

    public function data(array $data): self
    {
        if ($this->route) {
            $this->route->withData($data);
        } else {
            $this->data = array_merge($this->data, $data);
        }
        return $this;
    }

    public function middleware(MiddlewareInterface $middleware): self
    {
        if ($this->route) {
            $this->route->addMiddleware($middleware);
        } else {
            $this->middleware[] = $middleware;
        }
        return $this;
    }

    public function getRoute(): AbstractRoute
    {
        if (!$this->route) {
            throw new Exception("Kein Routentyp für den Pfad {$this->path} gesetzt.");
        }

        if (!empty($this->data)) {
            $this->route->withData($this->data);
        }

        foreach ($this->middleware as $m) {
            $this->route->addMiddleware($m);
        }

        return $this->route;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}