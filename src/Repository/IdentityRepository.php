<?php
namespace Starbug\Auth\Repository;

use Starbug\Auth\Identity;
use Starbug\Auth\IdentityInterface;
use Starbug\Auth\IdentityRepositoryInterface;
use Starbug\Core\DatabaseInterface;

class IdentityRepository implements IdentityRepositoryInterface {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function getIdentity($criteria): ?IdentityInterface {
    $query = $this->db->query("users")
      ->condition("users.deleted", "0")
      ->select("users.*")
      ->select("GROUP_CONCAT(users.groups.slug) as groups")
      ->group("users.id");
    if (is_array($criteria)) $query->conditions($criteria);
    else $query->condition("users.id", $criteria);
    $user = $query->one();
    if ($user) {
      if (!is_array($user['groups'])) $user['groups'] = is_null($user['groups']) ? [] : explode(",", $user['groups']);
      $identity = new Identity($user["id"], $user["password"], $user["groups"]);
      unset($user["password"]);
      $identity->setData($user);
      return $identity;
    }
    return null;
  }
}
