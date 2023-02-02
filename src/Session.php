<?php
namespace Starbug\Auth;

class Session implements SessionInterface {
  protected $id;
  protected $identity;
  protected $token;
  protected $expirationDate;
  protected $data;

  public function __construct(IdentityInterface $identity, $token, $expirationDate, $data = []) {
    $this->identity = $identity;
    $this->token = $token;
    $this->expirationDate = $expirationDate;
    $this->data = $data;
  }

  public function getIdentity(): IdentityInterface {
    return $this->identity;
  }

  public function setIdentity(IdentityInterface $identity) {
    $this->identity = $identity;
  }

  public function getToken() {
    return $this->token;
  }

  public function setToken($token) {
    $this->token = $token;
  }

  public function getExpirationDate() {
    return $this->expirationDate;
  }

  public function setExpirationDate($expirationDate) {
    $this->expirationDate = $expirationDate;
  }

  public function getData($property = false) {
    if (false === $property) {
      return $this->data;
    }
    return $this->data[$property] ?? null;
  }

  public function setData($data = []) {
    $this->data = $data;
  }
}
