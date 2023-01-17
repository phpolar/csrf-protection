<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Tests\Stubs;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class RequestStub implements ServerRequestInterface
{
    public function __construct(
        private string $method = "GET",
        private string $url = "",
        private array $queryParams = [],
        private array|object $parsedBody = []
    ) {
    }

    private static function t()
    {
        throw new Exception("Not implemented");
    }

    public function getAttribute($name, $default = null)
    {
        self::t();
    }

    public function getAttributes()
    {
        self::t();
    }

    public function getBody()
    {
        self::t();
    }

    public function getCookieParams()
    {
        self::t();
    }

    public function getHeader($name)
    {
        self::t();
    }

    public function getHeaderLine($name)
    {
        self::t();
    }

    public function getHeaders()
    {
        self::t();
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function getProtocolVersion()
    {
        self::t();
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }


    public function getRequestTarget()
    {
        self::t();
    }

    public function getServerParams()
    {
        self::t();
    }

    public function getUploadedFiles()
    {
        self::t();
    }

    public function getUri()
    {
        self::t();
    }

    public function hasHeader($name)
    {
        self::t();
    }

    public function withAddedHeader($name, $value)
    {
        self::t();
    }

    public function withAttribute($name, $value)
    {
        self::t();
    }

    public function withBody(StreamInterface $body)
    {
        self::t();
    }

    public function withCookieParams(array $cookies)
    {
        self::t();
    }

    public function withHeader($name, $value)
    {
        self::t();
    }

    public function withMethod($method)
    {
        $copy = clone $this;
        $copy->method = $method;
        return $copy;
    }

    public function withoutAttribute($name)
    {
        self::t();
    }

    public function withoutHeader($name)
    {
        self::t();
    }

    public function withParsedBody($data)
    {
        $copy = clone $this;
        $copy->parsedBody = $data;
        return $copy;
    }

    public function withProtocolVersion($version)
    {
        self::t();
    }

    public function withQueryParams(array $query)
    {
        $copy = clone $this;
        $copy->queryParams = $query;
        return $copy;
    }

    public function withRequestTarget($requestTarget)
    {
        self::t();
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        self::t();
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        self::t();
    }
}
