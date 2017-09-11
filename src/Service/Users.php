<?php

namespace Drupal\okta_api\Service;

use Okta\Exception;
use Okta\Resource\User;

/**
 * Service class for Users
 */
class Users {

  /**
   * @var \Drupal\okta_api\Service\OktaClient
   */
  public $oktaClient;

  /**
   * Constructor for the Okta Users class.
   */
  public function __construct(OktaClient $oktaClient) {
    $this->oktaClient = $oktaClient->Client;
    $this->user = new User($oktaClient->Client);
    $this->oktaConfig = $oktaClient->config;
  }

  public function userCreate($first_name, $last_name, $email_address) {

    $existingUser = $this->getUserIfExists($email_address);
    if ($existingUser) {
      return $existingUser;
    }

    // TODO Assign user to application
    try {
      $user = $this->user->create(
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
      $user = $this->user->get($email_address);
      return $user;
    }
    catch (OktaException $e) {
      // TODO handle exceptions.
      return $e->getErrorSummary();
    }
  }

  public function userGetAll() {
    // TODO Wrap this around try catch.
    // TODO handle exceptions.
    $users = $this->user->get();
    return $users;
  }

  public function userActivate($email_address) {
    try {
      $response = $this->user->activate($email_address);
      return $response;
    }
    catch (OktaException $e) {
      // TODO handle exceptions.
      return $e->getErrorSummary();
    }
  }

  // TODO Extend the CRUD
  //public function userUpdate($something) {}

  public function userDeactivate($user_id) {
    // TODO Wrap this around try catch.
    // TODO handle exceptions.
    $response = $this->user->deactivate($user_id);
    return $response;
  }

}
