<?php

namespace System\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="system_roles")
 */
class Role implements \Zend\Stdlib\ArraySerializableInterface {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer", name="id_role", options={"unsigned":true})
   * @ORM\GeneratedValue
   */
  public $id_role;

  /**
   * @ORM\Column(type="string", name="name", length=25, nullable=false, unique=true)  
   */
  public $name;

  /**
   * @ORM\OneToMany(targetEntity="System\Entity\Right", mappedBy="role")
   * @ORM\JoinColumn(name="id_role", referencedColumnName="id_role")
   */
  private $rights;

  /**
   * @ORM\ManyToMany(targetEntity="System\Entity\User", mappedBy="roles")
   */
  private $users;

  public function __construct() {
    $this->rights = new \Doctrine\Common\Collections\ArrayCollection();
    $this->users = new \Doctrine\Common\Collections\ArrayCollection();
  }

  public function getIdRole(): int {
    return $this->id_role;
  }

  public function setName(string $name) : void {
    $this->name = $name;
  }

  public function getName(): string {
    return $this->name;
  }

  public function addRight(\System\Entity\Right $right) : void {
    $this->rights[] = $right;
  }

  public function removeRight(\System\Entity\Right $right) : void {
    $this->rights->removeElement($right);
  }

  public function getRights(): \Doctrine\Common\Collections\Collection {
    return $this->rights;
  }

  public function addUser(\System\Entity\User $user) : void {
    $this->users[] = $user;
  }

  public function removeUser(\System\Entity\User $user) : void {
    $this->users->removeElement($user);
  }

  public function getUsers(): \Doctrine\Common\Collections\Collection {
    return $this->users;
  }

  public function exchangeArray(array $data) {
    $this->id_role = (isset($data['id_role'])) ? $data['id_role'] : null;
    $this->name = (isset($data['name'])) ? $data['name'] : null;
  }

  public function getArrayCopy() {
    return get_object_vars($this);
  }

}