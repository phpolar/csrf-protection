<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\CsrfToken;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

/**
 * Attaches the request id to forms and links using a single pass scan algorithm
 */
final class ResponseFilterScanStrategy implements ResponseFilterStrategyInterface
{
    private const FORM_OPEN_TAG = "<form";
    private const FORM_END_TAG = "</form>";
    private const LINK_OPEN_TAG = "<a ";
    private const QUESTION_MARK = "?";
    private const AMPERSAND = "&";
    private const LINK_HREF_ATTR = "href=";

    public function __construct(
        private CsrfToken $token,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private string $requestId = REQUEST_ID_KEY,
    ) {
    }

    public function algorithm(
        ResponseInterface $response,
    ): ResponseInterface {
        $readStream = $response->getBody();
        $writeStream = $this->streamFactory->createStream();
        $originalLocation = $readStream->tell();
        $savedChars = "";
        do {
            $savedChars = match ($this->copyNextChar($readStream, $writeStream)) {
                "<" => "<",
                "f" => $savedChars === "<" ? "<f" : "",
                "o" => $savedChars === "<f" ? "<fo" : "",
                "r" => $savedChars === "<fo" ? "<for" : "",
                "m" => $savedChars === "<for" ? "<form" : "",
                "a" => $savedChars === "<" ? "<a" : "",
                " " => $savedChars === "<a" ? "<a " : "",
                default => ""
            };
            if ($savedChars === self::FORM_OPEN_TAG) {
                $this->writeToForm($readStream, $writeStream);
                $savedChars = "";
            }
            if ($savedChars === self::LINK_OPEN_TAG) {
                $this->writeToLink($readStream, $writeStream);
                $savedChars = "";
            }
        } while ($readStream->eof() === false);
        $writeStream->rewind();
        $readStream->seek($originalLocation);
        return $this->responseFactory->createResponse()->withBody($writeStream);
    }

    private function writeToForm(StreamInterface $readStream, StreamInterface $writeStream): void
    {
        $savedFormChars = "";
        do {
            $savedFormChars = match ($this->copyNextChar($readStream, $writeStream)) {
                ">" => ">",
                default => ""
            };
        } while (
            $savedFormChars !== ">" &&
            $readStream->eof() === false
        );
        do {
            $savedFormChars = match ($this->copyNextChar($readStream, $writeStream)) {
                "<" => "<",
                "/" => $savedFormChars === "<" ? "</" : "",
                "f" => $savedFormChars === "</" ? "</f" : "",
                "o" => $savedFormChars === "</f" ? "</fo" : "",
                "r" => $savedFormChars === "</fo" ? "</for" : "",
                "m" => $savedFormChars === "</for" ? "</form" : "",
                ">" => $savedFormChars === "</form" ? "</form>" : "",
                default => ""
            };
        } while (
            $savedFormChars !== self::FORM_END_TAG &&
            $readStream->eof() === false
        );
        if ($savedFormChars === self::FORM_END_TAG) {
            $writeStream->seek(-mb_strlen(self::FORM_END_TAG), SEEK_CUR);
            $writeStream->write($this->formKey());
            $writeStream->write(self::FORM_END_TAG);
        }
    }

    private function writeToLink(StreamInterface $readStream, StreamInterface $writeStream): void
    {
        $savedLinkChars = "";
        do {
            $savedLinkChars = match ($this->copyNextChar($readStream, $writeStream)) {
                "h" => "h",
                "r" => $savedLinkChars === "h" ? "hr" : "",
                "e" => $savedLinkChars === "hr" ? "hre" : "",
                "f" => $savedLinkChars === "hre" ? "href" : "",
                "=" => $savedLinkChars === "href" ? "href=" : "",
                default => ""
            };
        } while (
            $savedLinkChars !== self::LINK_HREF_ATTR &&
            $readStream->eof() === false
        );
        do {
            $uriChar = $this->copyNextChar($readStream, $writeStream);
        } while ($uriChar !== self::QUESTION_MARK && $readStream->eof() === false);
        if ($uriChar === self::QUESTION_MARK) {
            $writeStream->write($this->linkKey());
            $writeStream->write(self::AMPERSAND);
        }
    }

    private function copyNextChar(StreamInterface $readStream, StreamInterface $writeStream): string
    {
        $char = $readStream->read(1);
        $writeStream->write($char);
        return $char;
    }

    private function linkKey(): string
    {
        return sprintf(
            "%s=%s",
            $this->requestId,
            $this->token->asString()
        );
    }

    private function formKey(): string
    {
        return sprintf(
            <<<'HTML'

                <input type="hidden" name="%s" value="%s" />

            HTML,
            $this->requestId,
            $this->token->asString()
        );
    }
}
