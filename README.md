# Csrf Protection

PSR-7 and PSR-15 based CSRF protection for HTTP requests

## Table of Contents

1. [Installation](#installation)
1. [Usage](#usage)
1. [Resources](#resources)
1. [API Documentation](#api-documentation)

## Installation

```bash
composer require phpolar/csrf-protection
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

## [API Documentation](https://phpolar.github.io/csrf-protection-api/)

[Back to top](#csrf-protection)