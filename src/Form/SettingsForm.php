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

    $form['okta_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Token'),
      '#description' => $this->t('The API token to use.'),
      '#default_value' => $config->get('okta_api_key'),
    ];

    $form['organisation_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your Okta organisation'),
      '#description' => $this->t('The the organisation you have set up in Okta'),
      '#default_value' => $config->get('organisation_url'),
    ];

    $form['okta_domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your Okta domain'),
      '#description' => $this->t('The the domain your organisation uses to log into Okta'),
      '#default_value' => $config->get('okta_domain'),
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
      '#description' => $this->t('If checked, API will use the Okta preview (oktapreview.com) domain.'),
      '#return_value' => TRUE,
      '#default_value' => $config->get('preview_domain'),
    ];

    // Check for devel module.
    $devel_module_present = \Drupal::moduleHandler()->moduleExists('devel');

    // Add debugging options.
    $form['debug_response'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Debug OKTA Responses (requires Devel module)'),
      '#description' => $this->t('Show OKTA Responses'),
      '#default_value' => $config->get('debug_response') && $devel_module_present,
      '#disabled' => !$devel_module_present,
    ];

    $form['debug_exception'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Debug OKTA Exception (requires Devel module)'),
      '#description' => $this->t('Show OKTA Exception'),
      '#default_value' => $config->get('debug_exception') && $devel_module_present,
      '#disabled' => !$devel_module_present,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('okta_api.settings')
      ->set('okta_api_key', $form_state->getValue('okta_api_key'))
      ->set('default_group_id', $form_state->getValue('default_group_id'))
      ->set('organisation_url', $form_state->getValue('organisation_url'))
      ->set('okta_domain', $form_state->getValue('okta_domain'))
      ->set('preview_domain', $form_state->getValue('preview_domain'))
      ->set('debug_response', $form_state->getValue('debug_response'))
      ->set('debug_exception', $form_state->getValue('debug_exception'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
