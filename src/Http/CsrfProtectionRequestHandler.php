<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\HttpCodes\ResponseCode;

use const Phpolar\CsrfProtection\FORBIDDEN_REQUEST_MESSAGE;
use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

/**
 * Refuses to process an invalid request
 */
final class CsrfProtectionRequestHandler implements RequestHandlerInterface
{
    public const BAD_REQUEST = "Bad Request";
    public const CREATED = "Created";
    public const FORBIDDEN = "Forbidden";
    public const OK = "OK";
    public const METHOD_NOT_ALLOWED = "Method Not Allowed";
    private ServerRequestInterface $request;

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private AbstractTokenStorage $storage,
        private string $requestId = REQUEST_ID_KEY,
        private string $forbiddenMsg = FORBIDDEN_REQUEST_MESSAGE,
    ) {
    }

    /**
     * Determines the response based on the validity
     * of the request.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        return match (strtoupper($request->getMethod())) {
            "DELETE" => match ($this->queryParamsHasRequestId()) {
                false => $this->badRequest(),
                true => $this->responseBasedOnRequestIdValidation($this->getRequestId()),
            },
            "GET" => match ($this->hasQueryParams()) {
                false => $this->success(),
                true => match ($this->queryParamsHasRequestId()) {
                    false => $this->badRequest(),
                    true => $this->responseBasedOnRequestIdValidation($this->getRequestId()),
                }
            },
            "HEAD", "OPTIONS" => $this->success(),
            "POST" => match ($this->parsedBodyHasRequestId()) {
                false => $this->badRequest(),
                true => $this->responseBasedOnPostRequestIdValidation($this->getRequestId()),
            },
            "PUT" => match ($this->parsedBodyHasRequestId()) {
                false => $this->badRequest(),
                true => $this->responseBasedOnRequestIdValidation($this->getRequestId()),
            },
            default => $this->methodNotAllowed(),
        };
    }

    /**
     * Returns a 'Bad Request' response
     */
    private function badRequest(): ResponseInterface
    {
        return $this->create(
            ResponseCode::BAD_REQUEST,
            self::BAD_REQUEST,
        );
    }

    /**
     * Creates a response
     */
    private function create(
        int $responseCode,
        string $reason
    ): ResponseInterface {
        return $this->responseFactory->createResponse(
            (int) $responseCode,
            $reason
        );
    }

    /**
     * Returns a 'CREATED' response
     */
    private function created(): ResponseInterface
    {
        return $this->create(
            ResponseCode::CREATED,
            self::CREATED,
        );
    }

    /**
     * @return array<string,string>|object
     */
    private function data(): array|object
    {
        $request = $this->request;
        return empty($request->getParsedBody()) === true ? $request->getQueryParams() : $request->getParsedBody();
    }

    /**
     * Returns a 'Forbidden' response
     */
    private function forbidden(): ResponseInterface
    {
        return $this->create(
            ResponseCode::FORBIDDEN,
            self::FORBIDDEN,
        );
    }

    /**
     * Retrieves the token from the request data or an empty string
     */
    private function getRequestId(): string
    {
        $data = $this->data();
        if (is_object($data) === false) {
            return $data[$this->requestId];
        }
        // @codeCoverageIgnoreStart
        if (property_exists($data, $this->requestId) === false) {
            return "";
        }
        // @codeCoverageIgnoreEnd
        return $data->{$this->requestId};
    }

    /**
     * Determines if the request has query params
     */
    private function hasQueryParams(): bool
    {
        return count($this->request->getQueryParams()) > 0;
    }

    /**
     * Returns a 'Method Not Allowed' response
     */
    private function methodNotAllowed(): ResponseInterface
    {
        return $this->create(
            ResponseCode::METHOD_NOT_ALLLOWED,
            self::METHOD_NOT_ALLOWED
        );
    }

    /**
     * Returns a 'OK' response
     */
    private function success(): ResponseInterface
    {
        return $this->create(
            ResponseCode::OK,
            self::OK
        );
    }

    /**
     * Retrieves a token from the parsed body of the request
     */
    private function parsedBodyHasRequestId(): bool
    {
        $data = $this->request->getParsedBody();
        if (is_object($data) === false) {
            return isset($data[$this->requestId]) === true;
        }
        return property_exists($data, $this->requestId);
    }

    private function responseBasedOnPostRequestIdValidation(
        string $token,
    ): ResponseInterface {
        return match ($this->storage->isValid($token)) {
            true => $this->created(),
            false => $this->forbidden(),
        };
    }

    private function responseBasedOnRequestIdValidation(
        string $token,
    ): ResponseInterface {
        return match ($this->storage->isValid($token)) {
            true => $this->success(),
            false => $this->forbidden(),
        };
    }

    /**
     * Retrieves a token from the query params of the request
     */
    private function queryParamsHasRequestId(): bool
    {
        return isset($this->request->getQueryParams()[$this->requestId]);
    }
}
