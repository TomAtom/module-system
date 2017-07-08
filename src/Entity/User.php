<?php

namespace System\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="system_users")
 */
class User implements \Zend\Stdlib\ArraySerializableInterface {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer", name="id_user", options={"unsigned":true})
   * @ORM\GeneratedValue
   */
  public $id_user;

  /**
   * @ORM\Column(type="string", name="name", length=20, nullable=false)  
   */
  public $name;

  /**
   * @ORM\Column(type="string", name="surname", length=20, nullable=false)  
   */
  public $surname;

  /**
   * @ORM\Column(type="string", name="email", length=25, nullable=false, unique=true)  
   */
  public $email;

  /**
   * @ORM\Column(type="string", name="password", length=33, nullable=false)  
   */
  public $password;

  /**
   * @ORM\Column(type="datetime", name="last_login", nullable=true)  
   */
  public $last_login;

  /**
   * @ORM\Column(type="boolean", name="is_admin", nullable=false, options={"default": 0})  
   */
  public $is_admin;

  /**
   * @ORM\Column(type="boolean", name="is_active", nullable=false, options={"default":1})  
   */
  public $is_active;

  /**
   * @ORM\Column(type="datetime", name="datetime_create", nullable=false)  
   */
  public $datetime_create;

  /**
   * @ORM\ManyToMany(targetEntity="System\Entity\Role", inversedBy="users")
   * @ORM\JoinTable(name="system_users_roles",
   *      joinColumns={@ORM\JoinColumn(name="id_user", referencedColumnName="id_user", onDelete="CASCADE")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="id_role", referencedColumnName="id_role", onDelete="CASCADE")}
   *      )
   */
  private $roles;

  /**
   * Constructor
   */
  public function __construct() {
    $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
  }

  public function getIdUser(): int {
    return $this->id_user;
  }

  public function setName(string $name) {
    $this->name = $name;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setSurname(string $surname) {
    $this->surname = $surname;
  }

  public function getSurname(): string {
    return $this->surname;
  }

  public function setEmail(string $email) {
    $this->email = $email;
  }

  public function getEmail(): string {
    return $this->email;
  }

  public function setPassword(string $password) {
    $this->password = $password;
  }

  public function getPassword(): string {
    return $this->password;
  }

  public function setLastLogin(\DateTime $lastLogin) {
    $this->last_login = $lastLogin;
  }

  public function getLastLogin(): ?\DateTime {
    return $this->last_login;
  }

  public function setIsAdmin(bool $isAdmin) {
    $this->is_admin = $isAdmin;
  }

  public function getIsAdmin(): bool {
    return $this->is_admin;
  }

  public function setIsActive(bool $isActive) {
    $this->is_active = $isActive;
  }

  public function getIsActive(): bool {
    return $this->is_active;
  }

  public function setDatetimeCreate(\DateTime $datetimeCreate) {
    $this->datetime_create = $datetimeCreate;
  }

  public function getDatetimeCreate(): \DateTime {
    return $this->datetime_create;
  }

  public function addRole(\System\Entity\Role $role) {
    $this->roles[] = $role;
  }

  public function removeRole(\System\Entity\Role $role) {
    $this->roles->removeElement($role);
  }

  public function getRoles(): \Doctrine\Common\Collections\Collection {
    return $this->roles;
  }

  public function getArrayCopy(): array {
    return \get_object_vars($this);
  }

  public function exchangeArray(array $data) {
    $this->id_user = (isset($data['id_user'])) ? $data['id_user'] : null;
    $this->name = (isset($data['name'])) ? $data['name'] : null;
    $this->surname = (isset($data['surname'])) ? $data['surname'] : null;
    $this->email = (isset($data['email'])) ? $data['email'] : null;
    if (array_key_exists('last_login', $data)) {
      $this->last_login = $data['last_login'];
    }
    if (array_key_exists('is_admin', $data)) {
      $this->is_admin = (bool) $data['is_admin'];
    }
    if (array_key_exists('is_active', $data)) {
      $this->is_active = (bool) $data['is_active'];
    }
  }

}