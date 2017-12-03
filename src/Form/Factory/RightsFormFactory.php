<?php

namespace System\Form\Factory;

class RightsFormFactory implements \Zend\ServiceManager\Factory\FactoryInterface {

  public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
    $form = new \System\Form\RightsForm();
    $form->setControllers($this->getControllers());
    return $form;
  }

  protected function getControllers(\Interop\Container\ContainerInterface $serviceContainer): array {
    $config = $serviceContainer->get('Config');
    $authorizationService = $serviceContainer->get(\System\Service\Authorization::class);
    $controllers = $config['controllers']['invokables'];
    foreach ($config['controllers']['factories'] as $controllerName => $controllerFactoryName) {
      $controllers[$controllerName] = $controllerName;
    }
    return \array_filter($controllers, function (string $controller) use ($authorizationService) {
      return $authorizationService->isControlerUnderAuthorizationControl($controller);
    });
  }

}