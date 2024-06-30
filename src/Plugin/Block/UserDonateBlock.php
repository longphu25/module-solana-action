<?php

declare(strict_types=1);

namespace Drupal\solana_action\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an user donate block.
 *
 * @Block(
 *   id = "solana_action_user_donate",
 *   admin_label = @Translation("User Donate"),
 *   category = @Translation("SolanaActionBlock"),
 * )
 */
final class UserDonateBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'example' => $this->t('Hello world!'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['example'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Example'),
      '#default_value' => $this->configuration['example'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['example'] = $form_state->getValue('example');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build['content'] = [
      '#markup' => $this->t('It works!'),
    ];
    return $build;
  }

}
