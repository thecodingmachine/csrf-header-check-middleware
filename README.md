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

The `CsrfHeaderCheckMiddleware` will look at all POST/PUT/DELETE requests (actually all requests that are not GET/HEAD/OPTIONS).
It will verify that the "Origin" of the request is your own website.

It does so by comparing the "Origin" (or the "Referrer" header as a fallback) to the "Host" (or "X-Forwarded-Host") header.
If the headers do not match (or if the headers are not found), it will trigger an exception.

Why does it work?
-----------------

In a CSRF attack, the victim (Alice) is logged in your application.
The attacker (Eve) sends Alice a malicious link to her malicious website. The malicious website contains some Javascript that performs a POST on a form of your website. Since Alice is logged into your website, the POST succeeds, allowing Eve to perform actions on the behalf of Alice.

The query is therefore executed by Alice's computer. We can expect Alice's browser to behave as a "normal" browsers.

- Normal browsers always send the "Host" header (at least in HTTP 1.1).
- Normal browsers [do not allow Javascript code to modify the "Origin" or "Referer" header](https://developer.mozilla.org/en-US/docs/Glossary/Forbidden_header_name).
- Normal browsers do not allow Javascript code to send the "X-Forwarded-Host" header (TODO: check this!)

How does it compare to other solutions
--------------------------------------

When fighting CSRF attacks, the most common solution used it to generate a token in each form, store this token in session, and check that the user sends back the token.
If you are looking for a CSRF token based middleware using PSR-7/PSR-15, have a look at [Ocramius/PSR7Csrf](https://github.com/Ocramius/PSR7Csrf/)

### Advantages over token based implementations

Checking for HTTP headers can be done in the middleware alone.
With token-based middlewares, you have to modify your application to generate a token and send the token with any form. In contrast, checking headers requires no work besides adding the middleware. So it's really fast to deploy.

### Limits

- Works only with HTTP 1.1 requests (in HTTP 1.0, the "Host" header is not set)
- This middleware completely bypasses GET requests. If your application modifies state on GET requests, you are screwed. Of course, modification of state should only happen in POST requests (but please check twice that your routes changing state do ONLY works with POST/DELETE/PUT requests).
- This middleware expects "Origin" or "Referrer" headers to be filled. This will often be true unless you are in a corporate environment with proxies that are fiddling with your request. For instance, some proxies are known to strip headers in order to make the request anonymous.
- Will block CORS requests. You cannot use this middleware if you are expecting requests to come from another origin than your website. 

If you are in one of those situations, use a token-based middleware instead.

Installation
------------

```php
composer require thecodingmachine/csrf-header-check-middleware
```

Usage
-----

The simplest usage is based on defaults. It assumes that you have
a configured PSR-7 compatible application that supports piping
middlewares.

In a [`zendframework/zend-expressive`](https://github.com/zendframework/zend-expressive)
application, the setup would look like the following:

```php
$app = \Zend\Expressive\AppFactory::create();

$app->pipe(\TheCodingMachine\Middlewares\CsrfHeaderCheckMiddlewareFactory::createDefault();
```

Disabling CSRF checks
---------------------

You can disable CSRF checks on a per-route basis:

```php
// The first argument of the factory is a list of regular expressions that will be matched on the path.
// Here, we disable CSRF checks on /api/*
$app->pipe(\TheCodingMachine\Middlewares\CsrfHeaderCheckMiddlewareFactory::createDefault([
    '#^/api/#'
]);
```

This can be useful for APIs that are only used when communicating from server to server. Please note that if you decide to disable CSRF for some routes, you need to have some other forms of protection for this route.

Alternatively, any request passed to the middleware that has the 'TheCodingMachine\BypassCsrf' attribute set will be ignored:

```php
// Put this in a middleware placed before the `CsrfHeaderCheckMiddleware` to disable it.
$request = $request->withAttribute('TheCodingMachine\\BypassCsrf', true);
```
