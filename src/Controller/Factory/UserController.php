<?php

namespace System\Controller\Factory;

class UserController implements \Zend\ServiceManager\Factory\FactoryInterface, \System\Controller\FactoryInterface {

  public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
    $roleTable = $container->get('System\Model\RoleTable');
    $userRoleTable = $container->get('System\Model\UserRoleTable');
    $userTable = $container->get('System\Model\UserTable');
    $authService = $container->get('AuthentificationService');
    return new \System\Controller\UserController($userTable, $authService, $roleTable, $userRoleTable);
  }

  public static function getCreatedClassName(): string {
    return \System\Controller\UserController::class;
  }

}
