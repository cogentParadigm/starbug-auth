<?php
namespace Starbug\Auth\Repository;

use Starbug\Auth\IdentityRepositoryInterface;
use Starbug\Auth\Session;
use Starbug\Auth\SessionInterface;
use Starbug\Auth\SessionRepositoryInterface;
use Starbug\Db\DatabaseInterface;

class SessionRepository implements SessionRepositoryInterface {

  /**
   * Storage backend
   *
   * @var DatabaseInterface
   */
  protected $db;
  /**
   * User repository
   *
   * @var IdentityRepositoryInterface
   */
  protected $users;

  public function __construct(DatabaseInterface $db, IdentityRepositoryInterface $users) {
    $this->db = $db;
    $this->users = $users;
  }
  /**
   * {@inheritdoc}
   */
  public function save(SessionInterface $session) {
    $this->db->store("sessions", [
      "users_id" => $session->getIdentity()->getId(),
      "token" => $session->getToken(),
      "expires" => date("Y-m-d H:i:s", $session->getExpirationDate())
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function load($token): ?SessionInterface {
    $session = $this->db->query("sessions")
      ->condition("token", $token)
      ->condition("expires", date("Y-m-d H:i:s"), ">=")
      ->one();
    if ($session) {
      $user = $this->users->getIdentity($session["users_id"]);
      return new Session(
        $user,
        $session["token"],
        $session["expires"],
        $session
      );
    }
    return null;
  }

  /**
   * {@inheritdoc}
   */
  public function destroy(SessionInterface $session) {
    $this->db->query("sessions")->condition("token", $session->getToken())->delete();
  }
}
