<?php
namespace Starbug\Auth;

use function DI\autowire;
use function DI\get;

return [
  "session_handler.hooks" => [
    get("Starbug\Auth\Http\CsrfHandlerInterface")
  ],
  "Starbug\Auth\SessionHandlerInterface" => autowire("Starbug\Auth\SessionHandler")
    ->method("addHooks", get("session_handler.hooks"))
];
