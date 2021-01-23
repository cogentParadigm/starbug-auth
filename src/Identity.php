<?php

namespace Starbug\Auth;

/**
 * Cannonical implementation of IdentityInterface.
 */
class Identity implements IdentityInterface {
  protected $id;
  protected $hashedPassword;
  protected $groups = [];
  protected $data = [];

  public function __construct($id, $hashedPassword, array $groups = [], array $data = []) {
    $this->id = $id;
    $this->hashedPassword = $hashedPassword;
    $this->groups = $groups;
    $this->data = $data;
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
  }

  public function getHashedPassword() {
    return $this->hashedPassword;
  }

  public function setHashedPassword($hashedPassword) {
    $this->hashedPassword = $hashedPassword;
  }

  public function getGroups() {
    return $this->groups;
  }

  public function setGroups(array $groups) {
    $this->groups = $groups;
  }

  public function getData($property = false) {
    if (false === $property) {
      return $this->data;
    }
    return $this->data[$property] ?? null;
  }

  public function setData(array $data) {
    $this->data = $data;
  }

  public function addData(array $data) {
    $this->data = $data + $this->data;
  }
}
