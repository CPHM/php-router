<?php

namespace CPHM\Router;

class Request implements RequestInterface
{
    protected string $method;
    protected string $url;
    protected string $ip;
    protected bool $https;
    protected string $body;
    protected array $headers;
    protected array $cookies;
    protected string $queryString;
    protected array $queries;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->url = $_SERVER['REQUEST_URI'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->ip = !empty($_SERVER['HTTPS']);
        $this->body = file_get_contents('php://input');
        $this->headers = apache_request_headers() === false ? [] : apache_request_headers();   
        $this->cookies = $_COOKIE;
        $this->queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        $this->queries = [];
    }

    public function body(): string
    {
        return $this->body;
    }

    public function json(): array
    {
        return json_decode($this->body, true);
    }

    public function urlencoded(): array
    {
        $result = array();
        parse_str($this->body, $result);
        return $result;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function cookies(): array
    {
        return $this->cookies;
    }

    public function queryString(): string
    {
        return $this->queryString;
    }

    public function queries(): array
    {
        $this->parseQueryString();
        return $this->queries;
    }

    public function hasHeader(string $header): bool
    {
        return array_key_exists($header, $this->headers);
    }

    public function hasCookie(string $cookie): bool
    {
        return array_key_exists($cookie, $this->cookies);
    }

    public function hasQuery(string $query): bool
    {
        $this->parseQueryString();
        return array_key_exists($query, $this->queries);
    }

    public function header(string $header): string|null
    {
        return array_key_exists($header, $this->headers) ? $this->headers[$header] : null;
    }

    public function cookie(string $cookie): string|null
    {
        return array_key_exists($cookie, $this->cookies) ? $this->cookies[$cookie] : null;
    }

    public function query(string $query): string|null
    {
        $this->parseQueryString();
        return array_key_exists($query, $this->queries) ? $this->queries[$query] : null;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function ip(): string
    {
        return $this->ip;
    }

    public function https(): bool
    {   
        return $this->https;
    }

    private function parseQueryString()
    {
        static $parsed = false;
        if (!$parsed) {
            parse_str($this->queryString, $this->queries);
            $parsed = true;
        }
    }
}