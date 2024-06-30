<?php

declare(strict_types=1);

namespace Drupal\solana_action\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for user donation routes.
 */
class UserDonateController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new UserDonateController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

  /**
   * Responds to route /user/{user_id}/donate.
   *
   * @param int $user_id
   *   The user ID.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response.
   */
  public function donate($user_id) {

    // $user_id = $this->entityTypeManager->getStorage('user')->getQuery()
    //   ->condition('uuid', $uuid)
    //   ->execute();
    $user = $this->entityTypeManager->getStorage('user')->load($user_id);

    if (!$user) {
      throw new NotFoundHttpException($this->t('User with ID @user_id not found.', ['@user_id' => $user_id]));
    }

    // https://solana.com/docs/advanced/actions#example-get-response-with-parameters
    $getResponse = [
      "icon" => "<url-to-image>",
      "label" => "Donate SOL",
      "title" => "Donate to {user_name}",
      "description" => "Help support this {user_name} by donating SOL.",
      "links" => [
        "actions" => [
            [
              "label" => "Donate",
              "href" => "/user/{user_id}/donate/{amount}",
              "parameters" => [
                    [
                      "name" => "amount",
                      "label" => "SOL amount",
                    ],
              ],
            ],
        ],
      ],
    ];

    $response_data = $getResponse;
    // Get link form field avatar image of user.
    $user_picture = $user->get('user_picture')->entity->getFileUri();
    // $user_picture_url = file_create_url($user_picture); // Deprecated.
    // https://www.drupal.org/node/2940031
    $user_picture_url = \Drupal::service('file_url_generator')->generateAbsoluteString($user_picture);
    $response_data['icon'] = $user_picture_url;
    $link_actions_href = &$response_data['links']['actions'][0]['href'];
    $link_actions_href = str_replace('{user_id}', $user_id, $link_actions_href);
    $user_name = $user->getDisplayName();
    if ($user->hasField('field_full_name')) {
      if ($field_full_name = $user->get('field_full_name')->value) {
        $user_name = $field_full_name;
      }
    }
    $response_data['title'] = str_replace('{user_name}', $user_name, $response_data['title']);
    $response_data['description'] = str_replace('{user_name}', $user_name, $response_data['description']);

    // $response_data = [
    //   'user_id' => $user_id,
    //   'message' => $this->t('Donation page for user @user_id.', ['@user_id' => $user_id]),
    // ];
    $response = new JsonResponse($response_data);
    // Set CORS headers for this response only.
    $response->headers->set('Access-Control-Allow-Origin', '*');

    return $response;
  }

}
