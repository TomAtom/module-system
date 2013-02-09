<?php

namespace System\View\Helper;  
use Zend\View\Helper\AbstractHelper;

class IsAllowed extends AbstractHelper
{   
    
    protected $acl;
    protected $routes;


    public function __construct($acl, $routes) {
        $this->acl = $acl;
        $this->routes = $routes;
    }

    public function __invoke($routeName, $action = null) {
        $routeParams = $this->getRouteControllerAction($routeName);
        if (is_array($routeParams)) {
            return $this->acl->isCurrentUserAllowed($routeParams['controller'],
                                                   ($action == null) ? $routeParams['action'] : $action);
        } else {
            return false;
        }
        
    }
    
    protected function getRouteControllerAction($routeName) {
        if (array_key_exists($routeName, $this->routes)) {
            return $this->routes[$routeName]['options']['defaults'];
        }
    }
    
}  
