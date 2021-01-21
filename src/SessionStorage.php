<?php
namespace Starbug\Auth;

/**
 * Cookie based implementation of SessionStorageInterface
 */
class SessionStorage implements SessionStorageInterface {
  /**
   * Session persistence.
   *
   * @var SessionRepositoryInterface
   */
  protected $repository;

  /**
   * Client exchange handler.
   *
   * @var SessionExchangeInterface
   */
  protected $exchanger;

  /**
   * The lifetime of new sessions.
   */
  protected $duration;

  public function __construct(SessionRepositoryInterface $repository, SessionExchangeInterface $exchanger, $duration = 2592000) {
    $this->repository = $repository;
    $this->exchanger = $exchanger;
    $this->duration = $duration;
  }
  /**
   * {@inheritdoc}
   */
  public function createSession(IdentityInterface $id, $persist = true): SessionInterface {
    $expires = time() + $this->duration;
    $token = bin2hex(random_bytes(32));
    $session = new Session($id, $token, $expires);
    if ($persist) {
      $this->repository->save($session);
      $this->exchanger->save($session);
    }
    return $session;
  }
  /**
   * {@inheritdoc}
   */
  public function load(): ?SessionInterface {
    return $this->exchanger->load($this->repository);
  }
  /**
   * {@inheritdoc}
   */
  public function destroy(?SessionInterface $session) {
    if (isset($session)) {
      $this->repository->destroy($session);
    }
    $this->exchanger->destroy();
  }
}
