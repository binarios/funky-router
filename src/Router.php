<?php declare(strict_types=1);

namespace FunkyRouter;

class Router
{
    private const ALLOWED_METHODS = ['GET', 'POST', 'PUT', 'DELETE'];

    private array $routes = [];
    private string $requestUri;
    private string $requestMethod;
    private RouterError $errorHandler;

    public function __construct()
    {
        $this->errorHandler = new RouterError();
        $this->requestUri = $this->getRequestUri();
        $this->requestMethod = $this->getRequestMethod();
    }

    private function getRequestUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';
        return rtrim($uri, '/') ?: '/';
    }

    public function setRequestUri(string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';
        $this->requestUri = rtrim($uri, '/') ?: '/';
    }

    private function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function setRequestMethod(string $method): void
    {
        $method = strtoupper($method);
        if (!$this->isMethodAllowed($method)) {
            $this->errorHandler->addError("<b>Method:</b> <mark>{$method}</mark> is not allowed");
        }
        $this->requestMethod = $method;
    }

    private function isMethodAllowed(string $method): bool
    {
        return in_array($method, self::ALLOWED_METHODS, true);
    }

    private function checkError(): bool
    {
        if ($this->errorHandler->hasErrors()) {
            $this->errorHandler->showErrors();
            return true;
        }
        return false;
    }

    # Auflösen der Routen
    public function dispatch(): void
    {
        if ($this->checkError()) exit;

        echo "Dispatching route for {$this->requestMethod} => {$this->requestUri}" . PHP_EOL;
    }
}
