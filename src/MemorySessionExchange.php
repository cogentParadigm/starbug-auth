<?php
namespace Starbug\Auth;

use Starbug\Bundle\Bundle;

class MemorySessionExchange implements SessionExchangeInterface {
  public function __construct(
    protected Bundle $jar
  ) {
  }

  public function save(SessionInterface $session): void {
    $this->jar->set("sid", $session->getToken());
  }

  public function load(SessionRepositoryInterface $sessions): ?SessionInterface {
    $token = $this->jar->get("sid");
    return $token ? $sessions->load($token) : null;
  }

  public function destroy(): void {
    unset($this->jar["sid"]);
  }
}
