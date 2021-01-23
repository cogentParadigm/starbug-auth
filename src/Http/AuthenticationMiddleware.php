<?php
namespace Starbug\Auth\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Starbug\Auth\SessionHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface {
  /**
   * Session handler.
   *
   * @var SessionHandlerInterface
   */
  protected $session;

  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $session = $this->session->startSession();
    return $handler->handle(
      $request->withAttribute("session", $session)
    );
  }
}
