# Csrf Protection

PSR-7 and PSR-15 based CSRF protection for HTTP requests

## Installation

```bash
composer install phpolar/csrf-protection
```

## Usage

```php
$middleWare = new CsrfProtectionMiddleware(
  $routingResponse,
  $responseFactory,
  $streamFactory,
);
$response = $middleWare->process($request, $errorHandler);

// or

$middleWare = new CsrfProtectionMiddleware(
  $routingResponse,
  $responseFactory,
  $streamFactory,
  $tokenStorage,
  $logger,
);

$response = $middleWare->process($request, $errorHandler);

// or

$middleWare = new CsrfProtectionMiddleware(
  $routingResponse,
  $responseFactory,
  $streamFactory,
);

$middleware->useLogger($logger);
$middleware->useStorage($memcachedStore);

$response = $middleWare->process($request, $errorHandler);
```

## Resources

1. [PSR-7](https://www.php-fig.org/psr/psr-7/)
1. [PSR-15](https://www.php-fig.org/psr/psr-15/)