<?php

namespace System\Controller\Factory;

class RoleController implements \Zend\ServiceManager\FactoryInterface, \System\Controller\FactoryInterface {

  private $serviceLocator;

  public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
    $this->serviceLocator = $serviceLocator;
    $roleTable = $serviceLocator->getServiceLocator()->get('System\Model\RoleTable');
    $rightsForm = $serviceLocator->getServiceLocator()->get('System\Form\RightsForm');
    $aclCache = $serviceLocator->getServiceLocator()->get('CacheAcl');
    $rightTable = $rightTable = $serviceLocator->getServiceLocator()->get('System\Model\RightTable');
    return new \System\Controller\RoleController($roleTable, $rightsForm, $aclCache, $rightTable);
  }

  public static function getCreatedClassName(): string {
    return \System\Controller\RoleController::class;
  }

}
