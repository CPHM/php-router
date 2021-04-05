<?php

namespace CPHM\Router;

interface RequestInterface
{
    public function body(): string;

    public function json(): array;

    public function urlencoded(): array;

    public function headers(): array;

    public function cookies(): array;

    public function queryString(): string;

    public function queries(): array;

    public function hasHeader(string $header): bool;

    public function hasCookie(string $cookie): bool;

    public function hasQuery(string $query): bool;

    public function header(string $header): string|null;

    public function cookie(string $cookie): string|null;

    public function query(string $query): string|null;

    public function method(): string;

    public function url(): string;

    public function ip(): string;

    public function https(): bool;
}