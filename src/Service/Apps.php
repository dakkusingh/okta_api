<?php

namespace Drupal\okta_api\Service;

use Okta\Exception as OktaException;
use Okta\Resource\App;

/**
 * Service class for Okta apps.
 */
class Apps {
  protected $apps;

  /**
   * Apps constructor.
   *
   * @param \Drupal\okta_api\Service\OktaClient $oktaClient
   *   An OktaClient.
   */
  public function __construct(OktaClient $oktaClient) {
    $this->apps = new App($oktaClient->Client);
  }

  /**
   * Gets all Okta apps.
   *
   * @return object
   *   A list of Okta apps.
   */
  public function getAllApps() {
    try {
      return $this->apps->get('');
    }
    catch (OktaException $e) {
      $this->logError("Unable to get apps", $e);
      return NULL;
    }
  }

  /**
   * Gets a single Okta app by its ID.
   *
   * @param string $appId
   *   The Okta app ID.
   *
   * @return object
   *   The Okta app.
   */
  public function getAppById($appId) {
    try {
      return $this->apps->get($appId);
    }
    catch (OktaException $e) {
      $this->logError("Unable to get app $appId", $e);
      return NULL;
    }
  }

  /**
   * Assigns a specific user to an app in Okta.
   *
   * @param string $appId
   *   The App ID.
   * @param array $users
   *   An associative array containing the user's credentials
   *   and optionally a profile. Example at:
   *   https://developer.okta.com/docs/api/resources/apps.html#request-example-23.
   *
   * @return object|bool
   *   Returns FALSE if there was a problem or the response object if
   *   successful.
   */
  public function assignUsersToApp($appId, array $users) {
    try {
      return $this->apps->assignUser($appId, $users);
    }
    catch (OktaException $e) {
      $this->logError("Unable to assign user " . $users['id'] . " to app $appId", $e);
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
