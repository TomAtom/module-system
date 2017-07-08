<?php

namespace System\Controller\Factory;

class AuthentificationController implements \Zend\ServiceManager\Factory\FactoryInterface {

  public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
    $authService = $container->get('AuthentificationService');
    $userManager = $container->get(\System\Service\UserManager::class);
    return new \System\Controller\Authentification($authService, $userManager);
  }

}
