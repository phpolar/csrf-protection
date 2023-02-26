<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Tests\DataProviders;

use Generator;
use DateTimeImmutable;
use Psr\Http\Message\ResponseFactoryInterface;
use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Tests\Stubs\RequestStub;
use Phpolar\CsrfProtection\Tests\Stubs\ResponseFactoryStub;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;
use Phpolar\HttpCodes\ResponseCode;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

final class CsrfCheckDataProvider
{
    private static function getResponseFactoryStub(): ResponseFactoryInterface
    {
        return new ResponseFactoryStub();
    }

    public static function tokenNotExists(): Generator
    {
        yield [
            new RequestStub("POST"),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "POST"
        ];
        yield [
            (new RequestStub("POST"))->withParsedBody(["name" => "something"]),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "POST"
        ];
        yield [
            (new RequestStub("POST"))->withParsedBody(null),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "POST"
        ];
        yield [
            (new RequestStub("POST"))->withQueryParams([]),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "POST"
        ];
        yield [
            (new RequestStub("POST"))->withParsedBody((object) ""),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "POST"
        ];
        yield [
            new RequestStub("PUT"),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "PUT"
        ];
        yield [
            (new RequestStub("GET"))->withQueryParams(["name" => "something"]),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "GET"
        ];
        yield [
            (new RequestStub("get"))->withQueryParams(["name" => "something"]),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "get"
        ];
        yield [
            new RequestStub("DELETE"),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "DELETE"
        ];
        yield [
            new RequestStub("delete"),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "delete"
        ];
        yield [
            new RequestStub("post"),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "post"
        ];
        yield [
            new RequestStub("put"),
            new MemoryTokenStorageStub(),
            self::getResponseFactoryStub(),
            "put"
        ];
    }

