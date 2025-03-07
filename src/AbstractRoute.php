<?php

namespace FunkyRouter;

abstract class AbstractRoute implements RouteInterface
{
    protected array $data = [];
    protected array $middleware = [];

    public function withData(array $data): static
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function addMiddleware(MiddlewareInterface $middleware): static
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function run(array $request = []): mixed
    {
        $request = array_merge($request, $this->data);
        $middlewareChain = $this->buildMiddlewareChain(function (array $request) {
            return $this->handle($request);
        });

        return $middlewareChain($request);
    }

    protected function buildMiddlewareChain(callable $lastCallback): callable
    {
        $chain = $lastCallback;
        foreach (array_reverse($this->middleware) as $middleware) {
            $next = $chain;
            $chain = function (array $request) use ($middleware, $next) {
                return $middleware->handle($request, $next);
            };
        }
        return $chain;
    }

    abstract protected function handle(array $request): mixed;
}