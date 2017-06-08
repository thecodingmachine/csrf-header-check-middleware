[![Latest Stable Version](https://poser.pugx.org/thecodingmachine/csrf-header-check-middleware/v/stable)](https://packagist.org/packages/thecodingmachine/csrf-header-check-middleware)
[![Total Downloads](https://poser.pugx.org/thecodingmachine/csrf-header-check-middleware/downloads)](https://packagist.org/packages/thecodingmachine/csrf-header-check-middleware)
[![Latest Unstable Version](https://poser.pugx.org/thecodingmachine/csrf-header-check-middleware/v/unstable)](https://packagist.org/packages/thecodingmachine/csrf-header-check-middleware)
[![License](https://poser.pugx.org/thecodingmachine/csrf-header-check-middleware/license)](https://packagist.org/packages/thecodingmachine/csrf-header-check-middleware)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thecodingmachine/csrf-header-check-middleware/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thecodingmachine/csrf-header-check-middleware/?branch=master)
[![Build Status](https://travis-ci.org/thecodingmachine/csrf-header-check-middleware.svg?branch=master)](https://travis-ci.org/thecodingmachine/csrf-header-check-middleware)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/csrf-header-check-middleware/badge.svg?branch=master&service=github)](https://coveralls.io/github/thecodingmachine/csrf-header-check-middleware?branch=master)

# CSRF header checking middleware

This package contains a PHP PSR-15 (http-interop) compliant middleware that checks for CSRF attacks.

It implements the [first OWASP general recommendation for guarding your site against cross-site request forgery (Verifying Same Origin with Standard Headers)](https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet).

Note that OWASP recommends also using a CSRF token. This requires some changes in your application and this middleware does not provide any help regarding CSRF token generation.
Other packages (like [Slim-CSRF](https://github.com/slimphp/Slim-Csrf)) can help you with CSRF token validation.
 
What is it doing?
-----------------

The `CsrfHeaderCheckMiddleware` will check that all POST/PUT/DELETE requests and verify that the "Origin" of the request is your own website.

It does so by comparing the "Origin" (or the "Referrer" header as a fallback) to the "Host" (or "X-Forwarded-Host") header.
If the headers do not match (or if the headers are not found), it will trigger an exception.

Limits:
-------

- This middleware completely bypasses GET requests. If your application modifies state on GET requests, you are screwed. Of course, modification of state should only happen in POST requests (but please check twice that your routes changing state do ONLY works with POST/DELETE/PUT requests).
- This middleware expects "Origin" or "Referrer" headers to be filled. This will often be true unless you are in a corporate environment with proxies that are fiddling with your request. For instance, some proxies are known to strip headers in order to make the request anonymous.
