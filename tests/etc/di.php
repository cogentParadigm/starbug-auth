<?php

use function DI\autowire;
use function DI\get;
use Starbug\Bundle\Bundle;
use Starbug\Auth\MemorySessionExchange;
use Starbug\Auth\Http\MemoryCsrfExchange;

return [
  'http.cookie_jar' => autowire(Bundle::class),
  'Starbug\Auth\SessionExchangeInterface' => autowire(MemorySessionExchange::class)
    ->constructorParameter('jar', get('http.cookie_jar')),
  'Starbug\Auth\Http\CsrfExchangeInterface' => autowire(MemoryCsrfExchange::class)
    ->constructorParameter('jar', get('http.cookie_jar'))
];
