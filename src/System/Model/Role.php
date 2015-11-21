<?php

namespace System\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Role implements InputFilterAwareInterface, \Zend\Stdlib\ArraySerializableInterface {

  public $id_role;
  public $name;
  protected $inputFilter;

  public function exchangeArray(array $data) {
    $this->id_role = (isset($data['id_role'])) ? $data['id_role'] : null;
    $this->name = (isset($data['name'])) ? $data['name'] : null;
  }

  public function getArrayCopy() {
    return get_object_vars($this);
  }

  public function setInputFilter(InputFilterInterface $inputFilter) {
    throw new \Exception("Not used");
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