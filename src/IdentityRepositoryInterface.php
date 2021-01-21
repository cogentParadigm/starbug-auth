<?php
namespace Starbug\Auth;

interface IdentityRepositoryInterface {
  /**
   * Retrieve a user.
   *
   * @param int|array $criteria An ID or array of fields to match.
   *
   * @return void
   */
  public function getIdentity($criteria): ?IdentityInterface;
}
