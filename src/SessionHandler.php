<?php
namespace Starbug\Auth;

class SessionHandler implements SessionHandlerInterface {
  /**
   * The session.
   *
   * @var SessionInterface
   */
  protected $session;
  /**
   * The repository of users.
   *
   * @var IdentityRepositoryInterface
   */
  protected $users;
  /**
   * The repository of sessions.
   *
   * @var SessionStorageInterface
   */
  protected $sessions;

  protected $hooks = [];

  public function __construct(SessionStorageInterface $sessions, IdentityRepositoryInterface $users) {
    $this->sessions = $sessions;
    $this->users = $users;
  }
  /**
   * {@inheritdoc}
   */
  public function startSession(): ?SessionInterface {
    $this->session = $this->sessions->load();
    $this->invokeHooks("startSession", [$this]);
    return $this->session;
  }
  /**
   * {@inheritdoc}
   */
  public function createSession(IdentityInterface $id, bool $persist = true, bool $activate = true): SessionInterface {
    $session = $this->sessions->createSession($id, $persist);
    if ($activate) {
      $this->setSession($session);
    }
    $this->invokeHooks("createSession", [$this, $session, $persist, $activate]);
    return $session;
  }
  /**
   * {@inheritdoc}
   */
  public function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
  }
  /**
   * {@inheritdoc}
   */
  public function authenticate($user, $password): ?IdentityInterface {
    $id = $this->users->getIdentity($user);
    if (isset($id) && password_verify($password, $id->getHashedPassword())) {
      return $id;
    }
    return null;
  }
  /**
   * {@inheritdoc}
   */
  public function destroy() {
    $this->sessions->destroy($this->session);
    $this->session = null;
    $this->invokeHooks("destroy", [$this]);
  }
  /**
   * {@inheritdoc}
   */
  public function loggedIn($group = "") {
    if (!isset($this->session)) return false;
    if (empty($group)) return true;
    $groups = $this->session->getIdentity()->getGroups();
    if (!is_array($group)) $group = [$group];
    return !empty(array_intersect($group, $groups));

  }

  /**
   * {@inheritdoc}
   */
  public function getSession(): ?SessionInterface {
    return $this->session;
  }

  /**
   * {@inheritdoc}
   */
  public function setSession(SessionInterface $session) {
    $this->session = $session;
  }

  /**
   * {@inheritdoc}
   */
  public function getUserId() {
    if (isset($this->session)) {
      return $this->session
        ->getIdentity()
        ->getId();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getData($property = false) {
    if (isset($this->session)) {
      return $this->session
        ->getIdentity()
        ->getData($property);
    }
  }

  public function addHook(SessionHandlerHook $hook) {
    $this->hooks[] = $hook;
  }

  protected function invokeHooks($method, $args) {
    foreach ($this->hooks as $hook) {
      call_user_func_array([$hook, $method], $args);
    }
  }
}
