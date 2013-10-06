<?php

namespace System;

class Acl extends \Zend\Permissions\Acl\Acl {

    public function isCurrentUserAllowed($controller, $action) {
        $storage = new \Zend\Authentication\Storage\Session();
        $identity = $storage->read();
        if (is_object($identity)) {
            return $this->isAllowed($identity->id_role, $controller, $action);
        } else {
            return $this->isAllowed(\System\Model\RoleTable::ID_ROLE_GUEST, $controller, $action);
        }
    }
    
}