<?php

namespace Drupal\okta_api\Service;

use Okta\Resource\User;
use Okta\Exception;

/**
 * Service class for User Profile.
 */
class Profile {

  /**
   * @var \Drupal\okta_api\Service\OktaClient
   */
  public $oktaClient;

  /**
   * Constructor for the OKTA User Profile class.
   */
  public function __construct(OktaClient $oktaClient) {
    $this->oktaClient = $oktaClient->Client;
    $this->user = new User($oktaClient->Client);
    $this->oktaConfig = $oktaClient->config;
  }

  // TODO Extend the Profile
  //public function profileGet($something) {}

  // TODO Extend the Profile
  public function profileSet($first_name, $last_name, $email_address, $user) {
    // TODO Extend the Profile, the code below needs refactoring.
    /*$this->profile = new UserProfile();

    $this->profile->setFirstName($first_name)
      ->setLastName($last_name)
      ->setLogin($email_address)
      ->setEmail($email_address);

    $user->setProfile($this->profile);

    return $user;*/
  }

}
