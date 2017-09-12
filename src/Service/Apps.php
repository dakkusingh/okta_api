<?php

namespace Drupal\okta_api\Service;

use Okta\Exception;
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
    catch (Exception $e) {
      // TODO: Handle exceptions.
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
    catch (Exception $e) {
      // TODO: Handle exceptions.
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
   * @return object
   *   The response.
   */
  public function assignUsersToApp($appId, array $users) {
    try {
      return $this->apps->assignUser($appId, $users);
    }
    catch (Exception $e) {
      // TODO: Handle exceptions.
    }
  }

}
