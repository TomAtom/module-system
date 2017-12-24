<?php

namespace System\Service;

class UserManager {

  /**
   * @var Doctrine\ORM\EntityManager
   */
  private $entityManager;

  public function __construct(\Doctrine\ORM\EntityManager $entityManager) {
    $this->entityManager = $entityManager;
  }

  public function add(\System\Entity\User $user) : void {
    $userByEmail = $this->entityManager->getRepository(\System\Entity\User::class)->findOneByEmail($user->getEmail());
    if (is_object($userByEmail) && $userByEmail->getIdUser() != $user->getIdUser()) {
      throw new \System\Exception\AlreadyExistsException("Another user with email address " . $user->getEmail() . " already exists");
    }
    $user->setPassword(\md5($user->getPassword()));
    $user->setDatetimeCreate(new \DateTime());
    $this->entityManager->persist($user);
    $this->entityManager->flush();
  }

  public function update(\System\Entity\User $user) : void  {
    $userByEmail = $this->entityManager->getRepository(\System\Entity\User::class)->findOneByEmail($user->getEmail());
    if (is_object($userByEmail) && $userByEmail->getIdUser() != $user->getIdUser()) {
      throw new \System\Exception\AlreadyExistsException("Another user with email address " . $user->getEmail() . " already exists");
    }
    $this->entityManager->flush();
  }

  public function delete(\System\Entity\User $user) : void  {
    $this->entityManager->remove($user);
    $this->entityManager->flush();
  }

  public function setPassword(\System\Entity\User $user, string $password) : void {
    $user->setPassword(\md5($password));
    $this->entityManager->flush();
  }

  public function assignRoles(\System\Entity\User $user, array $roleIds) : void {
    $user->getRoles()->clear();
    foreach ($roleIds as $roleId) {
      $role = $this->entityManager->getRepository(\System\Entity\Role::class)->find($roleId);
      if ($role == null) {
        throw new \Exception('Not found role by ID');
      }
      $user->addRole($role);
    }
    $this->entityManager->flush();
  }

}