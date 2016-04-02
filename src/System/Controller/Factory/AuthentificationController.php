<?php

namespace System\Controller\Factory;

class AuthentificationController implements \Zend\ServiceManager\FactoryInterface, \System\Controller\FactoryInterface {

  public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
    $authService = $serviceLocator->getServiceLocator()->get('AuthentificationService');
    $userRoleTable = $serviceLocator->getServiceLocator()->get('System\Model\UserRoleTable');
    $userTable = $serviceLocator->getServiceLocator()->get('System\Model\UserTable');
    return new \System\Controller\AuthentificationController($authService, $userRoleTable, $userTable);
  }

  public static function getCreatedClassName(): string {
    return \System\Controller\AuthentificationController::class;
  }

}
