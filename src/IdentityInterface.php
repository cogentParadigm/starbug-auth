<?php

namespace Starbug\Auth;

interface IdentityInterface {
  /**
   * Get the ID string
   *
   * @return string the ID
   */
  public function getId();
  /**
   * Set the ID string
   *
   * @param string $id The ID string
   */
  public function setId($id);
  /**
   * Get the hashed password
   *
   * @return string The hashed password
   */
  public function getHashedPassword();
  /**
   * Set the hashed password
   *
   * @param string $hashedPassword The hashed password
   */
  public function setHashedPassword($hashedPassword);
  /**
   * Get the groups.
   *
   * @return array The groups.
   */
  public function getGroups();
  /**
   * Set the groups
   *
   * @param array $groups The groups
   */
  public function setGroups(array $groups);
  /**
   * Get custom data.
   *
   * @param string $property An optional property name
   *
   * @return array|string The property specified or all properties.
   */
  public function getData($property = false);
  /**
   * Set custom data. This allows you to add custom metadata.
   * This could be a birth date or profile photo, or it could
   * be special authorizations. This is not persistent, so
   * usually it is merely data that has been loaded along with
   * the core identity information to optimize calls to storage.
   *
   * @param array $data New data to replace any old data.
   */
  public function setData(array $data);

  /**
   * See setData method.
   *
   * @param array $data Additional data to add.
   */
  public function addData(array $data);
}
