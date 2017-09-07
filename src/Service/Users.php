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
    // TODO Set profile here or somewhere else?
    $user = userSetProfile($first_name, $last_name, $email_address, $user)

    $user->setGroupIds([
      $config->get('default_group_id'),
    ]);*/

    return $user->create() ? TRUE : FALSE;
  }

  // TODO Extend the CRUD
  /*public function userSave($user) {
    return $user->save() ? TRUE : FALSE;
  }*/

  public function userGetByEmail($email_address) {
    $user = new User();
    return $user->get($email_address);
  }

  // TODO Extend the CRUD
  //public function userUpdate($something) {}

  // TODO Extend the CRUD
  //public function userDelete($something) {}

  // TODO Extend the Profile
  //public function userGetProfile($something) {}

  // TODO Extend the Profile
  /*public function userSetProfile($first_name, $last_name, $email_address, $user) {
    $profile = new UserProfile();

    $profile->setFirstName($first_name)
      ->setLastName($last_name)
      ->setLogin($email_address)
      ->setEmail($email_address);

    $user->setProfile($profile);
    return $user;
  }*/



}
