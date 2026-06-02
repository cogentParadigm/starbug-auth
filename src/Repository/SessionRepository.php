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
  /**
   * Additional data columns to load/save
   *
   * @var array
   */
  protected $dataColumns = [];

  public function __construct(DatabaseInterface $db, IdentityRepositoryInterface $users, $dataColumns = []) {
    $this->db = $db;
    $this->users = $users;
    $this->dataColumns = $dataColumns;
  }
  /**
   * {@inheritdoc}
   */
  public function save(SessionInterface $session) {
    $record = [
      "users_id" => $session->getIdentity()->getId(),
      "token" => $session->getToken(),
      "expires" => date("Y-m-d H:i:s", $session->getExpirationDate())
    ];
    $data = $session->getData();
    foreach ($this->dataColumns as $column) {
      if (array_key_exists($column, $data) && !array_key_exists($column, $record)) {
        $record[$column] = $data[$column];
      }
    }
    $this->db->store("sessions", $record);
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
      if ($user = $this->users->getIdentity($session["users_id"])) {
        return new Session(
          $user,
          $session["token"],
          strtotime($session["expires"]),
          $session
        );
      }
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
