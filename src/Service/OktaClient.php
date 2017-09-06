<?php

namespace Drupal\okta_api\Service;

use Okta\Okta;
use Okta\ClientBuilder;
use Drupal\Core\Config\ConfigFactory;

/**
 * Service class for OktaClient
 */
class OktaClient {

  protected $oktaClient = NULL;

  /**
   * Create the Okta API client.
   */
  public function __construct(ConfigFactory $config_factory) {
    // Get the config.
    $this->config = $config_factory->get('okta_api.settings');

    try {
      $this->oktaClient = (new ClientBuilder())
        ->setToken($this->config->get('api_key'))
        ->setOrganizationUrl($this->config->get('organisation_url'))
        ->build();
    }

    // TODO Druplify this.
    catch (\Exception $e) {
      \Drupal::logger('okta_api')->warning("Failed to create Okta Client using API: @message",
        ['@message' => $e->getMessage()]);
    }
  }

}
