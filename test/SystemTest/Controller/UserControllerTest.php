<?php

namespace SystemTest\Controller;

class UserControllerTest extends \Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase {
  
  use \SystemTest\Controller\LoginMocker;

  public function setUp() {
    $configOverrides = [];
    $this->setApplicationConfig(\Zend\Stdlib\ArrayUtils::merge(
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