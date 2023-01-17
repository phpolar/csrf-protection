# Csrf Protection

PSR-7 and PSR-15 based CSRF protection for HTTP requests

## Installation

```bash
composer install phpolar/csrf-protection
```

## Usage

```php
$sessionStorage = new SessionTokenStorage();

$handler = new CsrfCheckRequestHandler($sessionStorage, $responseFactory);
$response = $handler->handle($request);
```

## Resources

1. [PSR-7](https://www.php-fig.org/psr/psr-7/)
1. [PSR-15](https://www.php-fig.org/psr/psr-15/)