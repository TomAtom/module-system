<?php

namespace System;

use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class Acl extends \Zend\Permissions\Acl\Acl implements \Zend\ServiceManager\ServiceLocatorAwareInterface {
    
    private $serviceLocator;

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
 
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
    
    public function setRoles(\System\Model\RoleTable $roleTable) {
        $roles = $roleTable->fetchAll();
        foreach ($roles as $role) {
            $this->addRole(new Role($role->id_role));
        }
    }
    
    public function setResources($config) {
        foreach ($config['controllers']['invokables'] as $controllerAlias => $controllerClass) {
            $this->addResource(new Resource($controllerAlias));
        }
    }
    
    public function setRights(\System\Model\RightTable $rightTable) {
        $rights = $rightTable->fetchAll();
        foreach ($rights as $right) {
            $this->allow($right->id_role, $right->controller, $right->action);
        }
        $this->allow(null, 'System\Controller\Authentification', null);
    }

    public function isCurrentUserAllowed($controller, $action) {
        $authService = $this->getServiceLocator()->get('AuthService');
        if ($authService->hasIdentity()) {
            $identity = $authService->getIdentity();
            return $this->isAllowed($identity->id_role, $controller, $action);
        } else {
            return $this->isAllowed(\System\Model\RoleTable::ID_ROLE_GUEST, $controller, $action);
        }
    }
    
}