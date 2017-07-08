<?php

namespace System\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="system_rights")
 */
class Right {

  /**
   * @ORM\Id
   * @ORM\Column(type="string", name="controller", length=100, nullable=false)  
   */
  public $controller;

  /**
   * @ORM\Id
   * @ORM\Column(type="string", name="action", length=20, nullable=false)
   */
  public $action;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="System\Entity\Role", inversedBy="rights")
   * @ORM\JoinColumn(name="id_role", referencedColumnName="id_role", onDelete="CASCADE")
   */
  private $role;

  public function setController(string $controller) {
    $this->controller = $controller;
  }

  public function getController(): string {
    return $this->controller;
  }

  public function setAction(string $action) {
    $this->action = $action;
  }

  public function getAction(): string {
    return $this->action;
  }

  public function setRole(\System\Entity\Role $role) {
    $this->role = $role;
  }

  public function getRole(): \System\Entity\Role {
    return $this->role;
  }

}