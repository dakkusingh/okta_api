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
    $this->config = $config_factory->get('okta_api.settings');
    $domain = $this->config->get('okta_domain');

    $oktaClientConfig = [
      // Don't auto-bootstrap the Okta resource properties.
      'bootstrap' => FALSE,
      // Use the okta preview (oktapreview.com) domain.
      'preview' => $this->config->get('preview_domain'),
      // 'headers' => [
      // 'Some-Header'    => 'Some value',
      // 'Another-Header' => 'Another value'
      // ].
    ];

    if (isset($domain) && $domain !== '') {
      $oktaClientConfig['domain'] = $domain;
    }

    $this->Client = new Client(
        $this->config->get('organisation_url'),
        $this->config->get('okta_api_key'),
        $oktaClientConfig
      );
  }

  /**
   * Debug OKTA response and exceptions.
   *
   * @param mixed $data
   *   Data to debug.
   * @param string $type
   *   Response or Exception.
   */
  public function debug($data, $type = 'response') {
    if ($this->config->get('debug_' . $type)) {
      if (\Drupal::moduleHandler()->moduleExists('devel')) {
        ksm($data);
      }
    }
  }

}
