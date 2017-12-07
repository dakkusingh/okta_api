<?php

namespace Drupal\okta_api\Service;

use Okta\Client;
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
    $config = $config_factory->get('okta_api.settings');
    $domain = $config->get('okta_domain');

    $oktaClientConfig = [
      // Don't auto-bootstrap the Okta resource properties.
      'bootstrap' => FALSE,
      // Use the okta preview (oktapreview.com) domain.
      'preview' => $config->get('preview_domain'),
      // 'headers' => [
      // 'Some-Header'    => 'Some value',
      // 'Another-Header' => 'Another value'
      // ].
    ];

    if (isset($domain) && $domain !== '') {
      $oktaClientConfig['domain'] = $domain;
    }

    $this->Client = new Client(
        $config->get('organisation_url'),
        $config->get('okta_api_key'),
        $oktaClientConfig
      );
  }

}
