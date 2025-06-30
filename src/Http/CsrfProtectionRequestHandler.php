<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as ResponseCode;
use Phpolar\CsrfProtection\CsrfToken;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

/**
 * Refuses to process an invalid request
 */
final class CsrfProtectionRequestHandler implements RequestHandlerInterface
{
    private const SAFE_METHODS = ["HEAD", "OPTIONS"];
    private const UNSAFE_METHODS = ["DELETE", "PUT", "GET", "POST"];

    public function __construct(
        private CsrfToken $token,
        private AbstractTokenStorage $storage,
        private ResponseFactoryInterface $responseFactory,
        private string $requestId = REQUEST_ID_KEY,
    ) {}

    /**
     * Determines the response based on the validity
     * of the request.
     */
    public function handle(Request $request): Response
    {
        $method = strtoupper($request->getMethod());
        if (in_array($method, self::SAFE_METHODS) === true) {
            return $this->handleSafeRequest();
        }
        if (in_array($method, self::UNSAFE_METHODS) === true) {
            return $this->handleUnsafeMethods($method, $request);
        }
        return $this->methodNotAllowed();
    }

    /**
     * Returns a 'Bad Request' response
     */
    private function badRequest(): Response
    {
        return $this->responseFactory->createResponse(
            (int) ResponseCode::BadRequest->value,
            ResponseCode::BadRequest->getLabel(),
        );
    }

    /**
     * Returns a 'Created' response
     */
    private function created(): Response
    {
        return $this->responseFactory->createResponse(
            (int) ResponseCode::Created->value,
            ResponseCode::Created->getLabel(),
        );
    }

    /**
     * Returns a 'Forbidden' response
     */
    private function forbidden(): Response
    {
        return $this->responseFactory->createResponse(
            (int) ResponseCode::Forbidden->value,
            ResponseCode::Forbidden->getLabel(),
        );
    }

    private function handleSafeRequest(): Response
    {
        return $this->success();
    }

    private function handleUnsafeMethods(string $method, Request $request): Response
    {
        $queryParams = $request->getQueryParams();
        $noQueryParams = count($queryParams) === 0;
        $data = array_merge((array) $request->getParsedBody(), $queryParams);
        $noRequestId = isset($data[$this->requestId]) === false;
        $isGetRequest = $method === "GET";
        $notPostRequest = $method !== "POST";

        if ($noQueryParams === true) {
            if ($isGetRequest === true) {
                $this->storage->add($this->token);
                return $this->handleSafeRequest();
            }
        }
        if ($noRequestId === true) {
            return $this->badRequest();
        }
        if ($this->tokenIsInvalid($data) === true) {
            return $this->forbidden();
        }
        if ($notPostRequest === true) {
            $this->storage->add($this->token);
            return $this->success();
        }
        return $this->created();
    }

    /**
     * Returns a 'Method Not Allowed' response
     */
    private function methodNotAllowed(): Response
    {
        return $this->responseFactory->createResponse(
            (int) ResponseCode::MethodNotAllowed->value,
            ResponseCode::MethodNotAllowed->getLabel(),
        );
    }

    /**
     * Returns a 'OK' response
     */
    private function success(): Response
    {
        return $this->responseFactory->createResponse(
            (int) ResponseCode::Ok->value,
            ResponseCode::Ok->getLabel(),
        );
    }

    /**
     * @param array<string,string> $data
     */
    private function tokenIsInvalid(array $data): bool
    {
        return $this->storage->isValid($data[$this->requestId]) === false;
    }
}
