<?php
namespace Starbug\Auth;

interface SessionExchangeInterface {
  /**
   * Save the session to client.
   *
   * @param SessionInterface $session The new session.
   */
  public function save(SessionInterface $session);

  /**
   * Obtain any saved session from the client.
   *
   * @return SessionInterface The session.
   */
  public function load(SessionRepositoryInterface $sessions): ?SessionInterface;

  /**
   * Delete the saved session.
   */
  public function destroy();
}
