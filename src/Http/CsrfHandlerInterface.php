<?php
namespace Starbug\Auth\Http;

interface CsrfHandlerInterface {
  public function getRequestToken();
  public function checkRequestToken($token);
}
