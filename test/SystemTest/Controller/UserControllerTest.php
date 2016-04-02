<?php

namespace SystemTest\Controller;

use SystemTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use System\Controller\UserController;
use Zend\Http\Request;
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
    $roleTable = $serviceManager->get('System\Model\RoleTable');
    $userRoleTable = $serviceManager->get('System\Model\UserRoleTable');
    $userTable = $serviceManager->get('System\Model\UserTable');
    $authService = $serviceManager->get('AuthentificationService');
    $this->controller = new UserController($userTable, $authService, $roleTable, $userRoleTable);
    $this->request = new Request();
    $this->routeMatch = new RouteMatch(array('controller' => 'System\Controller\User'));
    $this->event = new MvcEvent();
    $config = $serviceManager->get('Config');
    $routerConfig = isset($config['router']) ? $config['router'] : array();
    $router = HttpRouter::factory($routerConfig);

    $this->event->setRouter($router);
    $this->event->setRouteMatch($this->routeMatch);
    $this->controller->setEvent($this->event);
    $this->controller->setServiceLocator($serviceManager);
  }
  
  /**
   * @test
   */
  public function indexActionCanNoBeAccessedWithUnlogedUser() {
//    $serviceManager = Bootstrap::getServiceManager();
//    chdir(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
//    $authorizationService = $serviceManager->get('AuthorizationService');
//    $this->routeMatch->setParam('action', 'index');
//    $authorizationService->doAuthorization($this->event);
//    $this->assertEquals(302, $this->event->getResponse()->getStatusCode());
  }

  /**
   * @test
   */
  public function loggingInShouldBeOk() {
    $this->login();
    $serviceManager = Bootstrap::getServiceManager();
    $this->assertTrue($serviceManager->get('AuthentificationService')->hasIdentity());
  }

  protected function login() {
    $serviceManager = Bootstrap::getServiceManager();
    $authService = $serviceManager->get('AuthentificationService');
    $userRoleTable = $serviceManager->get('System\Model\UserRoleTable');
    $userTable = $serviceManager->get('System\Model\UserTable');
    $controller = new \System\Controller\AuthentificationController($authService, $userRoleTable, $userTable);
    $controller->setEvent($this->event);
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

  /**
   * @test
   */
  public function indexActionCanBeAccessed() {
    $this->routeMatch->setParam('action', 'index');
    $result = $this->controller->dispatch($this->request);
    $response = $this->controller->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
  }

  /**
   * @test
   */
  public function addActionCanBeAccessed() {
    $this->routeMatch->setParam('action', 'add');
    $result = $this->controller->dispatch($this->request);
    $response = $this->controller->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
  }

  /**
   * @test
   */
  public function editActionCanBeAccessed() {
    $this->routeMatch->setParam('action', 'edit');
    $this->routeMatch->setParam('id', '1');
    $result = $this->controller->dispatch($this->request);
    $response = $this->controller->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
  }

  /**
   * @test
   */
  public function deleteCanBeAccessed() {
    $this->routeMatch->setParam('action', 'delete');
    $this->routeMatch->setParam('id', '1');
    $result = $this->controller->dispatch($this->request);
    $response = $this->controller->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
  }

  /**
   * @test
   */
  public function profileActionCanBeAccessed() {
    $this->login();
    $this->routeMatch->setParam('action', 'profile');
    $result = $this->controller->dispatch($this->request);
    $response = $this->controller->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
  }

}
