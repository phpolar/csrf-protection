<p align="center">
    <img width="240" src="./phpolar.svg" />
</p>

# Csrf Protection

PSR-7 and PSR-15 based CSRF protection for HTTP requests

[![Coverage Status](https://coveralls.io/repos/github/phpolar/csrf-protection/badge.svg?branch=main)](https://coveralls.io/github/phpolar/csrf-protection?branch=main) [![Latest Stable Version](http://poser.pugx.org/phpolar/csrf-protection/v)](https://packagist.org/packages/phpolar/csrf-protection) [![Total Downloads](http://poser.pugx.org/phpolar/csrf-protection/downloads)](https://packagist.org/packages/phpolar/csrf-protection) [![Latest Unstable Version](http://poser.pugx.org/phpolar/csrf-protection/v/unstable)](https://packagist.org/packages/phpolar/csrf-protection) [![License](http://poser.pugx.org/phpolar/csrf-protection/license)](https://packagist.org/packages/phpolar/csrf-protection) [![PHP Version Require](http://poser.pugx.org/phpolar/csrf-protection/require/php)](https://packagist.org/packages/phpolar/csrf-protection)

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
$csrfChecking = $this->container->get(CsrfRequestCheckMiddleware::class);
$csrfFiltering = $this->container->get(CsrfResponseFilterMiddleware::class);

$app->use($csrfChecking);
$app->use($csrfFiltering);

// ...

$response = $csrfCheckMiddleware->process($request, $nextHandler);

// ...

$preparedResponse = $middleWare->process($request, $routingHandler);
```

## Resources

1. [PSR-7](https://www.php-fig.org/psr/psr-7/)
1. [PSR-15](https://www.php-fig.org/psr/psr-15/)
1. [Example middleware setup](https://www.php-fig.org/psr/psr-15/meta/#63-example-interface-interactions)

## [API Documentation](https://phpolar.github.io/csrf-protection/)

[Back to top](#csrf-protection)
