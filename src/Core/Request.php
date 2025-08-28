<?php

namespace App\Core;

class Request
{
    private array $params = [];
    private array $query = [];
    private array $body = [];
    private string $method;
    private string $path;
    
    public function __construct()
    {
        $this->query = $_GET;
        $this->body = $_POST;
        $this->params = array_merge($this->query, $this->body);
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path = $this->parsePath();
    }
    
    private function parsePath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        
        return $path;
    }
    
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }
    
    public function post(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }
    
    public function all(): array
    {
        return $this->params;
    }
    
    public function only(array $keys): array
    {
        return array_intersect_key($this->params, array_flip($keys));
    }
    
    public function has(string $key): bool
    {
        return isset($this->params[$key]);
    }
    
    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }
    
    public function method(): string
    {
        return $this->method;
    }
    
    public function getMethod(): string  // Added this method
    {
        return $this->method;
    }
    
    public function getPath(): string     // Added this method
    {
        return $this->path;
    }
    
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }
    
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }
    
    public function ip(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '';
        }
    }
    
    public function header(string $key, mixed $default = null): mixed
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$key] ?? $default;
    }
    
    public function isAjax(): bool
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function isSecure(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
            || $_SERVER['SERVER_PORT'] == 443;
    }
}