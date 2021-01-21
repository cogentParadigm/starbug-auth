<?php
namespace Starbug\Auth\Tests;

use Starbug\Auth\IdentityInterface;
use Starbug\Auth\SessionInterface;
use Starbug\Auth\SessionStorage;

/**
 * Mock implementation of SessionStorageInterface
 */
class MockSessionStorage extends SessionStorage {

  public function __construct($session = false) {
    $this->session = $session;
  }

  public function createSession(IdentityInterface $id, $persist = true): SessionInterface {
    return parent::createSession($id, false);
  }

  public function load(): ?SessionInterface {
    return null;
  }

  public function destroy(?SessionInterface $session) {
    // do nothing.
  }
}
