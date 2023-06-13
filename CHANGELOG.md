## 3.0.3 (2023-06-13)

### Fix

- prune dist

## 3.0.2 (2023-05-22)

### Fix

- **SessionTokenStorage**: allow adding tokens after max count threshold is reached

## 3.0.1 (2023-05-22)

### Fix

- **AbstractTokenStorage**: do not add tokens more than once

## 3.0.0 (2023-05-21)

### BREAKING CHANGE

- Use the `SessionWrapper` instead of passing the global session array. The list of tokens will be re-indexed after expired tokens are cleared.
- Inject token in request handler instead of request middleware.
- Inject the token into the `CsrfResponseFilterMiddleware`'s constructor.  The `CsrfTokenGenerator` has been removed.
- Use a response filter to create `CsrfResponseFilterMiddleware` instead of a response filter strategy.

### Feat

- **CsrfResponseFilterMiddleware**: use response filter instead of response filter strategy
- use response filter library

### Fix

- fix various session handling issues
- add token in handler instead of middleware
- inject the token into the middleware

## 2.1.0 (2023-04-29)

### Feat

- upgrade psr/http-message to 2.0

## 2.0.1 (2023-03-01)

### Fix

- remove unused dependency

## 2.0.0 (2023-02-25)

### BREAKING CHANGE

- The `CsrfResponseFilterMiddleware` and `CsrfProtectionRequestHandler` will no longer accept a `LoggerInterface` instance as an argument.  Logging invalid requests can be handled in another request handler or middleware.
- Closes #45, Closes #46, Closes #47, Closes #48, Closes #49
- Issue #38
- Closes #30
- Closes #23, Closes #24

### Fix

- remove logging
- **CsrfToken**: allow negative ttl
- reduce max number of tokens

### Refactor

- improve path coverage
- **CsrfTokenGenerator**: add type declaration to ttl parameter
- **ResponseFilterScanStrategy**: remove this class
- simplify the csrf response filter middleware
- **SessionTokenStorage**: improve variable name
- **CsrfRequestCheckMiddleware**: delegate to next handler when request is valid
- **CsrfResponseFilter**: implement response filter interface
- **CsrfResponseFilter**: rename class
- **CsrfToken**: calculate expires on when created
- rename classes

## 1.3.3 (2023-02-22)

### Fix

- **SessionTokenStorage**: load tokens from session variable

## 1.3.2 (2023-02-11)

### Fix

- remove unnecessary file/folders from dist

## 1.3.1 (2023-02-09)

### Refactor

- add docs and refactor

## 1.3.0 (2023-02-01)

### Feat

- **CsrfPostRoutingMiddlewareFactory**: allow extending

## 1.2.0 (2023-02-01)

### Feat

- allow extending middleware

## 1.1.0 (2023-02-01)

### Feat

- add factory for post routing middleware

## 1.0.1 (2023-01-31)

### Fix

- change csrf token key

## 1.0.0 (2023-01-30)

### Feat

- split middleware to post- pre- routing

## 0.4.4 (2023-01-25)

### Fix

- **.gitattributes**: reduce dist size

## 0.4.3 (2023-01-17)

### Fix

- remove unnecessary files from dist

## 0.4.2 (2023-01-17)

### Fix

- add badges to readme

## 0.4.1 (2023-01-17)

### Fix

- use require instead of install

## 0.4.0 (2023-01-17)

### Feat

- add api documentation

## 0.3.0 (2023-01-17)

### Feat

- **CsrfProtectionMiddleware.php**: add middleware

### Fix

- do not start session
- uri encode the token query param

## 0.2.0 (2023-01-17)

### Feat

- add logging support

## 0.1.1 (2023-01-17)

### Fix

- improve session token storage
