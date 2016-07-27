<?php

namespace System\Controller\Factory;

class RoleController implements \Zend\ServiceManager\Factory\FactoryInterface, \System\Controller\FactoryInterface {

  public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
    $roleTable = $container->get('System\Model\RoleTable');
    $rightsForm = $container->get('System\Form\RightsForm');
    $aclCache = $container->get('CacheAcl');
    $rightTable = $rightTable = $container->get('System\Model\RightTable');
    return new \System\Controller\RoleController($roleTable, $rightsForm, $aclCache, $rightTable);
  }

  public static function getCreatedClassName(): string {
    return \System\Controller\RoleController::class;
  }

}
