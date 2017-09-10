<?php

namespace Drupal\okta_api\Service;

use Drupal\Core\Config\ConfigFactory;
use Okta\Exception;

/**
 * Service class for Users
 */
class Users extends OktaClient {

  /**
   * Constructor for the Okta Users class.
   */
  public function __construct(ConfigFactory $config_factory) {
    parent::__construct($config_factory);
  }

  public function userCreate($first_name, $last_name, $email_address) {
    $config = $this->oktaClient->config;

    $existingUser = $this->getUserIfExists($email_address);
    if ($existingUser) {
      return $existingUser;
    }

    // TODO Assign user to application
    try {
      $user = $this->oktaClient->user->create(
        [
          "firstName" => $first_name,
          "lastName" => $last_name,
          "email" => $email_address,
          "login" => $email_address,
        ]
      );
      return $user;
    }
    catch (Exception $e) {
      // TODO handle exceptions.
      return $e->getErrorSummary();
    }

  }

  private function getUserIfExists($email_address){
    try {
      $existingUser = $this->userGetByEmail($email_address);
      if ($existingUser) {
        return $existingUser;
      }
    }
    catch (Exception $e) {
      return FALSE;
    }
  }

  // TODO Extend the CRUD
  /*public function userSave($user) {
    return $user->save() ? TRUE : FALSE;
  }*/

  public function userGetByEmail($email_address) {
    try {
      $user = $this->oktaClient->user->get($email_address);
      return $user;
    }
    catch (OktaException $e) {
      // TODO handle exceptions.
      return $e->getErrorSummary();
    }
  }

  public function userGetAll() {
    $users = $this->oktaClient->user->get();
    return $users;
  }

  public function userActivate($email_address) {
    try {
      $response = $this->oktaClient->user->activate($email_address);
      return $response;
    }
    catch (OktaException $e) {
      // TODO handle exceptions.
      return $e->getErrorSummary();
    }
  }

  // TODO Extend the CRUD
  //public function userUpdate($something) {}

  // TODO Extend the CRUD
  //public function userDelete($something) {}

}
