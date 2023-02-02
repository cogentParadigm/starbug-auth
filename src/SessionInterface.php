<?php
namespace Starbug\Auth;

interface SessionInterface {
  public function getToken();
  public function setToken($token);
  public function getIdentity(): IdentityInterface;
  public function setIdentity(IdentityInterface $userId);
  public function getExpirationDate();
  public function setExpirationDate($expirationDate);
  public function getData($property = false);
  public function setData($data = []);
}
