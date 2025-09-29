<?php
// app/core/Router.php
class Router {
    protected array $routes = [];

    public function add(string $method, string $path, $handler): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => rtrim($path, '/') ?: '/',
            'handler' => $handler,
        ];
    }

    public function dispatch(string $uri, string $method): void {
        $uri = rtrim($uri, '/') ?: '/';
        $method = strtoupper($method);
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                // Check for exact match first
                if ($route['path'] === $uri) {
                    $this->callHandler($route['handler']);
                    return;
                }
                
                // Check for regex pattern match
                $pattern = '#^' . $route['path'] . '$#';
                if (preg_match($pattern, $uri, $matches)) {
                    // Remove the full match from matches array
                    array_shift($matches);
                    $this->callHandler($route['handler'], $matches);
                    return;
                }
            }
        }
        
        http_response_code(404);
        echo '404 - Not Found';
    }
    
    private function callHandler($handler, array $params = []): void {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }
        
        if (is_array($handler)) {
            [$controller, $action] = $handler;
            // Allow passing class name string; instantiate lazily
            if (is_string($controller)) {
                $controller = new $controller();
            }
            if (is_callable([$controller, $action])) {
                call_user_func_array([$controller, $action], $params);
                return;
            }
        }
    }
}