    public static function tokenExpired(): array
    {
        $expiredToken = new CsrfToken(new DateTimeImmutable("2000-10-10"));
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($expiredToken);
        return [
            [
                (new RequestStub("POST"))->withParsedBody([REQUEST_ID_KEY => $expiredToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                "POST"
            ],
            [
                (new RequestStub("post"))->withParsedBody([REQUEST_ID_KEY => $expiredToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                "post"
            ],
            [
                (new RequestStub("PUT"))->withParsedBody([REQUEST_ID_KEY => $expiredToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                "PUT"
            ],
            [
                (new RequestStub("put"))->withParsedBody([REQUEST_ID_KEY => $expiredToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                "put"
            ],
            [
                (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => $expiredToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                "GET"
            ],
            [
                (new RequestStub("get"))->withQueryParams([REQUEST_ID_KEY => $expiredToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                "get"
            ],
        ];
    }

    public static function invalidToken(): Generator
    {
        $expiredToken = new CsrfToken(new DateTimeImmutable("2000-10-10"));
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($expiredToken);
        yield [
            (new RequestStub("POST"))->withParsedBody([REQUEST_ID_KEY => $expiredToken->asString()]),
            $tokenStorage,
            self::getResponseFactoryStub(),
            "POST",
        ];
        yield [
            (new RequestStub("post"))->withParsedBody((object) [REQUEST_ID_KEY => $expiredToken->asString()]),
            $tokenStorage,
            self::getResponseFactoryStub(),
            "post",
        ];
        yield [
            (new RequestStub("PUT"))->withParsedBody([REQUEST_ID_KEY => $expiredToken->asString()]),
            $tokenStorage,
            self::getResponseFactoryStub(),
            "PUT",
        ];
        yield [
            (new RequestStub("put"))->withParsedBody((object) [REQUEST_ID_KEY => $expiredToken->asString()]),
            $tokenStorage,
            self::getResponseFactoryStub(),
            "put",
        ];
        yield [
            (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => $expiredToken->asString()]),
            $tokenStorage,
            self::getResponseFactoryStub(),
            "GET",
        ];
        yield [
            (new RequestStub("get"))->withQueryParams([REQUEST_ID_KEY => $expiredToken->asString()]),
            $tokenStorage,
            self::getResponseFactoryStub(),
            "get",
        ];
        yield [
            (new RequestStub("DELETE"))->withQueryParams([REQUEST_ID_KEY => $expiredToken->asString()]),
            $tokenStorage,
            self::getResponseFactoryStub(),
            "DELETE",
        ];
        yield [
            (new RequestStub("delete"))->withQueryParams([REQUEST_ID_KEY => $expiredToken->asString()]),
            $tokenStorage,
            self::getResponseFactoryStub(),
            "delete",
        ];
        $freshToken = new CsrfToken(new DateTimeImmutable("now"));
        $storageWithoutToken = new MemoryTokenStorageStub();
        yield [
            (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => $freshToken->asString()]),
            $tokenStorage,
            self::getResponseFactoryStub(),
            "GET",
        ];
        yield [
            (new RequestStub("get"))->withQueryParams([REQUEST_ID_KEY => $freshToken->asString()]),
            $tokenStorage,
            self::getResponseFactoryStub(),
            "get",
        ];
        yield [
            (new RequestStub("POST"))->withParsedBody([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWithoutToken,
            self::getResponseFactoryStub(),
            "POST",
        ];
        yield [
            (new RequestStub("post"))->withParsedBody([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWithoutToken,
            self::getResponseFactoryStub(),
            "posT",
        ];
        yield [
            (new RequestStub("PUT"))->withParsedBody([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWithoutToken,
            self::getResponseFactoryStub(),
            "PUT",
        ];
        yield [
            (new RequestStub("put"))->withParsedBody([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWithoutToken,
            self::getResponseFactoryStub(),
            "put",
        ];
        yield [
            (new RequestStub("DELETE"))->withQueryParams([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWithoutToken,
            self::getResponseFactoryStub(),
            "DELETE",
        ];
        yield [
            (new RequestStub("delete"))->withQueryParams([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWithoutToken,
            self::getResponseFactoryStub(),
            "delete",
        ];
        $freshToken = new CsrfToken(new DateTimeImmutable("now"));
        $nonMatchingToken = new CsrfToken(new DateTimeImmutable("now"));
        $storageWitNonMatchingToken = new MemoryTokenStorageStub();
        $storageWitNonMatchingToken->add($nonMatchingToken);
        yield [
            (new RequestStub("POST"))->withParsedBody([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWitNonMatchingToken,
            self::getResponseFactoryStub(),
            "POST"
        ];
        yield [
            (new RequestStub("post"))->withParsedBody([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWitNonMatchingToken,
            self::getResponseFactoryStub(),
            "post"
        ];
        yield [
            (new RequestStub("PUT"))->withParsedBody([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWitNonMatchingToken,
            self::getResponseFactoryStub(),
            "PUT"
        ];
        yield [
            (new RequestStub("put"))->withParsedBody([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWitNonMatchingToken,
            self::getResponseFactoryStub(),
            "put"
        ];
        yield [
            (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWitNonMatchingToken,
            self::getResponseFactoryStub(),
            "GET"
        ];
        yield [
            (new RequestStub("get"))->withQueryParams([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWitNonMatchingToken,
            self::getResponseFactoryStub(),
            "get"
        ];
        yield [
            (new RequestStub("DELETE"))->withQueryParams([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWitNonMatchingToken,
            self::getResponseFactoryStub(),
            "DELETE"
        ];
        yield [
            (new RequestStub("delete"))->withQueryParams([REQUEST_ID_KEY => $freshToken->asString()]),
            $storageWitNonMatchingToken,
            self::getResponseFactoryStub(),
            "delete"
        ];
    }

    public static function validToken()
    {
        $validToken = new CsrfToken(new DateTimeImmutable(), 20000000);
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($validToken);
        return [
            [
                (new RequestStub("POST"))->withParsedBody([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::CREATED,
                "POST"
            ],
            [
                (new RequestStub("post"))->withParsedBody([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::CREATED,
                "post"
            ],
            [
                (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
                "GET"
            ],
            [
                (new RequestStub("get"))->withQueryParams([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
                "get"
            ],
        ];
    }

    public static function validTokenWithPostRequest()
    {
        $validToken = new CsrfToken(new DateTimeImmutable(), 20000000);
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($validToken);
        return [
            [
                (new RequestStub("POST"))->withParsedBody([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::CREATED,
            ],
            [
                (new RequestStub("post"))->withParsedBody([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::CREATED,
            ],
            [
                (new RequestStub("post"))->withParsedBody((object) [REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::CREATED,
            ],
        ];
    }

    public static function validTokenWithDeleteRequest()
    {
        $validToken = new CsrfToken(new DateTimeImmutable(), 20000000);
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($validToken);
        return [
            [
                (new RequestStub("DELETE"))->withQueryParams([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
            [
                (new RequestStub("delete"))->withQueryParams([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
        ];
    }

    public static function validTokenWithGetRequest()
    {
        $validToken = new CsrfToken(new DateTimeImmutable(), 20000000);
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($validToken);
        return [
            [
                (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
            [
                (new RequestStub("get"))->withQueryParams([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
        ];
    }

    public static function noTokenWithNoQueryParamsGetRequest()
    {
        foreach (["GET", "get"] as $method) {
            yield [
                new RequestStub($method),
                new MemoryTokenStorageStub(),
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ];
        }
    }

    public static function validTokenWithOtherMethodsRequest()
    {
        $validToken = new CsrfToken(new DateTimeImmutable(), 20000000);
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($validToken);
        $notImplementedMethods = [
            "connect",
            "CONNECT",
            "patch",
            "PATCH",
            "trace",
            "TRACE",
        ];
        foreach ($notImplementedMethods as $notImplementedmethod) {
            yield [
                (new RequestStub($notImplementedmethod))
                    ->withQueryParams([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::METHOD_NOT_ALLLOWED,
            ];
        }
    }

    public static function validTokenWithPutRequest()
    {
        $validToken = new CsrfToken(new DateTimeImmutable(), 20000000);
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($validToken);
        return [
            [
                (new RequestStub("PUT"))->withParsedBody([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
            [
                (new RequestStub("put"))->withParsedBody([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
        ];
    }

    public static function validTokenWithHeadRequest()
    {
        $validToken = new CsrfToken(new DateTimeImmutable(), 20000000);
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($validToken);
        return [
            [
                (new RequestStub("HEAD"))->withQueryParams([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
            [
                (new RequestStub("head"))->withQueryParams([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
        ];
    }

    public static function invalidTokenWithHeadRequest()
    {
        $invalidToken = new CsrfToken(new DateTimeImmutable("2000-10-10"));
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($invalidToken);
        return [
            [
                (new RequestStub("HEAD"))->withQueryParams([REQUEST_ID_KEY => $invalidToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
            [
                (new RequestStub("head"))->withQueryParams([REQUEST_ID_KEY => $invalidToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
            [
                (new RequestStub("HEAD")),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
            [
                (new RequestStub("head")),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ],
        ];
    }

    public static function validTokenWithOptionsRequest()
    {
        $validToken = new CsrfToken(new DateTimeImmutable(), 20000000);
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($validToken);
        foreach (
            [
                "options",
                "OPTIONS",
            ] as $method
        ) {
            yield [
                (new RequestStub($method))->withQueryParams([REQUEST_ID_KEY => $validToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ];
        }
    }

    public static function invalidTokenWithOptionsRequest()
    {
        $invalidToken = new CsrfToken(new DateTimeImmutable("2000-10-10"));
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($invalidToken);
        foreach (
            [
                "options",
                "OPTIONS",
            ] as $method
        ) {
            yield [
                (new RequestStub($method))->withQueryParams([REQUEST_ID_KEY => $invalidToken->asString()]),
                $tokenStorage,
                self::getResponseFactoryStub(),
                ResponseCode::OK,
            ];
        }
    }
}
