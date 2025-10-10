<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Tests\DataProviders;

use Generator;
use DateTimeImmutable;
use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as ResponseCode;
use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

final class CsrfCheckDataProvider
{
    public static function tokenNotExists(): Generator
    {
        yield [
            new RequestStub("POST"),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "POST"
        ];
        yield [
            (new RequestStub("POST"))->withParsedBody(["name" => "something"]),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "POST"
        ];
        yield [
            (new RequestStub("POST"))->withParsedBody(null),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "POST"
        ];
        yield [
            (new RequestStub("POST"))->withQueryParams([]),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "POST"
        ];
        yield [
            (new RequestStub("POST"))->withParsedBody((object) ""),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "POST"
        ];
        yield [
            new RequestStub("PUT"),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "PUT"
        ];
        yield [
            (new RequestStub("GET"))->withQueryParams(["name" => "something"]),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "GET"
        ];
        yield [
            (new RequestStub("get"))->withQueryParams(["name" => "something"]),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "get"
        ];
        yield [
            new RequestStub("DELETE"),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "DELETE"
        ];
        yield [
            new RequestStub("delete"),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "delete"
        ];
        yield [
            new RequestStub("post"),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "post"
        ];
        yield [
            new RequestStub("put"),
            new MemoryTokenStorageStub(),
            new ResponseFactoryStub(),
            "put"
        ];
    }

