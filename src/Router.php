<?php

namespace FunkyRouter;

use Exception;

class Router
{
    protected array $routes = [];
    protected ?AbstractRoute $defaultRoute = null;

    public function get(string $path): FluentRoute
    {
        return $this->addRoute('GET', $path);
    }

    public function post(string $path): FluentRoute
    {
        return $this->addRoute('POST', $path);
    }

    public function patch(string $path): FluentRoute
    {
        return $this->addRoute('PATCH', $path);
    }

    public function put(string $path): FluentRoute
    {
        return $this->addRoute('PUT', $path);
    }

    public function delete(string $path): FluentRoute
    {
        return $this->addRoute('DELETE', $path);
    }

    protected function addRoute(string $method, string $path): FluentRoute
    {
        $fluent = new FluentRoute($path);
        $this->routes[$method][$path] = $fluent;
        return $fluent;
    }

    public function setDefaultRoute(AbstractRoute $route): self
    {
        $this->defaultRoute = $route;
        return $this;
    }

    public function dispatch(string $uri, string $method, array $request = []): mixed
    {
        foreach ($this->routes[$method] ?? [] as $path => $route) {
            if ($this->match($path, $uri, $params)) {
                $request = array_merge($request, $params);
                return $route->getRoute()->run($request);
            }
        }

        if ($this->defaultRoute !== null) {
            return $this->defaultRoute->run($request);
        }

        throw new Exception("Keine passende Route gefunden und keine Default-Route gesetzt.");
    }

    protected function match(string $path, string $uri, &$params): bool
    {
        $path = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $path);
        $pattern = "#^$path$#";

        if (preg_match($pattern, $uri, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }
}