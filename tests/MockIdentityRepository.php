<?php
namespace Starbug\Auth\Tests;

use Starbug\Auth\Identity;
use Starbug\Auth\IdentityInterface;
use Starbug\Auth\IdentityRepositoryInterface;

class MockIdentityRepository implements IdentityRepositoryInterface {
  protected $users;
  public function __construct() {
    $this->users = [];
  }
  public function addUser($key, $user) {
    if (!isset($user["id"])) $user["id"] = "";
    if (!isset($user["password"])) $user["password"] = "";
    if (!isset($user["groups"])) $user["groups"] = [];
    $this->users[$key] = $user;
  }
  public function getIdentity($key): ?IdentityInterface {
    $user = $this->users[$key];
    if (!is_array($user['groups'])) $user['groups'] = is_null($user['groups']) ? [] : explode(",", $user['groups']);
    if ($user) {
      $identity = new Identity($user["id"], $user["password"], $user["groups"]);
      unset($user["password"]);
      $identity->setData($user);
      return $identity;
    }
  }
}
