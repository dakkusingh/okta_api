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

}
