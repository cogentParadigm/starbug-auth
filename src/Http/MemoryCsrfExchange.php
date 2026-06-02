<?php
namespace Starbug\Auth\Http;

use Starbug\Bundle\Bundle;

class MemoryCsrfExchange implements CsrfExchangeInterface {
  public function __construct(
    protected Bundle $jar
  ) {
  }

  public function save($token): void {
    $this->jar->set("oid", $token);
  }

  public function load() {
    return $this->jar->get("oid") ?? "";
  }

  public function destroy(): void {
    unset($this->jar["oid"]);
  }
}
