<?php
namespace Starbug\Auth\Http;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddleware implements MiddlewareInterface {

  /**
   * CSRF Handler
   *
   * @var CsrfHandlerInterface
   */
  protected $csrf;

  protected $formKey;

  public function __construct(CsrfHandlerInterface $csrf, $formKey = "oid") {
    $this->csrf = $csrf;
    $this->formKey = $formKey;
  }
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    if (in_array($request->getMethod(), ["PUT", "POST", "DELETE"])) {
      $body = $request->getParsedBody();
      if (!$this->csrf->checkRequestToken($body[$this->formKey] ?? "")) {
        throw new Exception("Could not authenticate request.");
      }
      unset($body[$this->formKey]);
      $request = $request->withParsedBody($body);
    }
    return $handler->handle($request);
  }
}
