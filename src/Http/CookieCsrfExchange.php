<?php
namespace Starbug\Auth\Http;

class CookieCsrfExchange implements CsrfExchangeInterface {
  /**
   * Cookie path.
   *
   * @var string
   */
  protected $path;
  /**
   * Session duration.
   *
   * @var int
   */
  protected $duration;

  public function __construct($path = "/", $duration = 2592000) {
    $this->path = $path;
    $this->duration = $duration;
  }
  /**
   * {@inheritdoc}
   */
  public function save($token) {
    if (!headers_sent()) {
      setcookie("oid", $token, time() + $this->duration, $this->path, null, true, false);
    }
  }
  /**
   * {@inheritdoc}
   *
   * @SuppressWarnings(PHPMD.Superglobals)
   */
  public function load() {
    return $_COOKIE["oid"] ?? "";
  }
  /**
   * Destroying the session means destroying the cookie.
   */
  public function destroy() {
    if (!headers_sent()) {
      setcookie("oid", null, time(), $this->path, null, true, false);
    }
  }
}
