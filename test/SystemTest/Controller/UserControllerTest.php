<?php

namespace SystemTest\Controller;

class UserControllerTest extends \Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase {

  protected $controller;
  protected $request;
  protected $response;
  protected $routeMatch;
  protected $event;

  public function setUp() {
    // The module configuration should still be applicable for tests.
    // You can override configuration here with test case specific values,
    // such as sample view templates, path stacks, module_listener_options,
    // etc.
    $configOverrides = [];
    $this->setApplicationConfig(\Zend\Stdlib\ArrayUtils::merge(
                    // Grabbing the full application configuration:
                    include __DIR__ . '/../../TestConfig.php.dist', $configOverrides
    ));
    parent::setUp();
  }

  /**
   * @test
   */
  public function indexActionCanNotBeAccessedWithUnlogedUser() {
    $this->dispatch('/user');
    $this->assertResponseStatusCode(302);
  }

  /**
   * @test
   */
  public function loggingInShouldBeOk() {
    $this->login();
    $serviceLocator = $this->getApplicationServiceLocator();
    $this->assertTrue($serviceLocator->get('AuthentificationService')->hasIdentity());
  }

  protected function login() {
    $form = new \System\Form\LoginForm();
    $form->prepare();
    $this->dispatch('/authentification/login', 'POST',
            array(
      'email' => 'admin@admin.cz',
      'password' => 'admin@admin.cz',
      'csrf' => $form->get('csrf')->getValue(),
      'submit' => 'Přihlásit',
    ));
  }

  protected function mockLogin() {
    $userSessionModel = new class {

      public $id_user = '1';
      public $name = 'a';
      public $surname = 'bbb';
      public $email = 'admin@admin.cz';
      public $last_login = '2016-07-25 22:07:55';
      public $is_admin = '1';
      public $is_active = '1';
      public $rolesIds = ['2'];
    };

    $authService = $this->createMock('\Zend\Authentication\AuthenticationService');
    $authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($userSessionModel));

    $authService->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

    $this->getApplicationServiceLocator()->setAllowOverride(true);
    $this->getApplicationServiceLocator()->setService('AuthentificationService', $authService);
    $this->getApplicationServiceLocator()->setAllowOverride(false);
  }

  /**
   * @test
   */
  public function indexActionCanBeAccessed() {
    $this->mockLogin();
    $this->dispatch('/user');
    $this->assertResponseStatusCode(200);
  }

  /**
   * @test
   */
  public function addActionCanBeAccessed() {
    $this->mockLogin();
    $this->dispatch('/user/action/add');
    $this->assertResponseStatusCode(200);
  }

  /**
   * @test
   */
  public function editActionCanBeAccessed() {
    $this->mockLogin();
    $this->dispatch('/user/action/edit/id/1');
    $this->assertResponseStatusCode(200);
  }

  /**
   * @test
   */
  public function deleteCanBeAccessed() {
    $this->mockLogin();
    $this->dispatch('/user/action/delete/id/1');
    $this->assertResponseStatusCode(200);
  }

  /**
   * @test
   */
  public function profileActionCanBeAccessed() {
    $this->mockLogin();
    $this->dispatch('/user/action/profile');
    $this->assertResponseStatusCode(200);
  }

}