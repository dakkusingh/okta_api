<?php

namespace Drupal\okta_api\Service;

use Okta\Users\User;
use Okta\Users\UserProfile;
use Drupal\okta_api\Service\OktaClient;

/**
 * Service class for Users
 */
class Users {

  /**
   * @var \Drupal\okta_api\Service\OktaClient
   */
  public $oktaClient;

  /**
   * Constructor for the OKTA Users class.
   */
  public function __construct(OktaClient $oktaClient) {
    $this->oktaClient = $oktaClient;
  }

  public function userCreate($first_name, $last_name, $email_address) {
    $config = $this->oktaClient->config;

    $user = new User();

    // TODO This seems outdated in the OKTA API documentation.
    // Check API and update.
    /*
    $profile = new UserProfile();

    $profile->setFirstName($first_name)
      ->setLastName($last_name)
      ->setLogin($email_address)
      ->setEmail($email_address);

    $user->setProfile($profile);

    $user->setGroupIds([
      $config->get('default_group_id'),
    ]);*/

    return $user->create() ? TRUE : FALSE;
  }

}
