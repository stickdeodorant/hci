<?php

namespace App\Core;

use App\Core\Request;
use App\Core\Response;

class Router
{
    private array $routes = [];
    private Request $request;
    private Response $response;
    
    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }
    
    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }
    
    private function addRoute(string $method, string $path, $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }
    
    public function resolve(): void
    {
        $method = $this->request->getMethod();
        $path = $this->request->getPath();
        
        $handler = $this->routes[$method][$path] ?? null;
        
        if (!$handler) {
            $this->response->setStatusCode(404);
            echo "404 - Page not found";
            return;
        }
        
        if (is_callable($handler)) {
            call_user_func($handler, $this->request, $this->response);
        } elseif (is_array($handler)) {
            $controller = new $handler[0]();
            $method = $handler[1];
            call_user_func([$controller, $method], $this->request, $this->response);
        }
    }
}