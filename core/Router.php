<?php

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $regex = '#^' . str_replace('{id}', '(\d+)', $route['pattern']) . '$#';

            if (preg_match($regex, $uri, $matches)) {
                ($route['handler'])($matches);
                return;
            }
        }

        Response::error('Маршрут не найден', 404);
    }
}
