<?php
namespace Starbug\Auth\Http;

interface CsrfExchangeInterface {
  /**
   * Save the token.
   *
   * @param string $token The CSRF token.
   */
  public function save($token);

  /**
   * Obtain any saved token from the client.
   *
   * @return string The CSRF token.
   */
  public function load();

  /**
   * Delete the saved token.
   */
  public function destroy();
}
