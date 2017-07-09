<?php

namespace System\Service;

class RoleManager {
  
  const ID_ROLE_GUEST = 1;

  /**
   * @var \Doctrine\ORM\EntityManager
   */
  private $entityManager;

  public function __construct(\Doctrine\ORM\EntityManager $entityManager) {
    $this->entityManager = $entityManager;
  }

  public function add(\System\Entity\Role $role) {
    $this->checkIfExist($role);
    $this->entityManager->persist($role);
    $this->entityManager->flush();
  }

  public function update(\System\Entity\Role $role) {
    $this->checkIfExist($role);
    $this->entityManager->flush();
  }

  private function checkIfExist(\System\Entity\Role $role) {
    $roleByName = $this->entityManager->getRepository(\System\Entity\Role::class)->findOneByName($role->getName());
    if (is_object($roleByName) && $roleByName->getIdRole() != $role->getIdRole()) {
      throw new \System\Exception\AlreadyExistsException("Another role with name " . $role->getName() . " already exists");
    }
  }

  public function delete(\System\Entity\Role $role) {
    $this->entityManager->remove($role);
    $this->entityManager->flush();
  }

  public function updateRights(\System\Entity\Role $role, array $data) {
    $this->entityManager->getConnection()->beginTransaction();
    foreach ($role->getRights() as $right) {
      $this->entityManager->remove($right);
    }
    $this->entityManager->flush();
    foreach ($data as $controller => $actions) {
      foreach ($actions as $action => $actionChecked) {
        if ($actionChecked) {
          $right = new \System\Entity\Right();
          $right->setRole($role);
          $right->setController($controller);
          $right->setAction($action);
          $this->entityManager->persist($right);
        }
      }
    }
    $this->entityManager->flush();
    $this->entityManager->getConnection()->commit();
  }

}