<?php

namespace System\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RoleForm extends Form implements InputFilterAwareInterface {

  protected $inputFilter;

  public function __construct($name = null) {
    parent::__construct('role');
    $this->setAttribute('method', 'post');

    $element = new \Zend\Form\Element\Hidden('id_role');
    $this->add($element);

    $element = new \Zend\Form\Element\Text('name');
    $element->setLabel('název');
    $this->add($element);

    $element = new \Zend\Form\Element\Submit('submit');
    $this->add($element);

    $element = new \Zend\Form\Element\Submit('return');
    $element->setValue('Zpět');
    $this->add($element);
  }

  public function getInputFilter() {
    if (!$this->inputFilter) {
      $inputFilter = new InputFilter();
      $factory = new InputFactory();

      $inputFilter->add($factory->createInput(array(
                'name' => 'id_role',
                'required' => true,
                'filters' => array(
                  array('name' => 'Int'),
                ),
      )));

      $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                  array('name' => 'StripTags'),
                  array('name' => 'StringTrim'),
                ),
                'validators' => array(
                  array(
                    'name' => 'StringLength',
                    'options' => array(
                      'encoding' => 'UTF-8',
                      'min' => 1,
                      'max' => 25,
                    ),
                  ),
                ),
      )));

      $this->inputFilter = $inputFilter;
    }

    return $this->inputFilter;
  }

}