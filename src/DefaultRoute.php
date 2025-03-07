<?php

namespace FunkyRouter;

class DefaultRoute extends AbstractRoute
{
    protected function handle(array $request): mixed
    {
        http_response_code(404);
        return '404 - Seite nicht gefunden';
    }
}