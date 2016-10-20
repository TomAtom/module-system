<?php

namespace System\Controller\Factory;

class AuthentificationController implements \Zend\ServiceManager\Factory\FactoryInterface, \System\Controller\FactoryInterface {

  public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
    $authService = $container->get('AuthentificationService');
    $userRoleTable = $container->get('System\Model\UserRoleTable');
    $userTable = $container->get('System\Model\UserTable');
    return new \System\Controller\AuthentificationController($authService, $userRoleTable, $userTable);
  }

  public static function getCreatedClassName(): string {
    return \System\Controller\AuthentificationController::class;
  }

}