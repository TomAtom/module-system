<?php

namespace System\Form\Factory;

class RightsFormFactory implements \Zend\ServiceManager\Factory\FactoryInterface {

  protected $serviceContainer;

  public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
    $this->serviceContainer = $container;
    $form = new \System\Form\RightsForm();
    $form->setControllers($this->getControllers());
    return $form;
  }

  protected function getControllers(): array {
    $config = $this->serviceContainer->get('Config');
    $controllers = $config['controllers']['invokables'];
    foreach ($config['controllers']['factories'] as $controllerName => $controllerFactoryName) {
      $controllers[$controllerName] = $controllerName;
    }
    return $controllers;
  }

}