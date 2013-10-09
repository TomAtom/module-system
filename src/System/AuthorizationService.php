<?php

namespace System;

class AuthorizationService {
    
    protected $serviceManager;
    
    public function doAuthorization(\Zend\Mvc\MvcEvent $e) {
        $this->serviceManager = $e->getApplication()->getServiceManager();
        $acl = $this->serviceManager->get('System\Acl');
        if ($acl->isCurrentUserAllowed($e->getRouteMatch()->getParam('controller'),
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