<?php

namespace CPHM\Router;

class Response implements ResponseInterface
{
    protected array $headers;
    protected array $cookies;
    protected string $body;
    protected int $status;

    public function __construct(string|array|object $body = "", int $status = 200)
    {
        if (is_string($body))
            $this->body = $body;
        else if (is_array($body) || is_object($body))
            $this->body = json_encode($body);
        $this->status = $status;
        $this->headers = [];
        $this->cookies = [];
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function body(string $body): void
    {
        $this->body = $body;
    }

    public function json(object|array $body): void
    {
        $this->body = json_encode($body);
    }

    public function status(int $code): void
    {
        $this->status = $code;
    }

    public function cookies(array $cookies): void
    {
        $this->cookies = $cookies;
    }

    public function appendCookies(array $cookies): void
    {
        $this->cookies = array_merge($this->cookies, $cookies);
    }

    public function cookie(string $cookie, string $value, int $expires = 0, string $domain = "", bool $secure = false): void
    {
        $this->cookies[$cookie] = [
            'value' => $value,
            'expires' => $expires,
            'domain' => $domain,
            'secure' => $secure,
        ];
    }

    public function headers(array $headers): void
    {
        $this->headers = $headers;
    }

    public function appendHeaders(array $headers): void
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    public function header(string $header, string $value): void
    {
        $this->headers[$header] = $value;
    }

    public function send(bool $die = true): void
    {
        //Set headers:
        foreach ($this->headers as $header => $value) {
            header("$header: $value");
        }

        //Set Cookies:
        foreach ($this->cookies as $cookie => $data) {
            if (is_array($data)) {
                $value = $data['value'];
                $expires = isset($data['expires']) ? $data['expires'] : 0;
                $domain = isset($data['domain']) ? $data['domain'] : "";
                $secure = isset($data['secure']) ? $data['secure'] : false;
                setcookie($cookie, $value, $expires, "", $domain, $secure);
            } else {
                setcookie($cookie, $data);
            }
        }

        //Set Status
        http_response_code($this->status);

        //Send Body
        echo $this->body;

        if ($die)
            die();
    }
}