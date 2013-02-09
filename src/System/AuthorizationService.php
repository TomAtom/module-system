<?php

namespace System;

use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class AuthorizationService {
    
    protected $serviceManager;
    protected $acl;
    
    public function doAuthorization(\Zend\Mvc\MvcEvent $e) {
        $this->acl = \System\Acl::getInstance();
        $this->serviceManager = $e->getApplication()->getServiceManager();
        $this->setRoles();
        $this->setResources();
        $this->setRights();
        if ($this->acl->isCurrentUserAllowed($e->getRouteMatch()->getParam('controller'),
                                             $e->getRouteMatch()->getParam('action'))) {
        } else {
            if ($this->existsControllerAction($e)) {
                $url = $e->getRouter()->assemble(array(), array('name' => 'authentification'));
                $response = $e->getResponse();
                $response->setStatusCode(302);
                $response->getHeaders()->addHeaderLine('Location', $url);
                $e->stopPropagation();
            }
        }
    }
    
    protected function setRoles() {
        $roleTable = $this->serviceManager->get('System\Model\RoleTable');
        $roles = $roleTable->fetchAll();
        foreach ($roles as $role) {
            $this->acl->addRole(new Role($role->id_role));
        }
    }
    
    protected function setResources() {
        $config = $this->serviceManager->get('Config');
        foreach ($config['controllers']['invokables'] as $controllerAlias => $controllerClass) {
            $this->acl->addResource(new Resource($controllerAlias));
        }
    }
    
    protected function setRights() {
        $rightTable = $this->serviceManager->get('System\Model\RightTable');
        $rights = $rightTable->fetchAll();
        foreach ($rights as $right) {
            $this->acl->allow($right->id_role, $right->controller, $right->action);
        }
        $this->acl->allow(null, 'System\Controller\Authentification', null);
    }
    
    protected function existsControllerAction(\Zend\Mvc\MvcEvent $e) {
        $return = false;
        $config = $this->serviceManager->get('Config');
        if (array_key_exists($e->getRouteMatch()->getParam('controller'), $config['controllers']['invokables'])) {
           $classMethods = get_class_methods($config['controllers']['invokables'][$e->getRouteMatch()->getParam('controller')]);
           if (in_array($e->getRouteMatch()->getParam('action').'Action', $classMethods)) {
               $return = true;
           }
        }
        return $return;
    }
    
}