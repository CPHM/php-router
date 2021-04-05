<?php

namespace CPHM\Router;

interface ResponseInterface
{
    public function getBody(): string;

    public function getHeaders(): array;

    public function getCookies(): array;

    public function body(string $body): void;

    public function json(object|array $body): void;

    public function status(int $code): void;

    public function cookies(array $cookies): void;

    public function appendCookies(array $cookies): void;

    public function cookie(string $cookie, string $value, int $expires = 0, string $domain = "", bool $secure = false): void;

    public function headers(array $headers): void;

    public function appendHeaders(array $headers): void;

    public function header(string $header, string $value): void;

    public function send(bool $die = true): void;
}