<?php

namespace SystemTest\Controller;

class RoleControllerTest extends \Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase {
  
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
  public function indexActionCanBeAccessed() {
    $this->mockLogin();
    $this->dispatch('/role');
    $this->assertResponseStatusCode(200);
  }

}