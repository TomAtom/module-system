<?php

namespace System\Controller\Factory;

class UserController implements \Zend\ServiceManager\FactoryInterface, \System\Controller\FactoryInterface {

  private $serviceLocator;

  public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
    $this->serviceLocator = $serviceLocator;
    $roleTable = $serviceLocator->getServiceLocator()->get('System\Model\RoleTable');
    $userRoleTable = $serviceLocator->getServiceLocator()->get('System\Model\UserRoleTable');
    $userTable = $serviceLocator->getServiceLocator()->get('System\Model\UserTable');
    $authService = $serviceLocator->getServiceLocator()->get('AuthentificationService');
    return new \System\Controller\UserController($userTable, $authService, $roleTable, $userRoleTable);
  }

  public static function getCreatedClassName(): string {
    return \System\Controller\UserController::class;
  }

}
