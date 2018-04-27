<?php

namespace Drupal\okta_api\Service;

use Okta\Exception as OktaException;
use Okta\Resource\User;

/**
 * Class Users.
 *
 * @package Drupal\okta_api\Service
 */
class Users {

  /**
   * Okta Client.
   *
   * @var \Drupal\okta_api\Service\OktaClient
   */
  public $oktaClient;

  /**
   * Okta User Resource.
   *
   * @var \Okta\Resource\User
   */
  public $user;

  /**
   * Users constructor.
   *
   * @param \Drupal\okta_api\Service\OktaClient $oktaClient
   *   Okta Client.
   */
  public function __construct(OktaClient $oktaClient) {
    $this->oktaClient = $oktaClient;
    $this->user = new User($oktaClient->Client);
  }

  /**
   * Creates an Okta User.
   *
   * @param array $profile
   *   The new user's profile.
   * @param array|null $credentials
   *   The new user's credentials.
   * @param array|null $provider
   *   The authentication provider, if using.
   * @param bool $activate
   *   TRUE if the user should be activated after creation.
   *
   * @return bool|object
   *   Returns the user if creation was successful or FALSE if not.
   */
  public function userCreate(array $profile, $credentials = [], $provider = [], $activate = TRUE) {

    $existingUser = $this->getUserIfExists($profile['email']);

    if ($existingUser) {
      return $existingUser;
    }

    try {
      $user = $this->user->create($profile, $credentials, $provider, $activate);
      $this->oktaClient->debug($user, 'response');
      return $user;
    }
    catch (OktaException $e) {
      $this->logError("Unable to create user", $e);
      return FALSE;
    }
  }

  /**
   * Builds a profile array for a user.
   *
   * @param string $first_name
   *   First name.
   * @param string $last_name
   *   Last name.
   * @param string $email_address
   *   Email address.
   * @param string $login
   *   Login.
   *
   * @return array
   *   Returns the profile array.
   */
  public function buildProfile($first_name, $last_name, $email_address, $login) {
    $profile = [
      "firstName" => $first_name,
      "lastName" => $last_name,
      "email" => $email_address,
      "login" => $login,
    ];

    return $profile;
  }

  /**
   * Builds a credentials array for a user.
   *
   * @param string $password
   *   Password.
   * @param array|null $recovery_question
   *   An optional recovery question array containing 'question' and 'answer'.
   *
   * @return array
   *   Returns the credentials array.
   */
  public function buildCredentials($password, array $recovery_question = NULL) {
    $credentials = [
      "password" => $password,
      "recovery_question" => $recovery_question,
    ];

    return $credentials;
  }

  /**
   * Creates an Okta user and adds them to an app.
   *
   * @param string $appId
   *   The Okta App ID to assign the user to.
   * @param array $profile
   *   The user's profile.
   * @param array $credentials
   *   The user's credentials.
   * @param array $provider
   *   The authentication provider, if using.
   * @param bool $activate
   *   TRUE if the user should be activated after creation.
   *
   * @return bool|object
   *   Returns the user if creation was successful or FALSE if not.
   */
  public function userCreateAndAssignToApp($appId,
                                           array $profile,
                                           array $credentials = [],
                                           array $provider = [],
                                           $activate = TRUE) {
    $createdUser = $this->userCreate($profile, $credentials, $provider, $activate);
    $appService = \Drupal::service('okta_api.apps');

    $credentials = [
      'id' => $createdUser->id,
      'scope' => 'USER',
      'credentials' => ['userName' => $createdUser->profile->email],
    ];

    $result = $appService->assignUsersToApp($appId, $credentials);
    $this->oktaClient->debug($result, 'response');
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
      array_push(
        $createdUsers,
        $this->userCreate(
          $user['profile'],
          $user['credentials'],
          $user['provider'],
          $user['activate']
        )
      );
    }

