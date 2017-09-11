<?php

namespace Drupal\okta_api\Service;

use Okta\Client;
use Okta\Exception as OktaException;
use Drupal\Core\Config\ConfigFactory;

/**
 * Service class for OktaClient.
 */
class OktaClient {

  public $oktaClient = NULL;

  /**
   * Create the Okta API client.
   */
  public function __construct(ConfigFactory $config_factory) {
    // Get the config.
    $config = $config_factory->get('okta_api.settings');

    try {
      $this->Client = new Client(
        $config->get('organisation_url'),
        $config->get('api_key'),
        [
          // Don't auto-bootstrap the Okta resource properties.
          'bootstrap' => false,
          // Use the okta preview (oktapreview.com) domain.
          'preview' => $config->get('preview_domain'),
          //'headers' => [
          //  'Some-Header'    => 'Some value',
          //  'Another-Header' => 'Another value'
          //]
        ]
      );
    }
    catch (OktaException $e) {
      // TODO handle exceptions.
      //ksm($e);
      //\Drupal::logger('okta_api')->warning("Failed to create Okta Client using API: @message",
      //  ['@message' => $e->getMessage()]);
    }
  }

}
