<?php

namespace System\Form;

use Zend\Form\Form;

class RightsForm extends Form {

  public function __construct($name = null) {
    parent::__construct('rights');
    $this->setAttribute('method', 'post');
  }

  public function setControllers(array $controllers) {
    $rightsFieldset = new \Zend\Form\Fieldset('rights');
    foreach ($controllers as $controller => $class) {
      $fieldSet = new \Zend\Form\Fieldset($controller);
      $fieldSet->setLabel($controller);
      foreach ($this->getControllerActions($class) as $checkbox) {
        $element = new \Zend\Form\Element\Checkbox($checkbox);
        $element->setLabel($checkbox);
        $fieldSet->add($element);
      }
      $rightsFieldset->add($fieldSet);
    }
    $this->add($rightsFieldset);
    $element = new \Zend\Form\Element\Submit('save');
    $element->setValue('Uložit');
    $this->add($element);
    $element = new \Zend\Form\Element\Submit('return');
    $element->setValue('Zpět');
    $this->add($element);
  }

  public function setRights(\Iterator $rights) {
    foreach ($rights as $right) {
      $controllerContainer = $this->get('rights')->get($right->controller);
      if (is_object($controllerContainer) && $controllerContainer->has($right->action)) {
          $element = $controllerContainer->get($right->action);
          if (is_object($element)) {
            $element->setChecked(true);
          }
        }
      }
    }

  protected function getControllerActions($controllerClass): array {
    $methods = get_class_methods($controllerClass);
    $exclude = array('notFoundAction', 'getMethodFromAction');
    $actions = array();
    foreach ($methods as $method) {
      if (preg_match("/Action$/", $method) && !in_array($method, $exclude)) {
        $method = preg_replace("/Action$/", '', $method);
        $actions[] = $method;
      }
    }
    return $actions;
  }

  protected function getControllerActionDescription($controllerClass, $action) {
    $controller = new \ReflectionClass($controllerClass);
    $method = $controller->getMethod($action . 'Action');
    preg_match_all('#@description(.*?)\n#s', $method->getDocComment(), $annotations);
    var_dump($annotations);
  }

}