<?php

namespace Drupal\okta_api\Service;

use Okta\Exception as OktaException;
use Okta\Resource\User;

/**
 * Service class for Users.
 */
class Users {

  public $oktaClient;
  public $user;

  /**
   * Constructor for the Okta Users class.
   *
   * @param \Drupal\okta_api\Service\OktaClient $oktaClient
   *   An OktaClient.
   */
  public function __construct(OktaClient $oktaClient) {
    $this->oktaClient = $oktaClient->Client;
    $this->user = new User($oktaClient->Client);
  }

  /**
   * Create Okta User.
   *
   * @param string $first_name
   *   First name.
   * @param string $last_name
   *   Last name.
   * @param string $email_address
   *   Email address.
   *
   * @return bool|object
   *   Returns the user if creation was successful or FALSE if not.
   */
  public function userCreate($first_name, $last_name, $email_address) {

    $existingUser = $this->getUserIfExists($email_address);
    if ($existingUser) {
      return $existingUser;
    }

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
    catch (OktaException $e) {
      $this->logError("Unable to create user", $e);
      return FALSE;
    }
  }

  /**
   * Creates an Okta user and adds them to an app.
   *
   * @param string $first_name
   *   First name.
   * @param string $last_name
   *   Last name.
   * @param string $email_address
   *   Email address.
   * @param string $appId
   *   App ID.
   *
   * @return bool|object
   *   Returns the user if creation was successful or FALSE if not.
   */
  public function userCreateAndAssignToApp($first_name, $last_name, $email_address, $appId) {
    $createdUser = $this->userCreate($first_name, $last_name, $email_address);
    $appService = \Drupal::service('okta_api.apps');

    $credentials = [
      'id' => $createdUser->id,
      'scope' => 'USER',
      'credentials' => ['userName' => $createdUser->profile->email],
    ];

    $result = $appService->assignUsersToApp($appId, $credentials);
    return $result;
  }

  /**
   * Create many Okta users.
   *
   * @param array $users
   *   An associative array of users containing firstName, lastName and email.
   *
   * @return array
   *   Returns an array of created users.
   */
  public function userCreateMany(array $users) {

    $createdUsers = [];

    foreach ($users as $user) {
      array_push($createdUsers, $this->userCreate($user['firstName'], $user['lastName'], $user['email']));
    }

    return $createdUsers;
  }

  /**
   * Check if Okta User exists.
   */
  private function getUserIfExists($email_address) {
    try {
      $existingUser = $this->userGetByEmail($email_address);
      if ($existingUser) {
        return $existingUser;
      }
    }
    catch (OktaException $e) {
      return FALSE;
    }

    return FALSE;
  }

  /**
   * Save changes to an Okta User.
   *
   * @param \Okta\Resource\User $user
   *   The Okta User to save.
   */
  public function userSave($user) {
    // TODO: Add user save logic.
  }

  /**
   * Get Okta User by email.
   *
   * @param string $email_address
   *   Email address.
   *
   * @return null|object
   *   Returns the Okta User.
   */
  public function userGetByEmail($email_address) {
    try {
      $user = $this->user->get($email_address);
      return $user;
    }
    catch (OktaException $e) {
      $this->logError("Unable to get user", $e);
      return NULL;
    }
  }

  /**
   * Get all Okta Users.
   */
  public function userGetAll() {
    try {
      $users = $this->user->get('');
      return $users;
    }
    catch (OktaException $e) {
      $this->logError("Unable to get users", $e);
      return NULL;
    }
  }

  /**
   * Activate Okta User.
   *
   * @param string $email_address
   *   The email address of the user to activate.
   *
   * @return bool|object
   *   Returns FALSE if unsuccessful or a response object if successful.
   */
  public function userActivate($email_address) {
    try {
      $response = $this->user->activate($email_address);
      return $response;
    }
    catch (OktaException $e) {
      $this->logError("Unable to activate user $email_address", $e);
      return FALSE;
    }
  }

  /**
   * Deactivate Okta User.
   *
   * @param string $user_id
   *   The User ID to deactivate.
   *
   * @return bool|\Okta\Resource\empty
   *   Returns FALSE if unsuccessful or a response object if successful.
   */
  public function userDeactivate($user_id) {
    try {
      $response = $this->user->deactivate($user_id);
      return $response;
    }
    catch (OktaException $e) {
      $this->logError("Unable to deactivate user $user_id", $e);
      return FALSE;
    }
  }

  /**
   * Logs an error to the Drupal error log.
   *
   * @param string $message
   *   The error message.
   * @param \Okta\Exception $e
   *   The exception being handled.
   */
  private function logError($message, OktaException $e) {
    \Drupal::logger('okta_api')->error("@message - @exception", ['@message' => $message, '@exception' => $e->getErrorSummary()]);
  }

}
