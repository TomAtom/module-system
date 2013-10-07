<?php

namespace System;

class Acl extends \Zend\Permissions\Acl\Acl {
    
    private $authService;
    
    public function __construct(\Zend\Authentication\AuthenticationService $authService) {
        $this->authService = $authService;
    }
    
    public function isCurrentUserAllowed($controller, $action) {
        if ($this->authService->hasIdentity()) {
            $identity = $this->authService->getIdentity();
            return $this->isAllowed($identity->id_role, $controller, $action);
        } else {
            return $this->isAllowed(\System\Model\RoleTable::ID_ROLE_GUEST, $controller, $action);
        }
    }
    
}