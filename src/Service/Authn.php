<?php

namespace Drupal\okta_api\Service;

use Okta\Exception as OktaException;
use Okta\Resource\Authentication;

/**
 * Service class for Authn.
 */
class Authn {

  public $oktaClient;
  public $authn;

  /**
   * Constructor for the Okta Authn class.
   *
   * @param \Drupal\okta_api\Service\OktaClient $oktaClient
   *   An OktaClient.
   */
  public function __construct(OktaClient $oktaClient) {
    $this->oktaClient = $oktaClient->Client;
    $this->authn = new Authentication($oktaClient->Client);
  }

  /**
   * Starts a new password recovery transaction for a given user and issues a
   * recovery token that can be used to reset a user's password.
   *
   * @param  string $username   User's non-qualified short-name or unique
   *                            fully-qualified login
   * @param  string $relayState Optional state value that is persisted for the
   *                            lifetime of the recovery transaction
   *
   * @return object             Recovery Transaction Object
   */
  public function forgotPassword($username, $relayState = NULL) {
    try {
      $recovery = $this->authn->forgotPassword($username, NULL, $relayState);
      return $recovery;
    }
    catch (OktaException $e) {
      $this->logError("Authn error", $e);
      return FALSE;
    }
  }

  /**
   * Resets a user's password to complete a recovery transaction with a
   * PASSWORD_RESET state.
   *
   * @param  string $stateToken  State token for current transaction
   * @param  string $newPassword User's new password
   *
   * @return object              Recovery Transaction Object
   */
  public function resetPassword($stateToken, $newPassword) {
    try {
      $reset = $this->authn->resetPassword($stateToken, $newPassword);
      return $reset;
    }
    catch (OktaException $e) {
      $this->logError("Authn error - couldnt reset password", $e);
      return FALSE;
    }
  }

  /**
   * Validates a recovery token that was distributed to the end-user to
   * continue the recovery transaction.
   *
   * @param  string $recoveryToken Recovery token that was distributed to
   *                               end-user via out-of-band mechanism such as email
   *
   * @return object                Recovery Transaction Object
   */
  public function verifyRecoveryToken($recoveryToken) {
    try {
      return $this->authn->verifyRecoveryToken($recoveryToken);
    }
    catch (OktaException $e) {
      $this->logError("Authn error - couldnt verifyRecoveryToken", $e);
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
