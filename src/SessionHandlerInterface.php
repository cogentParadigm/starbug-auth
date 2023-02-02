<?php
namespace Starbug\Auth;

interface SessionHandlerInterface {
  /**
   * Start the session. Called early to see if there's an active session and load it.
   *
   * @return void
   */
  public function startSession(): ?SessionInterface;
  /**
   * Create a session for the given user.
   *
   * @param array $user The user to create the session for. This should have come from the IdentityInterface.
   * @param bool $persist True to persist/exchange the session. Defaults to true.
   * @param bool $activate True to make the session active. Defaults to true.
   *
   * @return void
   */
  public function createSession(IdentityInterface $user, bool $persist = true, bool $activate = true): SessionInterface;
  /**
   * Hash a password.
   *
   * @param string $password The plain text password to hash.
   *
   * @return string The hashed password.
   */
  public function hashPassword($password);
  /**
   * Validate a password against the saved hash.
   *
   * @param array $user The user record, obtained from IdentityInterface.
   * @param string $password The users password entry.
   *
   * @return boolean Returns false if validation fails. If the password validates, true is returned.
   */
  public function authenticate($user, $password);
  /**
   * Destroy the session.
   *
   * @return void
   */
  public function destroy();
  /**
   * Obtain the session.
   *
   * @return SessionInterface|null The session.
   */
  public function getSession(): ?SessionInterface;
  /**
   * Set the session.
   *
   * @param SessionInterface $session The session.
   */
  public function setSession(?SessionInterface $session);
  /**
   * Check if a user is logged in. Session must be started first.
   *
   * @param string|array $group A group or array of groups.
   *
   * @return void
   */
  public function loggedIn($group = "");
  /**
   * Get the user ID.
   *
   * @return int The user ID.
   */
  public function getUserId();

  /**
   * Retrieve data from the IdentityInterface.
   *
   * @param boolean $property Optional data property.
   *
   * @return mixed The property value or array of property values.
   */
  public function getData($property = false);
}
