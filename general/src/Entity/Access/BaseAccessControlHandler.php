<?php

namespace Drupal\general\Entity\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access base controller for the entity.
 */
class BaseAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, "view {$this->entityTypeId} entities");

      case 'update':
        return AccessResult::allowedIfHasPermission($account, "edit {$this->entityTypeId} entities");

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, "delete {$this->entityTypeId} entities");
    }

    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, "add {$this->entityTypeId} entities");
  }

}
