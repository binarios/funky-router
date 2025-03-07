<?php

namespace FunkyRouter;

interface RouteInterface
{
    public function run(array $request = []): mixed;
    public function withData(array $data): static;
    public function addMiddleware(MiddlewareInterface $middleware): static;
}