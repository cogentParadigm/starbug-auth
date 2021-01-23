<?php
namespace Starbug\Auth;

interface SessionHandlerHook {
  public function startSession(SessionHandlerInterface $handler);
  public function createSession(SessionHandlerInterface $handler, SessionInterface $session, bool $persist, bool $activate);
  public function destroy(SessionHandlerInterface $handler);
}
