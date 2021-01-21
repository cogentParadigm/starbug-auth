<?php
namespace Starbug\Auth\Tests;

use PHPUnit\Framework\TestCase;
use Starbug\Auth\Identity;
use Starbug\Auth\SessionHandler;

class SessionHandlerTest extends TestCase {
  public function setUp() {
    $this->storage = new MockSessionStorage();
    $this->user = new MockIdentityRepository();
    $this->session = new SessionHandler($this->storage, $this->user);
  }

  public function testLoggedOut() {
    $this->session->startSession();
    $this->assertFalse($this->session->loggedIn());
  }

  public function testLoggedIn() {
    $id = rand(1, 100);
    $identity = new Identity($id, "", []);
    $this->session->createSession($identity);
    $this->assertTrue($this->session->loggedIn());
    $this->assertEquals($this->session->getUserId(), $id);
  }

  public function testLoggedInManualActivation() {
    $id = rand(1, 100);
    $identity = new Identity($id, "", []);
    $session = $this->session->createSession($identity, true, false);

    $this->assertFalse($this->session->loggedIn());

    $this->session->setSession($session);

    $this->assertTrue($this->session->loggedIn());
    $this->assertEquals($this->session->getUserId(), $id);
  }

  public function testPasswordValidation() {
    $passwords = [
      "5ba8036df3b35",
      "5ba8036df3b74",
      "5ba8036df3ba3",
      "5ba8036df3bb6",
      "5ba8036df3bc6"
    ];
    $badPasswords = [
      "5ba803cc36bfc",
      "5ba803cc36c20",
      "5ba803cc36c32",
      "5ba803cc36c45",
      "5ba803cc36c56"
    ];
    foreach ($passwords as $idx => $password) {
      $hash = $this->session->hashPassword($password);
      $this->user->addUser($idx, ["password" => $hash]);
      $this->assertNotEmpty($this->session->authenticate($idx, $password));
      $this->assertEmpty($this->session->authenticate($idx, $badPasswords[$idx]));
    }
  }

  public function testLoginLogout() {
    // Create a session.
    $id = rand(1, 100);
    $identity = new Identity($id, "", []);
    $this->session->createSession($identity);

    // Verify the user is logged in.
    $this->assertTrue($this->session->loggedIn());
    $this->assertEquals($this->session->getUserId(), $id);

    // Logout.
    $this->session->destroy();
    $this->assertFalse($this->session->loggedIn());
    $this->assertEmpty($this->session->getUserId());
  }
}
