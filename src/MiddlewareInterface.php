<?php

namespace FunkyRouter;

interface MiddlewareInterface
{
    public function handle(array $request, callable $next): mixed;
}