<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\CsrfToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

/**
 * Attaches the request id to forms and links using a pattern match and replace algorithm
 */
final class ResponseFilterPatternStrategy implements ResponseFilterStrategyInterface
{
    public function __construct(
        private CsrfToken $token,
        private StreamFactoryInterface $streamFactory,
        private string $requestId = REQUEST_ID_KEY,
    ) {
    }

    public function algorithm(
        ResponseInterface $response,
    ): ResponseInterface {
        $contents = $response->getBody()->getContents();
        $writeStream = $this->streamFactory->createStream();
        $result = preg_replace(
            [
                "/<form(.*?)>(.*?)<\/form>/s",
                "/<a href=(.*?)\?(.*?)>(.*?)<\/a>/s",
            ],
            [
                sprintf(
                    "<form$1>$2\n    <input type=\"hidden\" name=\"%s\" value=\"%s\" />\n</form>",
                    $this->requestId,
                    $this->token->asString(),
                ),
                sprintf(
                    "<a href=$1?%s=%s&$2>$3</a>",
                    $this->requestId,
                    $this->token->asString(),
                )
            ],
            $contents,
        );
        $writeStream->write($result ?? "");
        $writeStream->rewind();
        return $response->withBody($writeStream);
    }
}
