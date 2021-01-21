# Starbug Authentication

An authentication library for the Starbug framework.

# Usage

First you need to construct an instance of `Starbug\Auth\SessionHandler` which requires constructing several prerequesite objects. Initially, using this outside of Starbug is not a priority. Eventually, I would like to supply some factories for easier construction, as well as some repository implementations which only require PDO instances.

```php
// The provided repository implementations require the Starbug database interface.
$db = $container->get("Starbug\Core\DatabaseInterface");
// This can be any PSR-7 implementation.
$request = $container->get("Psr\Http\Message\ServerRequestInterface");

// Now the good stuff.
$idRepository = new Starbug\Auth\Repository\IdentityRepository($db);
$cookieSessionExchange = new Starbug\Auth\Http\CookieSessionExchange($request, "/", "secretkey");
$sessionRepository = new Starbug\Auth\Repository\SessionRepository($db, $idRepository);
$sessionStorage = new Starbug\Auth\SessionStorage($sessionRepository, $cookieSessionExchange);
// et voila!
$sessionHandler = new Starbug\Auth\SessionHandler($sessionStorage, $idRepository);

// Start the session. Do this to load any existing session.
$sessionHandler->startSession();

// Hash a password for user registration.
$hashedPassword = $sessionHandler->hashPassword("mypassword");

// Authenticate a user and create a session to login.
if ($id = $sessionHandler->authenticate($userIdOrCriteria, "mypassword")) {
  $session = $sessionHandler->createSession($id);
}

// Check if user is logged in
if ($sessionHandler->loggedIn()) {
  echo "Hello, your user ID is ".$sessionHandler->getUserId();
}

// Custom attributes
if ($sessionHandler->loggedIn()) {
  // Store custom data attributes
  $id = $sessionHandler->getSession()->getIdentity();
  $id->setData(["first_name" => "Ali"]);
  $id->addData(["last_name" => "Gangji"]);

  // Retrieve all data as an array
  $data = $id->getData();
  // Retrieve specific properties
  $firstName = $id->getData("first_name");

  //Retrieval can be done from session handler.
  $data = $sessionHandler->getData();
  $firstName = $sessionHandler->getData("first_name");
}

// Transient sessions
// Identity loaded directly from repository
// can be used to create sessions without authentication.
$id = $idRepository->getIdentity($userIdOrCriteria);
// Passing false prevents session from being persisted.
$sessionHandler->createSession($id, false);
```

