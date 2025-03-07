<?php

namespace FunkyRouter;

use Exception;

class ControllerRoute extends AbstractRoute
{
    private string $controller;
    private string $action;

    public function __construct(string $controller, string $action)
    {
        $this->controller = $controller;
        $this->action = $action;
    }

    protected function handle(array $request): mixed
    {
        if (!class_exists($this->controller)) {
            throw new Exception("Controller {$this->controller} existiert nicht.");
        }

        $controllerInstance = new $this->controller();

        if (!method_exists($controllerInstance, $this->action)) {
            throw new Exception("Methode {$this->action} im Controller {$this->controller} nicht vorhanden.");
        }

        return $controllerInstance->{$this->action}($request);
    }
}