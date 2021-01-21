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
   * @return array The session.
   */
  public function load($token): ?SessionInterface;

  /**
   * Delete a session.
   *
   * @param string $token The session token.
   */
  public function destroy(SessionInterface $session);
}
