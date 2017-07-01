<?php

namespace System\Controller\Factory;

class AuthentificationController implements \Zend\ServiceManager\Factory\FactoryInterface {

  public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
    $authService = $container->get('AuthentificationService');
    $userRoleTable = $container->get('System\Model\UserRoleTable');
    $userTable = $container->get('System\Model\UserTable');
    return new \System\Controller\Authentification($authService, $userRoleTable, $userTable);
  }

}
