<?php

namespace System\Form;

use Zend\Form\Form;

class UserSearchForm extends Form {

  public function __construct($name = null, $options = array()) {
    parent::__construct($name, $options);
    $this->setAttribute('method', 'get');

    $this->setWrapElements(true);

    $element = new \Zend\Form\Element\Text('name');
    $element->setLabel('jméno');
    $this->add($element);

    $element = new \Zend\Form\Element\Text('surname');
    $element->setLabel('příjmení');
    $this->add($element);

    $element = new \Zend\Form\Element\Text('email');
    $element->setLabel('email');
    $this->add($element);

    $element = new \Zend\Form\Element\Submit('submit');
    $element->setValue('Hledat');
    $this->add($element);
  }

}
