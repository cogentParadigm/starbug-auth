<?php
namespace Starbug\Auth;

use function DI\add;
use function DI\autowire;
use function DI\get;
use Starbug\Auth\Helper\CsrfHelper;
use Starbug\Auth\Helper\SessionHelper;

return [
  "session_handler.hooks" => [
    get("Starbug\Auth\Http\CsrfHandlerInterface")
  ],
  "Starbug\Auth\SessionHandlerInterface" => autowire("Starbug\Auth\SessionHandler")
    ->method("addHooks", get("session_handler.hooks")),
  "Starbug\Auth\*RepositoryInterface" => autowire("Starbug\Auth\Repository\*Repository"),
  "Starbug\Auth\SessionExchangeInterface" => autowire("Starbug\Auth\Http\CookieSessionExchange")
    ->constructorParameter("path", get("website_url"))
    ->constructorParameter("key", get("hmac_key")),
  "Starbug\Auth\Http\CsrfExchangeInterface" => autowire("Starbug\Auth\Http\CookieCsrfExchange")
    ->constructorParameter("path", get("website_url")),
  "Starbug\Auth\*Interface" => autowire("Starbug\Auth\*"),
  "Starbug\Auth\Http\CsrfHandlerInterface" => autowire("Starbug\Auth\Http\CsrfHandler")
    ->constructorParameter("key", get("hmac_key")),
  "template.helpers" => add([
    "csrf" => CsrfHelper::class,
    "session" => SessionHelper::class
  ])
];
