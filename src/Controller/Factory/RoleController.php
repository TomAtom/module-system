<?php

namespace System\Controller\Factory;

class RoleController implements \Zend\ServiceManager\Factory\FactoryInterface {

  public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
    $rightsForm = $container->get('System\Form\RightsForm');
    $aclCache = $container->get('CacheAcl');
    $entityManager = $container->get('doctrine.entitymanager.orm_default');
    $roleManager = $container->get(\System\Service\RoleManager::class);
    return new \System\Controller\Role($entityManager, $roleManager, $rightsForm, $aclCache);
  }

}
