<?php

namespace System\Controller\Factory;

class UserController implements \Zend\ServiceManager\Factory\FactoryInterface {

  public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
    $authService = $container->get('AuthentificationService');
    $entityManager = $container->get('doctrine.entitymanager.orm_default');
    $userManager = $container->get(\System\Service\UserManager::class);
    return new \System\Controller\User($entityManager, $userManager, $authService);
  }

}
