<?php

namespace Drupal\general\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Class CompanyRouteProvider.
 */
class CompanyRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    $entity_type_id = $entity_type->id();

    $merge_form_route = $this->getMergeFormRoute($entity_type);
    if ($merge_form_route instanceof Route) {
      $collection->add("entity.{$entity_type_id}.merge_form", $merge_form_route);
    }

    return $collection;
  }

  /**
   * Gets the merge-form route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getMergeFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('merge-form')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('merge-form'));
      $route
        ->addDefaults([
          '_entity_form' => "{$entity_type_id}.merge",
          '_title' => 'Merge Company',
        ])
        ->setRequirement('_entity_access', "{$entity_type_id}.merge")
        ->setOption('parameters', [
          $entity_type_id => ['type' => 'entity:' . $entity_type_id],
        ]);

      // Entity types with serial IDs can specify this in their route
      // requirements, improving the matching process.
      if ($this->getEntityTypeIdKeyType($entity_type) === 'integer') {
        $route->setRequirement($entity_type_id, '\d+');
      }
      return $route;
    }
    return NULL;
  }

}
