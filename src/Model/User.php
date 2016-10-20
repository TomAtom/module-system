<?php

namespace System\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class User implements InputFilterAwareInterface, \Zend\Stdlib\ArraySerializableInterface {
  
  public $id_user;
  public $name;
  public $surname;
  public $email;
  public $last_login;
  public $is_admin;
  public $is_active;
  public $datetime_create;

  protected $inputFilter;

  public function getArrayCopy() {
    return get_object_vars($this);
  }

  public function exchangeArray(array $data) {
    $this->id_user = (isset($data['id_user'])) ? $data['id_user'] : null;
    $this->name = (isset($data['name'])) ? $data['name'] : null;
    $this->surname = (isset($data['surname'])) ? $data['surname'] : null;
    $this->email = (isset($data['email'])) ? $data['email'] : null;
    if (array_key_exists('last_login', $data)) {
      $this->last_login = $data['last_login'];
    }
    if (array_key_exists('is_admin', $data)) {
      $this->is_admin = $data['is_admin'];
    }
    if (array_key_exists('is_active', $data)) {
      $this->is_active = $data['is_active'];
    }
    if (array_key_exists('datetime_create', $data)) {
      $this->datetime_create = $data['datetime_create'];
    }
  }

  public function setInputFilter(InputFilterInterface $inputFilter) {
    throw new \Exception("Not used");
  }

  public function getInputFilter() {
    if (!$this->inputFilter) {
      $inputFilter = new InputFilter();
      $factory = new InputFactory();

      $inputFilter->add($factory->createInput(array(
                'name' => 'id_user',
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
                      'max' => 50,
                    ),
                  ),
                ),
      )));

      $inputFilter->add($factory->createInput(array(
                'name' => 'surname',
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
                      'max' => 50,
                    ),
                  ),
                ),
      )));

      $inputFilter->add($factory->createInput(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                  array('name' => 'StripTags'),
                  array('name' => 'StringTrim'),
                ),
                'validators' => array(
                  array(
                    'name' => 'EmailAddress',
                    'options' => array(
                      'encoding' => 'UTF-8',
                      'min' => 5,
                      'max' => 100,
                    ),
                  ),
                ),
      )));

      $inputFilter->add($factory->createInput(array(
                'name' => 'password',
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
                      'min' => 2,
                    ),
                  ),
                ),
      )));

      $inputFilter->add($factory->createInput(array(
                'name' => 'password2',
                'required' => true,
                'filters' => array(
                  array('name' => 'StripTags'),
                  array('name' => 'StringTrim'),
                ),
                'validators' => array(
                  array(
                    'name' => 'Identical',
                    'options' => array(
                      'token' => 'password'
                    ),
                  ),
                ),
      )));

      $this->inputFilter = $inputFilter;
    }

    return $this->inputFilter;
  }

}