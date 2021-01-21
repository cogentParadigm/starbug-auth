<?php
namespace Starbug\Auth\Http;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Auth\SessionExchangeInterface;
use Starbug\Auth\SessionInterface;
use Starbug\Auth\SessionRepositoryInterface;

class CookieSessionExchange implements SessionExchangeInterface {
  /**
   * Server request to retrieve cookies sent from client.
   *
   * @var ServerRequestInterface
   */
  protected $request;
  /**
   * Cookie path.
   *
   * @var string
   */
  protected $path;
  /**
   * HMAC key for validating session token integrity.
   *
   * @var string
   */
  protected $key;
  public function __construct(ServerRequestInterface $request, $path, $key) {
    $this->request = $request;
    $this->path = $path;
    $this->key = $key;
  }
  /**
   * Saving the session means sending the cookie to client.
   *
   * @param SessionInterface $session The session.
   */
  public function save(SessionInterface $session) {
    // Encode session data.
    $values = [
      "e" => $session->getExpirationDate(),
      "v" => $session->getIdentity()->getId(),
      "t" => $session->getToken()
    ];
    $encoded = $this->encode($values);
    if (!headers_sent()) {
      setcookie("sid", $encoded, $values['e'], $this->path, null, false, true);
    }
  }
  /**
   * Loading the session means retrieving from cookie.
   *
   * @param SessionRepositoryInterface $sessions
   *  Repository to obtain a valid Session object.
   *
   * @return SessionInterface|null
   */
  public function load(SessionRepositoryInterface $sessions): ?SessionInterface {
    // Obtain and parse session cookie.
    $session = $this->request->getCookieParams()["sid"] ?? false;
    $values = $this->decode($session);
    if ($values) {
      return $sessions->load($values["t"]);
    }
    return null;
  }
  /**
   * Destroying the session means destroying the cookie.
   */
  public function destroy() {
    if (!headers_sent()) {
      setcookie("sid", null, time(), $this->path, null, false, true);
    }
  }

  /**
   * Helper function to encode a session token.
   *
   * @param array $values The values to encode.
   *
   * @return string The encoded token.
   */
  protected function encode($values) {
    $session = http_build_query($values);
    $session .= '&d='.urlencode(hash_hmac("sha256", $session, $this->key));
    return $session;
  }

  /**
   * Helper function to decode a session token.
   *
   * @param string $session The encoded token.
   *
   * @return array The decoded values.
   */
  protected function decode($session) {
    if (empty($session)) return false;
    parse_str($session, $values);
    $digest = $values['d'];
    unset($values['d']);
    // Validate cookie integrity.
    if (hash_hmac("sha256", http_build_query($values), $this->key) != $digest) return false;
    return $values;
  }
}