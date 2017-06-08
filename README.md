# CSRF header checking middleware

This package contains a PHP PSR-15 (http-interop) compliant middleware that checks for CSRF attacks.

It implements the [first OWASP general recommendation for guarding your site against cross-site request forgery (Verifying Same Origin with Standard Headers)](https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet).

Note that OWASP recommends also using a CSRF token. This requires some changes in your application and this middleware does not provide any help regarding CSRF token generation.
Other packages (like [Slim-CSRF](https://github.com/slimphp/Slim-Csrf)) can help you with CSRF token validation.
 
What is it doing?
-----------------

The `CsrfHeaderCheckMiddleware` will check that all POST/DELETE requests and verify that the "Origin" of the request is your own website.

It does so by comparing the "Origin" (or the "Referrer" header as a fallback) to the "Host" (or "X-Forwarded-Host") header.
If the headers do not match, it will trigger an exception.

Limits:
-------

- No GET (so if your application modifies state on GET, you are screwed.)
- Expect "Origin" or "Referrer" header to be filled. This will often be the case unless you are in a corporate environment with proxies that are fiddling with your request (sometimes done to make the request anonymous).
