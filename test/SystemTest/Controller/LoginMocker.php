<?php

namespace SystemTest\Controller;

trait LoginMocker {

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

}