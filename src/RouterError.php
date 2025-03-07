<?php declare(strict_types=1);

namespace FunkyRouter;

class RouterError
{
    private array $errors = [];

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function showErrors(): void
    {
        echo "<h1>Router Errors</h1>" . PHP_EOL;
        foreach ($this->errors as $error) {
            echo "<div>❌ {$error}</div>" . PHP_EOL;
        }
    }
}