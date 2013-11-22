<?php

namespace SystemTest\Controller;

use SystemTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use System\Controller\UserController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;

class UserControllerTest extends PHPUnit_Framework_TestCase {

    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;

    protected function setUp() {
        $serviceManager = Bootstrap::getServiceManager();
        $this->controller = new UserController();
        $this->request = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'index'));
        $this->event = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
    }
    
    public function testLoggingInShouldBeOk() {
        $this->login();
        $this->assertTrue($this->controller->getServiceLocator()->get('AuthentificationService')->hasIdentity());
    }
    
    protected function login() {
        $serviceManager = Bootstrap::getServiceManager();
        $controller = new \System\Controller\AuthentificationController();
        $controller->setEvent($this->event);
        $controller->setServiceLocator($serviceManager);
        $form = new \System\Form\LoginForm();
        $form->prepare();
        $this->request->setPost(new \Zend\Stdlib\Parameters(
               array(
           'email' => 'admin@admin.cz',
           'password' => 'admin@admin.cz',
           'csrf' => $form->get('csrf')->getValue(),
           'submit' => 'Přihlásit'
        )));
        $this->request->setMethod('POST');
        $this->routeMatch->setParam('action', 'login');
        $result = $controller->dispatch($this->request);
        $response = $controller->getResponse();
    }

    public function testIndexActionCanBeAccessed() {
        $this->routeMatch->setParam('action', 'index');
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testAddActionCanBeAccessed() {
        $this->routeMatch->setParam('action', 'add');
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testEditActionCanBeAccessed() {
        $this->routeMatch->setParam('action', 'edit');
        $this->routeMatch->setParam('id', '1');
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testDeleteCanBeAccessed() {
        $this->routeMatch->setParam('action', 'delete');
        $this->routeMatch->setParam('id', '1');
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testProfileActionCanBeAccessed() {
        $this->login();
        $this->routeMatch->setParam('action', 'profile');
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
}
