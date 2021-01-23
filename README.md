# Starbug Authentication

An authentication library for the Starbug framework.

# Usage

First you need to construct an instance of `Starbug\Auth\SessionHandler` which requires constructing several prerequesite objects. Initially, using this outside of Starbug is not a priority. Eventually, I would like to supply some factories for easier construction, as well as some repository implementations which only require PDO instances.

## Construction

```php
// The provided repository implementations require the Starbug database interface.
$db = $container->get("Starbug\Core\DatabaseInterface");

// Now the good stuff.
$idRepository = new Starbug\Auth\Repository\IdentityRepository($db);
$cookieSessionExchange = new Starbug\Auth\Http\CookieSessionExchange("secretkey");
$sessionRepository = new Starbug\Auth\Repository\SessionRepository($db, $idRepository);
$sessionStorage = new Starbug\Auth\SessionStorage($sessionRepository, $cookieSessionExchange);
// et voila!
$sessionHandler = new Starbug\Auth\SessionHandler($sessionStorage, $idRepository);
```

## Basic usage

```php
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
```

## Session data

The example below shows how custom data can be stored and retrieved from the Identity object. If you actually want to load custom data when the session is initialized, then you should create a custom IdentityRepository rather than manually setting data after the session is initialized as we are doing below.

```php
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
```

## Transient sessions

You can create sessions without any real credentials simply by loading the Identity from the IdentityRepository and creating a session from it. You can also choose not to persist or activate the session.

```php
// Identity loaded directly from repository
// can be used to create sessions without authentication.
$id = $idRepository->getIdentity($userIdOrCriteria);
// Passing false prevents session from being persisted.
$sessionHandler->createSession($id, false);
```

## CSRF Handler

A CSRF handler is included as a hook to the session handler. To use it, add it as a hook to the SessionHandler.

```php
$csrfExchanger = new Starbug\Auth\Http\CookieCsrfExchange();
$csrfHandler = new Starbug\Auth\Http\CsrfHandler($csrfExchanger, "secretkey");
$sessionHandler->addHook($csrfHandler);
```

From there it will provide two basic functions:

```php
// Check a request token. Do this after the session has started.
$csrfHandler->checkRequestToken($_POST["csrfToken"]);

// Obtain the request to include in a form.
<input type="hidden" name="csrfToken" value="<?php echo $csrfHandler->getRequestToken(); ?>"/>
```

## PSR-15 middlewares

A PSR-15 middleware is included to start the session. Pass it the session handler.

```php
$sessionMiddleware = new Starbug\Auth\Http\AuthenticationMiddleware($sessionHandler);
```

A PSR-15 middleware is also included for CSRF handling. Pass it the CSRF handler.

```php
$csrfMiddleware = new Starbug\Auth\Http\CsrfMiddleware($csrfHandler);
```