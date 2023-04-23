<?php

namespace spec\Starbug\Auth;

use PhpSpec\ObjectBehavior;
use Starbug\Auth\Identity;
use Starbug\Auth\IdentityRepositoryInterface;
use Starbug\Auth\Session;
use Starbug\Auth\SessionHandler;
use Starbug\Auth\SessionStorageInterface;

/**
 * Spec test for Starbug\Auth\SessionHandler.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class SessionHandlerSpec extends ObjectBehavior {
  public function let(SessionStorageInterface $sessions, IdentityRepositoryInterface $users) {
    $this->beConstructedWith($sessions, $users);
  }
  public function it_is_initializable() {
    $this->shouldHaveType(SessionHandler::class);
  }
  public function it_starts_an_empty_session($sessions) {
    // Given.
    $sessions->load()->willReturn(null);

    // Expect.
    $sessions->load()->shouldBeCalled();

    // When.
    $this->startSession();

    // Then.
    $this->loggedIn()->shouldBe(false);
  }
  public function it_starts_an_active_session($sessions) {
    // Given.
    $identity = new Identity(1, "", []);
    $session = new Session($identity, "testtoken", time() + 99999);
    $sessions->load()->willReturn($session);

    // Expect.
    $sessions->load()->shouldBeCalled();

    // When.
    $this->startSession();

    // Then.
    $this->loggedIn()->shouldBe(true);
    $this->getSession()->shouldBe($session);
    $this->getUserId()->shouldBe(1);
  }
  public function it_creates_a_session($sessions) {
    // Given.
    $identity = new Identity(1, "", []);
    $session = new Session($identity, "testtoken", time() + 99999);
    $sessions->createSession($identity, true)->willReturn($session);

    // When + Then.
    $this->createSession($identity)->shouldBe($session);

    // Then.
    $this->loggedIn()->shouldBe(true);
    $this->getSession()->shouldBe($session);
    $this->getUserId()->shouldBe(1);
  }
  public function it_creates_a_session_without_activating_it($sessions) {
    // Given.
    $identity = new Identity(1, "", []);
    $session = new Session($identity, "testtoken", time() + 99999);
    $sessions->createSession($identity, true)->willReturn($session);

    // When + Then.
    $this->createSession($identity, true, false)->shouldBe($session);

    // Then.
    $this->loggedIn()->shouldBe(false);
    $this->getSession()->shouldBe(null);
    $this->getUserId()->shouldBe(null);
  }
  public function it_hashes_and_validates_passwords($users) {
    // Given.
    $password = "mysupersecretpassword";
    $notmypassword = "notmysupersecretpassword";
    $hashed = $this->hashPassword($password)->getWrappedObject();
    $identity = new Identity(1, $hashed, []);
    $users->getIdentity(1)->willReturn($identity);

    // Then.
    $this->authenticate(1, $password)
      ->shouldBe($identity);
    $this->authenticate(1, $notmypassword)
      ->shouldBe(null);
  }
  public function it_destroys_the_session($sessions) {
    // Given.
    $identity = new Identity(1, "", []);
    $session = new Session($identity, "testtoken", time() + 99999);
    $this->setSession($session);

    // Then.
    $this->loggedIn()->shouldBe(true);
    $this->getSession()->shouldBe($session);
    $this->getUserId()->shouldBe(1);

    // Expect.
    $sessions->destroy($session)->shouldBeCalled();

    // When.
    $this->destroy();

    // Then.
    $this->loggedIn()->shouldBe(false);
    $this->getSession()->shouldBe(null);
    $this->getUserId()->shouldBe(null);
  }
  public function it_tells_us_if_a_user_is_logged_in() {
    // Assert.
    $this->loggedIn()->shouldBe(false);

    // Given.
    $identity = new Identity(1, "", []);
    $session = new Session($identity, "testtoken", time() + 99999);
    $this->setSession($session);

    // Then.
    $this->loggedIn()->shouldBe(true);
  }
  public function it_gets_custom_values_from_the_identity() {
    // Given.
    $identity = new Identity(1, "", []);
    $identity->setData(["first_name" => "Ali"]);
    $session = new Session($identity, "testtoken", time() + 99999);
    $this->setSession($session);

    // Then.
    $this->getData("first_name")->shouldBe("Ali");
    $this->getData()->shouldBe(["first_name" => "Ali"]);
  }
}
