<?php
namespace Starbug\Auth;

interface SessionStorageInterface {
  /**
   * Create a session for the given user.
   *
   * @param array $user The user to create the session for. This should have come from the IdentityInterface.
   * @param bool $persist Whether or not to persist the session. Defaults to true.
   *
   * @return void
   */
  public function createSession(IdentityInterface $id, bool $persist): SessionInterface;
  /**
   * Obtain the users active session claim.
   * Simply retrieves the token provided by the request.
   *
   * @return array The session data.
   */
  public function load(): ?SessionInterface;
  /**
   * Destroy the active session.
   *
   * @return void
   */
  public function destroy(?SessionInterface $session);
}
