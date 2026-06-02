<?php
namespace Starbug\Auth;

interface SessionRepositoryInterface {
  /**
   * Create a session.
   *
   * @param int $id The user ID.
   * @param string $duration The duration until the session expires.
   *
   * @return void
   */
  public function save(SessionInterface $session);

  /**
   * Retrieve a session.
   *
   * @param string $token The session token.
   *
   * @return ?SessionInterface The session, or null if not found.
   */
  public function load($token): ?SessionInterface;

  /**
   * Delete a session.
   *
   * @param string $token The session token.
   */
  public function destroy(SessionInterface $session);
}
