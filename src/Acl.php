<?php

namespace System;

use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class Acl extends \Zend\Permissions\Acl\Acl {

  public function setRoles(array $roles) {
    foreach ($roles as $role) {
      $this->addRole(new Role($role->id_role));
    }
  }

  public function setResources($config) {
    if (\array_key_exists('invokables', $config['controllers'])) {
      foreach ($config['controllers']['invokables'] as $controllerAlias => $controllerClass) {
        $this->addResource(new Resource($controllerAlias));
      }
    }
    foreach ($config['controllers']['factories'] as $controllerAlias => $controllerFactoryClass) {
      $this->addResource(new Resource($controllerAlias));
    }
  }

  public function setRights(array $rights) {
    foreach ($rights as $right) {
      if ($this->hasResource($right->controller)) {
        $role = $right->getRole();
        $this->allow($role->getIdRole(), $right->controller, $right->action);
      }
    }
    $this->allow(null, \System\Controller\Authentification::class, null);
  }

}