<?php

namespace Drupal\general\Entity\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Company Access controller for the entity.
 */
class CompanyAccessControlHandler extends BaseAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'merge':
        return AccessResult::allowedIfHasPermission($account, "merge {$this->entityTypeId} entities");
    }

    return parent::checkAccess($entity, $operation, $account);
  }

}
