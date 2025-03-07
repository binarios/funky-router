<?php

namespace FunkyRouter;

class CallbackRoute extends AbstractRoute
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    protected function handle(array $request): mixed
    {
        return call_user_func($this->callback, $request);
    }
}