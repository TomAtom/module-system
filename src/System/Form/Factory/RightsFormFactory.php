<?php

namespace System\Form\Factory;

class RightsFormFactory implements \Zend\ServiceManager\FactoryInterface {
  
  protected $serviceLocator;

  public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
    $this->serviceLocator = $serviceLocator;
    $form = new \System\Form\RightsForm();
    $form->setControllers($this->getControllers());
    return $form;
  }

  protected function getControllers(): array {
    $config = $this->serviceLocator->get('Config');
    $controllers = $config['controllers']['invokables'];
    foreach ($config['controllers']['factories'] as $controllerName => $controllerFactoryName) {
      if (is_subclass_of($controllerFactoryName, '\System\Controller\FactoryInterface')) {
        $controllers[$controllerName] = $controllerFactoryName::getCreatedClassName();
      } else {
        throw new \DomainException('Controller factory must implement \System\Controller\FactoryInterface');
      }
      
    }
    return $controllers;
  }

}
