<?php

namespace System\Service;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use System\Entity\User;

class AuthAdapter implements AdapterInterface {

  private $email;
  private $password;
  private $entityManager;

  /**
   * @var \System\Entity\User 
   */
  private $user;

  public function __construct(\Doctrine\ORM\EntityManager $entityManager) {
    $this->entityManager = $entityManager;
  }

  public function setEmail(string $email): void {
    $this->email = $email;
  }

  public function setPassword(string $password): void {
    $this->password = (string) $password;
  }

  public function authenticate(): Result {
    $this->user = $this->entityManager->getRepository(User::class)
            ->findOneByEmail($this->email);
    if ($this->user == null) {
      return new Result(
              Result::FAILURE_IDENTITY_NOT_FOUND, null, ['Invalid credentials.']);
    }

    if (!$this->user->getIsActive()) {
      return new Result(
              Result::FAILURE, null, ['Uživatel nemá povoleno přihlášení.']);
    }

    if (\md5($this->password) === $this->user->getPassword()) {
      $this->user->setLastLogin(new \DateTime);
      $this->entityManager->flush();
      return new Result(
              Result::SUCCESS, $this->email, ['Authenticated successfully.']);
    }
    return new Result(
            Result::FAILURE_CREDENTIAL_INVALID, null, ['Invalid credentials.']);
  }

  public function getIdentityData(): ?\System\Identity {
    if ($this->user) {
      $identity = new \System\Identity();
      $identity->exchangeArray($this->user->getArrayCopy());
      $identity->rolesIds = \array_map(function (\System\Entity\Role $role) {
        return $role->getIdRole();
      }, $this->user->getRoles()->toArray());
      return $identity;
    }
  }

}