    public static function tokenExpired(): array
    {
        $expiredToken = new CsrfToken(new DateTimeImmutable("2000-10-10"));
        $tokenStorage = new MemoryTokenStorageStub();
        $tokenStorage->add($expiredToken);
        $requestDataWithExpiredToken = [REQUEST_ID_KEY => (string) $expiredToken];
        return [
            [
                (new RequestStub("POST"))->withParsedBody($requestDataWithExpiredToken),
                $tokenStorage,
                new ResponseFactoryStub(),
                "POST"
            ],
            [
                (new RequestStub("post"))->withParsedBody($requestDataWithExpiredToken),
                $tokenStorage,
                new ResponseFactoryStub(),
                "post"
            ],
            [
                (new RequestStub("PUT"))->withParsedBody($requestDataWithExpiredToken),
                $tokenStorage,
                new ResponseFactoryStub(),
                "PUT"
            ],
            [
                (new RequestStub("put"))->withParsedBody($requestDataWithExpiredToken),
                $tokenStorage,
                new ResponseFactoryStub(),
                "put"
            ],
            [
                new RequestStub("get", "", $requestDataWithExpiredToken),
                $tokenStorage,
                new ResponseFactoryStub(),
                "get"
            ],
            [
                new RequestStub("get", "", $requestDataWithExpiredToken),
                $tokenStorage,
                new ResponseFactoryStub(),
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
            (new RequestStub("POST"))->withParsedBody([REQUEST_ID_KEY => (string) $expiredToken]),
            $tokenStorage,
            new ResponseFactoryStub(),
            "POST",
        ];
        yield [
            (new RequestStub("post"))->withParsedBody((object) [REQUEST_ID_KEY => (string) $expiredToken]),
            $tokenStorage,
            new ResponseFactoryStub(),
            "post",
        ];
        yield [
            (new RequestStub("PUT"))->withParsedBody([REQUEST_ID_KEY => (string) $expiredToken]),
            $tokenStorage,
            new ResponseFactoryStub(),
            "PUT",
        ];
        yield [
            (new RequestStub("put"))->withParsedBody((object) [REQUEST_ID_KEY => (string) $expiredToken]),
            $tokenStorage,
            new ResponseFactoryStub(),
            "put",
        ];
        yield [
            (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => (string) $expiredToken]),
            $tokenStorage,
            new ResponseFactoryStub(),
            "GET",
        ];
        yield [
            (new RequestStub("get"))->withQueryParams([REQUEST_ID_KEY => (string) $expiredToken]),
            $tokenStorage,
            new ResponseFactoryStub(),
            "get",
        ];
        yield [
            (new RequestStub("DELETE"))->withQueryParams([REQUEST_ID_KEY => (string) $expiredToken]),
            $tokenStorage,
            new ResponseFactoryStub(),
            "DELETE",
        ];
        yield [
            (new RequestStub("delete"))->withQueryParams([REQUEST_ID_KEY => (string) $expiredToken]),
            $tokenStorage,
            new ResponseFactoryStub(),
            "delete",
        ];
        $freshToken = new CsrfToken(new DateTimeImmutable("now"));
        $storageWithoutToken = new MemoryTokenStorageStub();
        yield [
            (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => (string) $freshToken]),
            $tokenStorage,
            new ResponseFactoryStub(),
            "GET",
        ];
        yield [
            (new RequestStub("get"))->withQueryParams([REQUEST_ID_KEY => (string) $freshToken]),
            $tokenStorage,
            new ResponseFactoryStub(),
            "get",
        ];
        yield [
            (new RequestStub("POST"))->withParsedBody([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWithoutToken,
            new ResponseFactoryStub(),
            "POST",
        ];
        yield [
            (new RequestStub("post"))->withParsedBody([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWithoutToken,
            new ResponseFactoryStub(),
            "posT",
        ];
        yield [
            (new RequestStub("PUT"))->withParsedBody([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWithoutToken,
            new ResponseFactoryStub(),
            "PUT",
        ];
        yield [
            (new RequestStub("put"))->withParsedBody([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWithoutToken,
            new ResponseFactoryStub(),
            "put",
        ];
        yield [
            (new RequestStub("DELETE"))->withQueryParams([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWithoutToken,
            new ResponseFactoryStub(),
            "DELETE",
        ];
        yield [
            (new RequestStub("delete"))->withQueryParams([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWithoutToken,
            new ResponseFactoryStub(),
            "delete",
        ];
        $freshToken = new CsrfToken(new DateTimeImmutable("now"));
        $nonMatchingToken = new CsrfToken(new DateTimeImmutable("now"));
        $storageWitNonMatchingToken = new MemoryTokenStorageStub();
        $storageWitNonMatchingToken->add($nonMatchingToken);
        yield [
            (new RequestStub("POST"))->withParsedBody([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWitNonMatchingToken,
            new ResponseFactoryStub(),
            "POST"
        ];
        yield [
            (new RequestStub("post"))->withParsedBody([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWitNonMatchingToken,
            new ResponseFactoryStub(),
            "post"
        ];
        yield [
            (new RequestStub("PUT"))->withParsedBody([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWitNonMatchingToken,
            new ResponseFactoryStub(),
            "PUT"
        ];
        yield [
            (new RequestStub("put"))->withParsedBody([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWitNonMatchingToken,
            new ResponseFactoryStub(),
            "put"
        ];
        yield [
            (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWitNonMatchingToken,
            new ResponseFactoryStub(),
            "GET"
        ];
        yield [
            (new RequestStub("get"))->withQueryParams([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWitNonMatchingToken,
            new ResponseFactoryStub(),
            "get"
        ];
        yield [
            (new RequestStub("DELETE"))->withQueryParams([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWitNonMatchingToken,
            new ResponseFactoryStub(),
            "DELETE"
        ];
        yield [
            (new RequestStub("delete"))->withQueryParams([REQUEST_ID_KEY => (string) $freshToken]),
            $storageWitNonMatchingToken,
            new ResponseFactoryStub(),
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
                (new RequestStub("POST"))->withParsedBody([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
                ResponseCode::Created->value,
                "POST"
            ],
            [
                (new RequestStub("post"))->withParsedBody([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
                ResponseCode::Created->value,
                "post"
            ],
            [
                (new RequestStub("PUT"))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
                ResponseCode::Ok->value,
                "PUT"
            ],
            [
                (new RequestStub("put"))->withParsedBody((object) [REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
                ResponseCode::Ok->value,
                "put"
            ],
            [
                (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
                ResponseCode::Ok->value,
                "GET"
            ],
            [
                (new RequestStub("get"))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
                ResponseCode::Ok->value,
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
                (new RequestStub("POST"))->withParsedBody([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
            ],
            [
                (new RequestStub("post"))->withParsedBody([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
            ],
            [
                (new RequestStub("post"))->withParsedBody((object) [REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
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
                (new RequestStub("DELETE"))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
            ],
            [
                (new RequestStub("delete"))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
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
                (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
            ],
            [
                (new RequestStub("get"))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
            ],
        ];
    }

    public static function noTokenWithNoQueryParamsGetRequest()
    {
        foreach (["GET", "get"] as $method) {
            yield [
                new RequestStub($method),
                new MemoryTokenStorageStub(),
                new ResponseFactoryStub(),
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
                    ->withQueryParams([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
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
                (new RequestStub("PUT"))->withParsedBody([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
            ],
            [
                (new RequestStub("put"))->withParsedBody([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
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
                (new RequestStub("HEAD"))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
            ],
            [
                (new RequestStub("head"))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
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
                (new RequestStub("HEAD"))->withQueryParams([REQUEST_ID_KEY => (string) $invalidToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
            ],
            [
                (new RequestStub("head"))->withQueryParams([REQUEST_ID_KEY => (string) $invalidToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
            ],
            [
                (new RequestStub("HEAD")),
                $tokenStorage,
                new ResponseFactoryStub(),
            ],
            [
                (new RequestStub("head")),
                $tokenStorage,
                new ResponseFactoryStub(),
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
                (new RequestStub($method))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
                ResponseCode::Ok->value,
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
                (new RequestStub($method))->withQueryParams([REQUEST_ID_KEY => (string) $invalidToken]),
                $tokenStorage,
                new ResponseFactoryStub(),
                ResponseCode::Ok->value,
            ];
        }
    }
}
