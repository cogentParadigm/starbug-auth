<?php
namespace Starbug\Auth\Http;

use Starbug\Auth\SessionHandlerHook;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Auth\SessionInterface;

class CsrfHandler implements SessionHandlerHook, CsrfHandlerInterface {
  /**
   * Session handler.
   *
   * @var SessionHandlerInterface
   */
  protected $session;

  /**
   * CSRF repository
   *
   * @var CsrfExchangeInterface
   */
  protected $exchanger;

  /**
   * HMAC key.
   */
  protected $key;

  public function __construct(CsrfExchangeInterface $exchanger, $key) {
    $this->exchanger = $exchanger;
    $this->key = $key;
  }
  public function startSession(SessionHandlerInterface $handler) {
    $this->session = $handler;
  }
  public function createSession(SessionHandlerInterface $handler, SessionInterface $session, bool $persist, bool $activate) {
    $token = bin2hex(random_bytes(32));
    if ($persist) {
      $this->exchanger->save($token);
    }
  }
  public function destroy(SessionHandlerInterface $handler) {
    $this->exchanger->destroy();
  }

  public function getRequestToken() {
    $token = $this->exchanger->load();
    if (empty($token)) return $token;
    $id = $this->session->getUserId();
    return hash_hmac("sha256", $id.$token, $this->key);
  }

  public function checkRequestToken($token) {
    if (!$this->session->loggedIn()) return true;
    $expected = $this->getRequestToken();
    if (!empty($expected) && $token === $expected) {
      return true;
    }
    return false;
  }
}