    return $createdUsers;
  }

  /**
   * Create many Okta users and assign them to an app.
   *
   * @param array $users
   *   An associative array of users containing firstName, lastName and email.
   * @param string $appId
   *   App ID.
   *
   * @return array
   *   Returns an array of created users.
   */
  public function userCreateManyAndAssignToApp(array $users, $appId) {
    $createdUsers = [];

    foreach ($users as $user) {
      array_push(
        $createdUsers,
        $this->userCreateAndAssignToApp(
          $appId,
          $user['profile'],
          $user['credentials'],
          $user['provider'],
          $user['activate']
        )
      );
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
   * @param object $user
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
      $this->oktaClient->debug($user, 'response');
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
      $this->oktaClient->debug($users, 'response');
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
   * @param string $uid
   *   The ID of the user to activate.
   * @param bool $sendEmail
   *   Whether to send an activation email from Okta.
   *
   * @return bool|object
   *   Returns FALSE if unsuccessful or a response object if successful.
   */
  public function userActivate($uid, $sendEmail = FALSE) {
    try {
      $response = $this->user->activate($uid, $sendEmail);
      $this->oktaClient->debug($response, 'response');
      return $response;
    }
    catch (OktaException $e) {
      $this->logError("Unable to activate user $uid", $e);
      return FALSE;
    }
  }

  /**
   * Deactivate Okta User.
   *
   * @param string $user_id
   *   The User ID to deactivate.
   *
   * @return bool|object
   *   Returns FALSE if unsuccessful or a response object if successful.
   */
  public function userDeactivate($user_id) {
    try {
      $response = $this->user->deactivate($user_id);
      $this->oktaClient->debug($response, 'response');
      return $response;
    }
    catch (OktaException $e) {
      $this->logError("Unable to deactivate user $user_id", $e);
      return FALSE;
    }
  }

  /**
   * User Expire Password.
   *
   * This operation will transition the user to the status of PASSWORD_EXPIRED
   * and the user will be required to change their password at their next
   * login. If tempPassword is passed, the user's password is reset to a
   * temporary password that is returned, and then the temporary password is
   * expired.
   *
   * @param string $uid
   *   User ID.
   * @param bool $tempPassword
   *   Sets the user's password to a temporary
   *   password, if true.
   *
   * @return object
   *   User object
   */
  public function userExpirePassword($uid, $tempPassword = TRUE) {
    try {
      $response = $this->user->expirePassword($uid, $tempPassword);
      $this->oktaClient->debug($response, 'response');
      return $response;
    }
    catch (OktaException $e) {
      $this->logError("Unable to expire password for user $uid", $e);
      return FALSE;
    }
  }

  /**
   * User Change Password.
   *
   * Changes a user's password by validating the user's current password. This
   * operation can only be performed on users in STAGED, ACTIVE,
   * PASSWORD_EXPIRED, or RECOVERY status that have a valid password
   * credential.
   *
   * @param string $uid
   *   User ID.
   * @param string $oldPass
   *   Current password for user.
   * @param string $newPass
   *   New passwor for user.
   *
   * @return object
   *   User credentials object
   */
  public function userChangePassword($uid, $oldPass, $newPass) {
    try {
      $response = $this->user->changePassword($uid, $oldPass, $newPass);
      $this->oktaClient->debug($response, 'response');
      return $response;
    }
    catch (OktaException $e) {
      $this->logError("Unable to change password for user $uid", $e);
      return FALSE;
    }
  }

  /**
   * Force Changes a user's password by doing user::update()
   *
   * @param string $uid
   *   User ID.
   * @param string $newPass
   *   New password for user.
   *
   * @return object
   *   User credentials object
   */
  public function userForceChangePassword($uid, $newPass) {
    // Create a new credentials array with new password.
    $credentials = ["password" => $newPass];
    try {
      $response = $this->user->update($uid, NULL, $credentials);
      $this->oktaClient->debug($response, 'response');
      return $response;
    }
    catch (OktaException $e) {
      $this->logError("Unable to change password for user $uid", $e);
      return FALSE;
    }
  }

  /**
   * Force Changes a user's security by doing user::update()
   *
   * @param string $uid
   *   User ID.
   * @param string $question
   *   New security question for user.
   * @param string $answer
   *   New security answer for user.
   *
   * @return object
   *   User credentials object
   */
  public function userForceChangeSecurity($uid, $question, $answer) {
    // Create a new credentials array with new question.
    $credentials = [
      'recovery_question' => [
        'question' => $question,
        'answer' => $answer,
      ],
    ];
    try {
      $response = $this->user->update($uid, NULL, $credentials);
      $this->oktaClient->debug($response, 'response');
      return $response;
    }
    catch (OktaException $e) {
      $this->logError("Unable to change security question for user $uid", $e);
      return FALSE;
    }
  }

  /**
   * Update a user's profile and/or credentials with partial update semantics.
   *
   * @param string $uid
   *   ID of user to update.
   * @param array $profile
   *   Array of user profile properties.
   * @param array $credentials
   *   Array of credential properties.
   *
   * @return object
   *   Updated user object
   */
  public function update($uid, array $profile = NULL, array $credentials = NULL) {
    try {
      $response = $this->user->update($uid, $profile, $credentials);
      $this->oktaClient->debug($response, 'response');
      return $response;
    }
    catch (OktaException $e) {
      $this->logError("Unable to update user $uid", $e);
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
    $this->oktaClient->debug($e, 'exception');
    \Drupal::logger('okta_api')->error("@message - @exception", ['@message' => $message, '@exception' => $e->getErrorSummary()]);
  }

  // @codingStandardsIgnoreStart
  /**
   * Example on how to change user password
   * This sets a temporary password first and
   * uses this temp pass as the old password.
   */
  // Private function oktaResetPassword($oktaUserEmail, $newPassword) {
  //    $response = $this->oktaUsers->userExpirePassword($oktaUserEmail);
  //    $tempPassword = $response->tempPassword;
  //    $this->oktaUsers->userChangePassword($oktaUserEmail, $tempPassword, $newPassword);
  //  }.
  /**
   * Example on how to change user password using authn
   * This sets a temporary password first and
   * uses this temp pass as the old password.
   */
  // Public function userResetForgottenPassword($userMail, $newPassword) {
  //    try {
  //      $recovery = $this->authn->forgotPassword($userMail);
  //      if ($recovery) {
  //        $recoveryToken = $recovery->recoveryToken;
  //        $stateTokenObj = $this->authn->verifyRecoveryToken($recoveryToken);
  //        try {
  //          $this->authn->resetPassword($stateTokenObj->stateToken, $newPassword);
  //        }
  //        catch (OktaException $e) {
  //          $this->logError("Unable set new password for $userMail", $e);
  //        }
  //      }
  //      return TRUE;
  //    }
  //    catch (OktaException $e) {
  //      $this->logError("Unable set reset password for $userMail", $e);
  //      return FALSE;
  //    }
  //  }
  // @codingStandardsIgnoreEnd
}
