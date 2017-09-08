<?php

namespace Drupal\okta_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Admin form for Okta API settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'okta_api_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'okta_api.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormTitle() {
    return 'Okta API Settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('okta_api.settings');

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Token'),
      '#description' => $this->t('The API token to use.'),
      '#default_value' => $config->get('api_key'),
    ];

    $form['organisation_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your Okta domain'),
      '#description' => $this->t('The the domain your organisation uses to log into Okta'),
      '#default_value' => $config->get('organisation_url'),
    ];

    $form['default_group_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default group ID'),
      '#description' => $this->t('The default group id to add the user to in Okta'),
      '#default_value' => $config->get('default_group_id'),
    ];

    // Add checkbox to handle okta preview (oktapreview.com) domain.
    $form['preview_domain'] = [
      '#type' => 'checkbox',
      '#title' => 'Use Okta preview domain',
      '#description' => 'If checked, API will use the Okta preview (oktapreview.com) domain.',
      '#return_value' => TRUE,
      '#default_value' => $config->get('preview_domain'),
    ];

    // TODO Remove this once module POC works.
    //$foo = \Drupal::service('okta_api.users')->userCreate('dakku', 'singh', 'email@email');
    //$foo2 = \Drupal::service('okta_api.users')->userGetByEmail('email@email');

    //$foo = \Drupal::service('okta_api.users');
    //$foo = \Drupal::service('okta_api.okta_client');

    //ksm($foo);
    //ksm($foo2);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('okta_api.settings')
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('default_group_id', $form_state->getValue('default_group_id'))
      ->set('organisation_url', $form_state->getValue('organisation_url'))
      ->set('preview_domain', $form_state->getValue('preview_domain'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
