<?php
namespace Starbug\Auth;

class Session implements SessionInterface {
  protected $id;
  protected $identity;
  protected $token;
  protected $expirationDate;

  public function __construct(IdentityInterface $identity, $token, $expirationDate) {
    $this->identity = $identity;
    $this->token = $token;
    $this->expirationDate = $expirationDate;
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
}
