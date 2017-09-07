<?php

namespace Drupal\okta_api\Service;

use Okta\Users\User;
use Okta\Users\UserProfile;
use Drupal\okta_api\Service\OktaClient;

/**
 * Service class for User Profile
 */
class Profile {

  /**
   * @var \Drupal\okta_api\Service\OktaClient
   */
  public $oktaClient;

  /**
   * @var \Drupal\okta_api\Service\Users
   */
  public $oktaUsers;

  /**
   * Constructor for the OKTA User Profile class.
   */
  public function __construct(OktaClient $oktaClient, $oktaUsers) {
    $this->oktaClient = $oktaClient;
    $this->oktaUsers = $oktaUsers;
    $this->oktaProfile = new UserProfile();
  }


  // TODO Extend the Profile
  //public function profileGet($something) {}

  // TODO Extend the Profile
  public function profileSet($first_name, $last_name, $email_address, $user) {
    $this->profile = new UserProfile();

    $this->profile->setFirstName($first_name)
      ->setLastName($last_name)
      ->setLogin($email_address)
      ->setEmail($email_address);

    $user->setProfile($this->profile);
    return $user;
  }



}
