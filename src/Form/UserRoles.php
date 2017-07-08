<?php

namespace System\Form;

use Zend\Form\Form;

class UserRoles extends Form {

  public function __construct($name = null) {
    parent::__construct('rights');
    $this->setAttribute('method', 'post');
  }

  public function setRolesTypes(array $rolesTypes) {
    $fieldSet = new \Zend\Form\Fieldset('roles');
    foreach ($rolesTypes as $roleType) {
      $element = new \Zend\Form\Element\Checkbox($roleType->getIdRole());
      $element->setLabel($roleType->getName());
      $fieldSet->add($element);
    }
    $this->add($fieldSet);
    $element = new \Zend\Form\Element\Submit('save');
    $element->setValue('Uložit');
    $this->add($element);
    $element = new \Zend\Form\Element\Submit('return');
    $element->setValue('Zpět');
    $this->add($element);
  }

  public function setRoles(array $roles) {
    $rolesFieldSet = $this->get('roles');
    foreach ($roles as $role) {
      $element = $rolesFieldSet->get($role->getIdRole());
      if (is_object($element)) {
        $element->setChecked(true);
      }
    }
  }

}