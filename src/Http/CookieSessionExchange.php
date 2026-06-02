<?php
namespace Starbug\Auth\Http;

use Starbug\Auth\SessionExchangeInterface;
use Starbug\Auth\SessionInterface;
use Starbug\Auth\SessionRepositoryInterface;

class CookieSessionExchange implements SessionExchangeInterface {
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

  public function __construct($key, $path = "/") {
    $this->key = $key;
    $this->path = $path;
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
      // Use options array to include SameSite attribute.
      setcookie("sid", $encoded, [
        'expires' => $values['e'],
        'path' => $this->path,
        'domain' => null,
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
      ]);
    }
  }
  /**
   * Loading the session means retrieving from cookie.
   *
   * @param SessionRepositoryInterface $sessions
   *  Repository to obtain a valid Session object.
   *
   * @return SessionInterface|null
   *
   * @SuppressWarnings(PHPMD.Superglobals)
   */
  public function load(SessionRepositoryInterface $sessions): ?SessionInterface {
    // Obtain and parse session cookie.
    $session = $_COOKIE["sid"] ?? false;
    $values = $this->decode($session);
    if ($values && isset($values["t"])) {
      return $sessions->load($values["t"]);
    }
    return null;
  }
  /**
   * Destroying the session means destroying the cookie.
   */
  public function destroy() {
    if (!headers_sent()) {
      // Expire the cookie in the past to ensure removal and keep same flags
      setcookie("sid", "", [
        'expires' => time() - 3600,
        'path' => $this->path,
        'domain' => null,
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
      ]);
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
    // Require a digest
    if (!isset($values['d'])) return false;
    $digest = $values['d'];
    unset($values['d']);
    // Validate cookie integrity using timing-safe comparison
    $payload = http_build_query($values);
    $expected = hash_hmac("sha256", $payload, $this->key);
    if (!hash_equals($expected, $digest)) return false;
    // Basic expiry validation: must include numeric expiration and not be expired
    if (!isset($values['e']) || !ctype_digit((string) $values['e'])) return false;
    if ((int) $values['e'] < time()) return false;
    return $values;
  }
}
