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
    private const SAFE_METHODS = ["HEAD", "OPTIONS"];
    private const UNSAFE_METHODS = ["DELETE", "PUT", "GET", "POST"];

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
        $method = strtoupper($request->getMethod());

        if (in_array($method, self::SAFE_METHODS) === true) {
            return $this->success();
        }

        if (in_array($method, self::UNSAFE_METHODS) === true) {
            if ($method === "GET") {
                if (count($request->getQueryParams()) === 0) {
                    return $this->success();
                }
            }
            $requestId = $this->getRequestId($request);
            if ($requestId === "") {
                return $this->badRequest();
            }
            if ($this->storage->isValid($requestId) === true) {
                if ($method === "POST") {
                    return $this->create(
                        ResponseCode::CREATED,
                        self::CREATED,
                    );
                }
                return $this->success();
            }
            return $this->forbidden();
        }

        return $this->create(
            ResponseCode::METHOD_NOT_ALLLOWED,
            self::METHOD_NOT_ALLOWED
        );
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
            $responseCode,
            $reason
        );
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
    private function getRequestId(ServerRequestInterface $request): string
    {
        $parsedBody = $request->getParsedBody();
        $data = empty($parsedBody) ? $request->getQueryParams() : $parsedBody;
        if (is_object($data) === true) {
            if (property_exists($data, $this->requestId) === true) {
                return $data->{$this->requestId};
            }
            return "";
        }
        return $data[$this->requestId] ?? "";
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
}
