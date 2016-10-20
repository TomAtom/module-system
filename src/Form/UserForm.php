<?php

namespace System\Form;

use Zend\Form\Form;

class UserForm extends Form {

  public function __construct($name = null) {
    // we want to ignore the name passed
    parent::__construct('user');
    $this->setAttribute('method', 'post');

    $element = new \Zend\Form\Element\Hidden('id_user');
    $this->add($element);

    $element = new \Zend\Form\Element\Text('name');
    $element->setLabel('jméno');
    $this->add($element);

    $element = new \Zend\Form\Element\Text('surname');
    $element->setLabel('příjmení');
    $this->add($element);

    $element = new \Zend\Form\Element\Text('email');
    $element->setLabel('email');
    $this->add($element);

    $element = new \Zend\Form\Element\Password('password');
    $element->setLabel('heslo');
    $this->add($element);

    $element = new \Zend\Form\Element\Password('password2');
    $element->setLabel('heslo (zopakujte)');
    $this->add($element);

    $element = new \Zend\Form\Element\Checkbox('is_admin');
    $element->setCheckedValue('1');
    $element->setUncheckedValue('0');
    $element->setLabel('administrátor systému');
    $this->add($element);

    $isActive = new \Zend\Form\Element\Checkbox('is_active');
    $isActive->setCheckedValue('1');
    $isActive->setUncheckedValue('0');
    $isActive->setValue('1');
    $isActive->setLabel('může se přihlásit');
    $this->add($isActive);

    $element = new \Zend\Form\Element\Submit('submit');
    $this->add($element);

    $element = new \Zend\Form\Element\Submit('return');
    $element->setValue('Zpět');
    $this->add($element);
  }

}
